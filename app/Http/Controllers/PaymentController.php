<?php

namespace App\Http\Controllers;

use App\Models\Car as CarModel;
use App\Models\Plan;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService,
        protected WalletService $walletService,
        protected SubscriptionService $subscriptionService
    ) {}

    public function checkout(Request $request)
    {
        $type = $request->type;
        $amount = $request->amount;
        
        if ($type === 'wallet_recharge' && $request->has('recharge_amount')) {
            $rechargeAmount = max(1000, (float) $request->recharge_amount);
            $amount = round($rechargeAmount * 1.18, 2);
        }

        $dealer = auth('dealer')->user();
        $balance = $this->walletService->getBalance($dealer->id);

        if ($type === 'plan_purchase' || $type === 'featured_listing') {
            if ($balance >= $amount) {
                return $this->processFromWallet($request, $dealer, $amount);
            }
        }

        $receipt = $type.'_'.time();

        $orderData = $this->razorpayService->createOrder($amount, $receipt, [
            'dealer_id' => $dealer->id,
            'type' => $type,
        ]);

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
}
