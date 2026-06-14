<?php

namespace App\Http\Controllers;

use App\Models\Car as CarModel;
use App\Models\Plan;
use App\Models\Setting;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use App\Services\PhonePeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService,
        protected PhonePeService $phonepeService,
        protected WalletService $walletService,
        protected SubscriptionService $subscriptionService
    ) {}

    public function checkout(Request $request)
    {
        $request->validate([
            'type' => 'required|in:wallet_recharge,plan_purchase,featured_listing',
            'recharge_amount' => 'required_if:type,wallet_recharge|numeric|min:0',
            'plan_id' => 'required_if:type,plan_purchase|integer',
            'car_id' => 'required_if:type,featured_listing|integer',
            'days' => 'required_if:type,featured_listing|integer|in:7,14,30',
        ]);

        $type = $request->string('type')->toString();
        $dealer = auth('dealer')->user();
        $amount = 0.0;
        $planId = null;
        $carId = null;
        $days = null;

        if ($type === 'wallet_recharge') {
            $minRechargeAmount = Setting::getMinimumWalletRechargeAmount();
            $rechargeAmount = (float) $request->input('recharge_amount', $minRechargeAmount);

            if ($rechargeAmount < $minRechargeAmount) {
                throw ValidationException::withMessages([
                    'recharge_amount' => "The minimum recharge amount is Rs {$minRechargeAmount}.",
                ]);
            }

            $amount = round($rechargeAmount * 1.18, 2);
        } elseif ($type === 'plan_purchase') {
            $plan = Plan::active()->findOrFail($request->integer('plan_id'));
            $planId = $plan->id;
            $amount = (float) $plan->price;
        } else {
            $car = CarModel::query()
                ->whereKey($request->integer('car_id'))
                ->where('dealer_id', $dealer->id)
                ->firstOrFail();

            $carId = $car->id;
            $days = $request->integer('days');
            $amount = match ($days) {
                7 => 99.0,
                14 => 179.0,
                30 => 299.0,
            };
        }

        $balance = $this->walletService->getBalance($dealer->id);

        if ($type === 'plan_purchase' || $type === 'featured_listing') {
            if ($balance >= $amount) {
                return $this->processFromWallet($request, $dealer, $amount);
            }
        }

        $isRazorpayActive = Setting::isRazorpayActive();
        $isPhonePeActive = Setting::isPhonePeActive();

        if (!$isRazorpayActive && !$isPhonePeActive) {
            return redirect()->back()->with('error', 'No payment gateway available.');
        }

        $orderData = null;
        if ($isRazorpayActive) {
            $receipt = $type.'_'.time();
            $orderData = $this->razorpayService->createOrder($amount, $receipt, [
                'dealer_id' => $dealer->id,
                'type' => $type,
            ]);
        }

        $paymentInfo = [
            'dealer_id' => $dealer->id,
            'type' => $type,
            'amount' => (float) $amount,
            'plan_id' => $planId,
            'car_id' => $carId,
            'days' => $days,
        ];
        $paymentIntent = (string) Str::uuid();
        session()->put("dealer_payment_intents.{$paymentIntent}", $paymentInfo);

        if ($orderData) {
            session()->put("dealer_razorpay_orders.{$orderData['order_id']}", $paymentInfo);
        }

        $typeLabel = match ($type) {
            'wallet_recharge' => 'Wallet Recharge',
            'plan_purchase' => 'Plan Purchase',
            'featured_listing' => 'Featured Listing',
            default => 'Payment',
        };

        return Inertia::render('Dealer/Payments/Checkout', [
            'order' => $orderData,
            'type' => $type,
            'amount' => (float) $amount,
            'typeLabel' => $typeLabel,
            'keyId' => $this->razorpayService->getKeyId(),
            'planId' => $planId,
            'carId' => $carId,
            'days' => $days,
            'isRazorpayActive' => $isRazorpayActive,
            'isPhonePeActive' => $isPhonePeActive,
            'paymentIntent' => $paymentIntent,
            'dealer' => [
                'name' => $dealer->name,
                'email' => $dealer->email,
            ],
            'csrfToken' => csrf_token(),
            'actions' => [
                'success' => route('dealer.payments.success'),
                'failed' => route('dealer.payments.failed'),
                'phonepe' => route('dealer.payments.phonepe.initiate'),
                'wallet' => route('dealer.wallet.index'),
            ],
        ]);
    }

    protected function processFromWallet(Request $request, $dealer, float $amount)
    {
        try {
            $type = $request->type;
            $referenceId = $type.'_wallet_'.time();

            $this->walletService->debit($dealer->id, $amount, 'Payment via wallet - '.$type, $referenceId, $type);

            if ($type === 'plan_purchase' && $request->plan_id) {
                $plan = Plan::find($request->plan_id);
                if ($plan) {
                    $this->subscriptionService->purchasePlan($dealer, $plan);
                }

                return redirect()->route('dealer.plans.index')->with('success', 'Plan purchased successfully using wallet balance!');
            }

            if ($type === 'featured_listing' && $request->car_id) {
                $car = CarModel::find($request->car_id);
                if ($car) {
                    $days = $request->days ?? 7;
                    $car->update([
                        'is_featured' => true,
                        'featured_expires_at' => now()->addDays($days),
                    ]);
                }

                return redirect()->route('dealer.cars.index')->with('success', 'Car marked as featured!');
            }

            return redirect()->route('dealer.wallet.index')->with('success', 'Payment successful!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required|string',
                'razorpay_payment_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            $dealer = auth('dealer')->user();
            $sessionKey = "dealer_razorpay_orders.{$request->razorpay_order_id}";
            $paymentInfo = session()->get($sessionKey);

            if (! $paymentInfo || (int) $paymentInfo['dealer_id'] !== (int) $dealer->id) {
                throw new Exception('This payment session is invalid or has expired.');
            }

            $payment = $this->razorpayService->processPayment(
                $dealer,
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                (float) $paymentInfo['amount'],
                $paymentInfo['type'],
                $request->reference_id ?? null
            );

            if ($paymentInfo['type'] === 'plan_purchase' && $paymentInfo['plan_id']) {
                $plan = Plan::active()->find($paymentInfo['plan_id']);
                if ($plan) {
                    $this->subscriptionService->purchasePlan($dealer, $plan);
                }
            }

            if ($paymentInfo['type'] === 'featured_listing' && $paymentInfo['car_id']) {
                $car = CarModel::query()
                    ->whereKey($paymentInfo['car_id'])
                    ->where('dealer_id', $dealer->id)
                    ->first();

                if ($car) {
                    $days = $paymentInfo['days'] ?? 7;
                    $car->update([
                        'is_featured' => true,
                        'featured_expires_at' => now()->addDays($days),
                    ]);
                }
            }

            session()->forget($sessionKey);

            $redirectRoute = match ($paymentInfo['type']) {
                'plan_purchase' => 'dealer.plans.index',
                'featured_listing' => 'dealer.cars.index',
                default => 'dealer.wallet.index',
            };

            $message = $paymentInfo['type'] === 'wallet_recharge'
                ? 'Payment successful! Your wallet has been credited.'
                : 'Payment successful!';

            return redirect()->route($redirectRoute)->with('success', $message);
        } catch (Exception $e) {
            return redirect()->route('dealer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function failed(Request $request)
    {
        return redirect()->route('dealer.wallet.index')->with('error', 'Payment failed. Please try again.');
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

            $dealer = auth('dealer')->user();
            $intentKey = "dealer_payment_intents.{$request->intent}";
            $paymentInfo = session()->get($intentKey);

            if (! $paymentInfo || (int) $paymentInfo['dealer_id'] !== (int) $dealer->id) {
                throw new Exception('This payment session is invalid or has expired.');
            }

            $type = $paymentInfo['type'];
            $amount = (float) $paymentInfo['amount'];
            $transactionId = 'PP_'.$dealer->id.'_'.Str::ulid();
            $callbackUrl = route('dealer.payments.phonepe.callback', [
                'merchant_order_id' => $transactionId,
            ]);

            session()->put("dealer_phonepe_payments.{$transactionId}", [
                'transaction_id' => $transactionId,
                'type' => $type,
                'amount' => $amount,
                'plan_id' => $paymentInfo['plan_id'],
                'car_id' => $paymentInfo['car_id'],
                'days' => $paymentInfo['days'],
            ]);

            $response = $this->phonepeService->createPayment($amount, $transactionId, [
                'dealer_id' => $dealer->id,
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
            \Illuminate\Support\Facades\Log::error('Dealer PhonePe payment initiation failed', [
                'dealer_id' => auth('dealer')->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->route('dealer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function phonepeCallback(Request $request)
    {
        try {
            $dealer = auth('dealer')->user();
            $transactionId = (string) $request->query('merchant_order_id', '');
            $paymentInfo = $transactionId !== ''
                ? session()->get("dealer_phonepe_payments.{$transactionId}")
                : null;

            if (!$paymentInfo) {
                throw new Exception('Payment information lost in session.');
            }

            if (! hash_equals((string) $paymentInfo['transaction_id'], $transactionId)) {
                throw new Exception('PhonePe payment session does not match this order.');
            }

            $this->phonepeService->processPayment(
                $dealer,
                $transactionId,
                $paymentInfo['amount'],
                $paymentInfo['type']
            );

                if ($paymentInfo['type'] === 'plan_purchase' && $paymentInfo['plan_id']) {
                    $plan = Plan::find($paymentInfo['plan_id']);
                    if ($plan) {
                        $this->subscriptionService->purchasePlan($dealer, $plan);
                    }
                }

                if ($paymentInfo['type'] === 'featured_listing' && $paymentInfo['car_id']) {
                    $car = CarModel::query()
                        ->whereKey($paymentInfo['car_id'])
                        ->where('dealer_id', $dealer->id)
                        ->first();
                    if ($car) {
                        $days = $paymentInfo['days'] ?? 7;
                        $car->update([
                            'is_featured' => true,
                            'featured_expires_at' => now()->addDays($days),
                        ]);
                    }
                }

            session()->forget("dealer_phonepe_payments.{$transactionId}");
            return redirect()->route('dealer.wallet.index')->with('success', 'PhonePe Payment successful! Amount credited.');
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dealer PhonePe callback failed', [
                'dealer_id' => auth('dealer')->id(),
                'merchant_order_id' => $request->query('merchant_order_id'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dealer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function phonepeWebhook(Request $request)
    {
        $expectedUser = (string) config('services.phonepe.webhook_user', '');
        $expectedPass = (string) config('services.phonepe.webhook_pass', '');

        if ($expectedUser === '' || $expectedPass === '') {
            \Illuminate\Support\Facades\Log::error('PhonePe webhook credentials are not configured.');

            return response()->json(['error' => 'Webhook is not configured'], 503);
        }

        $authHeader = trim((string) $request->header('Authorization', ''));
        $expectedHeader = 'SHA256('.hash('sha256', $expectedUser.':'.$expectedPass).')';

        if (! hash_equals($expectedHeader, $authHeader)) {
            \Illuminate\Support\Facades\Log::warning('PhonePe webhook authentication failed.');

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        \Illuminate\Support\Facades\Log::info('PhonePe Webhook Received', ['data' => $data]);

        if (($data['event'] ?? null) !== 'checkout.order.completed') {
            return response()->json(['success' => true]);
        }

        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];
        $transactionId = $payload['merchantOrderId'] ?? null;
        $state = $payload['state'] ?? null;
        $amount = (int) ($payload['amount'] ?? 0);

        if ($transactionId && $state === 'COMPLETED') {
                if (str_starts_with($transactionId, 'PL_')) {
                    $paymentLink = \App\Models\PaymentLink::where('transaction_id', $transactionId)->first();
                    
                    if ($paymentLink && $paymentLink->status === 'pending') {
                        $dealer = $paymentLink->dealer;
                        if ($dealer) {
                            try {
                                $payment = $this->phonepeService->processPayment(
                                    $dealer,
                                    $transactionId,
                                    (float) $paymentLink->amount,
                                    'custom_payment_link'
                                );
                                
                                \Illuminate\Support\Facades\DB::transaction(function() use ($paymentLink, $payment) {
                                    // Re-check status inside transaction
                                    $freshLink = \App\Models\PaymentLink::where('id', $paymentLink->id)->lockForUpdate()->first();
                                    if ($freshLink && $freshLink->status === 'pending') {
                                        $freshLink->update([
                                            'status' => 'paid',
                                            'transaction_id' => $payment->id
                                        ]);
                                    }
                                });
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('PhonePe Webhook Link Process Error', ['error' => $e->getMessage()]);
                            }
                        }
                    }
                } elseif (str_starts_with($transactionId, 'PPC_')) {
                    $parts = explode('_', $transactionId);
                    $customerId = $parts[1] ?? null;
                    $customer = \App\Models\Customer::find($customerId);
                    
                    if ($customer) {
                        try {
                            $this->phonepeService->processPayment(
                                $customer, 
                                $transactionId, 
                                (float) ($amount / 100), 
                                'wallet_recharge'
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('PhonePe Webhook Process Error for Customer', ['error' => $e->getMessage()]);
                        }
                    }
                } elseif (str_starts_with($transactionId, 'PP_')) {
                    $parts = explode('_', $transactionId);
                    $dealerId = $parts[1] ?? null;
                    $dealer = \App\Models\Dealer::find($dealerId);
                    
                    if ($dealer) {
                        try {
                            $this->phonepeService->processPayment(
                                $dealer, 
                                $transactionId, 
                                (float) ($amount / 100), 
                                'wallet_recharge'
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('PhonePe Webhook Process Error', ['error' => $e->getMessage()]);
                        }
                    }
                }
            }

        return response()->json(['success' => true]);
    }
}
