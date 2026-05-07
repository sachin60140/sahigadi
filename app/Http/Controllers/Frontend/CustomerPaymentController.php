<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\RazorpayService;
use App\Services\PhonePeService;
use Exception;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService,
        protected PhonePeService $phonepeService
    ) {}

    public function checkout(Request $request)
    {
        $type = 'wallet_recharge';
        $amount = 0;
        
        if ($request->has('recharge_amount')) {
            $minRechargeAmount = Setting::getCustomerMinimumWalletRechargeAmount();
            $rechargeAmount = max($minRechargeAmount, (float) $request->recharge_amount);
            $amount = round($rechargeAmount * 1.18, 2);
        } else {
            return redirect()->back()->with('error', 'Invalid recharge amount.');
        }

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

        $typeLabel = 'Wallet Recharge';

        // Reuse the dealer checkout view or create customer checkout view?
        // Dealer checkout view just renders Razorpay script and PhonePe button.
        // It POSTs to route('payments.success') etc. which we need to change.
        // Let's pass routes so the view can use them, OR we duplicate checkout.blade.php.
        // We will duplicate checkout.blade.php for customer.
        return view('frontend.customer.payments.checkout', [
            'order' => $orderData,
            'type' => $type,
            'amount' => $amount,
            'typeLabel' => $typeLabel,
            'keyId' => $this->razorpayService->getKeyId(),
            'isRazorpayActive' => $isRazorpayActive,
            'isPhonePeActive' => $isPhonePeActive,
        ]);
    }

    public function success(Request $request)
    {
        try {
            $customer = auth('customer')->user();

            $this->razorpayService->processPayment(
                $customer,
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                $request->amount / 100,
                $request->type,
                $request->reference_id ?? null
            );

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
            $customer = auth('customer')->user();
            $type = 'wallet_recharge';
            $amount = $request->amount;
            $transactionId = 'PPC_' . time() . '_' . $customer->id;

            session()->put('customer_phonepe_payment_info', [
                'transaction_id' => $transactionId,
                'type' => $type,
                'amount' => $amount,
            ]);

            $response = $this->phonepeService->createPayment($amount, $transactionId, [
                'customer_id' => $customer->id,
                'redirectUrl' => route('customer.payments.phonepe.callback')
            ]);

            if ($response['success']) {
                return redirect()->away($response['redirect_url']);
            }

            return redirect()->route('customer.wallet.index')->with('error', 'PhonePe payment initiation failed.');
        } catch (Exception $e) {
            return redirect()->route('customer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function phonepeCallback(Request $request)
    {
        try {
            $customer = auth('customer')->user();
            $paymentInfo = session()->get('customer_phonepe_payment_info');

            if (!$paymentInfo) {
                throw new Exception('Payment information lost in session.');
            }

            $transactionId = $paymentInfo['transaction_id'];

            $this->phonepeService->processPayment(
                $customer,
                $transactionId,
                $paymentInfo['amount'],
                $paymentInfo['type']
            );

            session()->forget('customer_phonepe_payment_info');
            return redirect()->route('customer.wallet.index')->with('success', 'PhonePe Payment successful! Amount credited.');
        } catch (Exception $e) {
            return redirect()->route('customer.wallet.index')->with('error', $e->getMessage());
        }
    }
}
