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
        $type = $request->type;
        $amount = $request->amount;
        
        if ($type === 'wallet_recharge' && $request->has('recharge_amount')) {
            $minRechargeAmount = Setting::getMinimumWalletRechargeAmount();
            $rechargeAmount = max($minRechargeAmount, (float) $request->recharge_amount);
            $amount = round($rechargeAmount * 1.18, 2);
        }

        $dealer = auth('dealer')->user();
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

        $typeLabel = match ($type) {
            'wallet_recharge' => 'Wallet Recharge',
            'plan_purchase' => 'Plan Purchase',
            'featured_listing' => 'Featured Listing',
            default => 'Payment',
        };

        return view('dealer.payments.checkout', [
            'order' => $orderData,
            'type' => $type,
            'amount' => $amount,
            'typeLabel' => $typeLabel,
            'keyId' => $this->razorpayService->getKeyId(),
            'planId' => $request->plan_id,
            'carId' => $request->car_id,
            'days' => $request->days,
            'isRazorpayActive' => $isRazorpayActive,
            'isPhonePeActive' => $isPhonePeActive,
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
            $dealer = auth('dealer')->user();

            $payment = $this->razorpayService->processPayment(
                $dealer,
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                $request->amount / 100,
                $request->type,
                $request->reference_id ?? null
            );

            if ($request->type === 'plan_purchase' && $request->plan_id) {
                $plan = Plan::find($request->plan_id);
                if ($plan) {
                    $this->subscriptionService->purchasePlan($dealer, $plan);
                }
            }

            if ($request->type === 'featured_listing' && $request->car_id) {
                $car = CarModel::find($request->car_id);
                if ($car) {
                    $days = $request->days ?? 7;
                    $car->update([
                        'is_featured' => true,
                        'featured_expires_at' => now()->addDays($days),
                    ]);
                }
            }

            return redirect()->route('dealer.wallet.index')->with('success', 'Payment successful! Amount credited to wallet.');
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
            $dealer = auth('dealer')->user();
            $type = $request->type;
            $amount = $request->amount;
            $transactionId = 'PP_' . time() . '_' . $dealer->id;

            // Store temporary info in session for callback
            session()->put('phonepe_payment_info', [
                'transaction_id' => $transactionId,
                'type' => $type,
                'amount' => $amount,
                'plan_id' => $request->plan_id,
                'car_id' => $request->car_id,
                'days' => $request->days,
            ]);

            $response = $this->phonepeService->createPayment($amount, $transactionId, ['dealer_id' => $dealer->id]);

            if ($response['success']) {
                return redirect()->away($response['redirect_url']);
            }

            return redirect()->route('dealer.wallet.index')->with('error', 'PhonePe payment initiation failed.');
        } catch (Exception $e) {
            return redirect()->route('dealer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function phonepeCallback(Request $request)
    {
        try {
            $dealer = auth('dealer')->user();
            $paymentInfo = session()->get('phonepe_payment_info');

            if (!$paymentInfo) {
                throw new Exception('Payment information lost in session.');
            }

            $transactionId = $paymentInfo['transaction_id'];

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
                    $car = CarModel::find($paymentInfo['car_id']);
                    if ($car) {
                        $days = $paymentInfo['days'] ?? 7;
                        $car->update([
                            'is_featured' => true,
                            'featured_expires_at' => now()->addDays($days),
                        ]);
                    }
                }

            session()->forget('phonepe_payment_info');
            return redirect()->route('dealer.wallet.index')->with('success', 'PhonePe Payment successful! Amount credited.');
        } catch (Exception $e) {
            return redirect()->route('dealer.wallet.index')->with('error', $e->getMessage());
        }
    }

    public function phonepeWebhook(Request $request)
    {
        $expectedUser = env('PHONEPE_WEBHOOK_USER', 'sahigadiwebhook');
        $expectedPass = env('PHONEPE_WEBHOOK_PASS', 'Sahigadi12345');

        $authHeader = $request->header('Authorization');
        $expectedSha256 = hash('sha256', $expectedUser . ':' . $expectedPass);
        
        $isBasicAuth = ($request->getUser() === $expectedUser && $request->getPassword() === $expectedPass);
        $isSha256 = (str_replace(' ', '', $authHeader) === 'SHA256(' . $expectedSha256 . ')' || $authHeader === $expectedSha256);

        if (!$isBasicAuth && !$isSha256) {
            \Illuminate\Support\Facades\Log::warning('PhonePe Webhook Auth Failed', ['header' => $authHeader]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // V2 payload is raw JSON
        $data = $request->all();
        \Illuminate\Support\Facades\Log::info('PhonePe Webhook Received', ['data' => $data]);

        if (isset($data['event']) && $data['event'] === 'checkout.order.completed') {
            $payload = $data['payload'] ?? [];
            $transactionId = $payload['merchantOrderId'] ?? null;
            $state = $payload['state'] ?? null;

            if ($transactionId && $state === 'COMPLETED') {
                if (str_starts_with($transactionId, 'PP_LNK_')) {
                    $parts = explode('_', $transactionId);
                    $linkId = end($parts);
                    $paymentLink = \App\Models\PaymentLink::find($linkId);
                    
                    if ($paymentLink && $paymentLink->status === 'pending') {
                        $dealer = $paymentLink->dealer;
                        if ($dealer) {
                            try {
                                $payment = $this->phonepeService->processPayment(
                                    $dealer,
                                    $transactionId,
                                    (float) (($payload['amount'] ?? 0) / 100),
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
                } else {
                    $parts = explode('_', $transactionId);
                    $dealerId = end($parts);
                    $dealer = \App\Models\Dealer::find($dealerId);
                    
                    if ($dealer) {
                        try {
                            $this->phonepeService->processPayment(
                                $dealer, 
                                $transactionId, 
                                (float) (($payload['amount'] ?? 0) / 100), 
                                'wallet_recharge'
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('PhonePe Webhook Process Error', ['error' => $e->getMessage()]);
                        }
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
