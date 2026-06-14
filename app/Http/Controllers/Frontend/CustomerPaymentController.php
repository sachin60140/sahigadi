<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\RazorpayService;
use App\Services\PhonePeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CustomerPaymentController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService,
        protected PhonePeService $phonepeService
    ) {}

    public function checkout(Request $request)
    {
        $request->validate([
            'recharge_amount' => 'required|numeric|min:0',
        ]);

        $type = 'wallet_recharge';
        $minRechargeAmount = Setting::getCustomerMinimumWalletRechargeAmount();
        $rechargeAmount = (float) $request->recharge_amount;

        if ($rechargeAmount < $minRechargeAmount) {
            throw ValidationException::withMessages([
                'recharge_amount' => "The minimum recharge amount is Rs {$minRechargeAmount}.",
            ]);
        }

        $amount = round($rechargeAmount * 1.18, 2);
        $customer = auth('customer')->user();

        $isRazorpayActive = Setting::isRazorpayActive();
        $isPhonePeActive = Setting::isPhonePeActive();

        if (!$isRazorpayActive && !$isPhonePeActive) {
            return redirect()->back()->with('error', 'No payment gateway available.');
        }

        $orderData = null;
        if ($isRazorpayActive) {
            $receipt = $type.'_'.time();
            $orderData = $this->razorpayService->createOrder($amount, $receipt, [
                'customer_id' => $customer->id,
                'type' => $type,
            ]);
        }

        $paymentInfo = [
            'customer_id' => $customer->id,
            'type' => $type,
            'amount' => (float) $amount,
        ];
        $paymentIntent = (string) Str::uuid();
        session()->put("customer_payment_intents.{$paymentIntent}", $paymentInfo);

        if ($orderData) {
            session()->put("customer_razorpay_orders.{$orderData['order_id']}", $paymentInfo);
        }

        return Inertia::render('Customer/Payments/Checkout', [
            'order' => $orderData,
            'type' => $type,
            'amount' => (float) $amount,
            'baseAmount' => $rechargeAmount,
            'typeLabel' => 'Wallet Recharge',
            'keyId' => $this->razorpayService->getKeyId(),
            'isRazorpayActive' => $isRazorpayActive,
            'isPhonePeActive' => $isPhonePeActive,
            'paymentIntent' => $paymentIntent,
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'csrfToken' => csrf_token(),
            'actions' => [
                'success' => route('customer.payments.success'),
                'phonepe' => route('customer.payments.phonepe.initiate'),
                'wallet' => route('customer.wallet.index'),
            ],
        ]);
    }

    public function success(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required|string',
                'razorpay_payment_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            $customer = auth('customer')->user();
            $sessionKey = "customer_razorpay_orders.{$request->razorpay_order_id}";
            $paymentInfo = session()->get($sessionKey);

            if (! $paymentInfo || (int) $paymentInfo['customer_id'] !== (int) $customer->id) {
                throw new Exception('This payment session is invalid or has expired.');
            }

            $this->razorpayService->processPayment(
                $customer,
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                (float) $paymentInfo['amount'],
                $paymentInfo['type'],
                $request->reference_id ?? null
            );

            session()->forget($sessionKey);

            return redirect()->route('customer.wallet.index')->with('success', 'Payment successful! Amount credited to wallet.');
        } catch (Exception $e) {
            return redirect()->route('customer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function failed(Request $request)
    {
        return redirect()->route('customer.wallet.index')->with('error', 'Payment failed. Please try again.');
    }

    public function phonepeInitiate(Request $request)
    {
        try {
            if (! Setting::isPhonePeActive()) {
                throw new Exception('PhonePe is currently unavailable.');
            }

            $request->validate([
                'intent' => 'required|uuid',
            ]);

            $customer = auth('customer')->user();
            $intentKey = "customer_payment_intents.{$request->intent}";
            $paymentInfo = session()->get($intentKey);

            if (! $paymentInfo || (int) $paymentInfo['customer_id'] !== (int) $customer->id) {
                throw new Exception('This payment session is invalid or has expired.');
            }

            $type = $paymentInfo['type'];
            $amount = (float) $paymentInfo['amount'];
            $transactionId = 'PPC_'.$customer->id.'_'.Str::ulid();
            $callbackUrl = route('customer.payments.phonepe.callback', [
                'merchant_order_id' => $transactionId,
            ]);

            session()->put("customer_phonepe_payments.{$transactionId}", [
                'transaction_id' => $transactionId,
                'type' => $type,
                'amount' => $amount,
            ]);

            $response = $this->phonepeService->createPayment($amount, $transactionId, [
                'customer_id' => $customer->id,
                'redirectUrl' => $callbackUrl,
            ]);

            if ($response['success']) {
                session()->forget($intentKey);

                if ($request->expectsJson()) {
                    return response()->json([
                        'checkout_url' => $response['redirect_url'],
                        'merchant_order_id' => $transactionId,
                    ]);
                }

                return redirect()->away($response['redirect_url']);
            }

            throw new Exception('PhonePe payment initiation failed.');
        } catch (Exception $e) {
            Log::error('Customer PhonePe payment initiation failed', [
                'customer_id' => auth('customer')->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->route('customer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function phonepeCallback(Request $request)
    {
        try {
            $customer = auth('customer')->user();
            $transactionId = (string) $request->query('merchant_order_id', '');
            $paymentInfo = $transactionId !== ''
                ? session()->get("customer_phonepe_payments.{$transactionId}")
                : null;

            if (!$paymentInfo) {
                throw new Exception('Payment information lost in session.');
            }

            if (! hash_equals((string) $paymentInfo['transaction_id'], $transactionId)) {
                throw new Exception('PhonePe payment session does not match this order.');
            }

            $this->phonepeService->processPayment(
                $customer,
                $transactionId,
                $paymentInfo['amount'],
                $paymentInfo['type']
            );

            session()->forget("customer_phonepe_payments.{$transactionId}");
            return redirect()->route('customer.wallet.index')->with('success', 'PhonePe Payment successful! Amount credited.');
        } catch (Exception $e) {
            Log::error('Customer PhonePe callback failed', [
                'customer_id' => auth('customer')->id(),
                'merchant_order_id' => $request->query('merchant_order_id'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('customer.wallet.index')->with('error', $e->getMessage());
        }
    }
}
