<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\SubscriptionService;
use App\Services\WalletService;

class PlanController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected WalletService $walletService
    ) {}

    public function index()
    {
        $plans = Plan::active()->orderBy('price')->get();
        $dealer = auth('dealer')->user();
        $currentPlan = $dealer->activeSubscription();

        return view('dealer.plans.index', compact('plans', 'currentPlan'));
    }

    public function purchase(Plan $plan)
    {
        $dealer = auth('dealer')->user();

        if ($plan->price == 0) {
            $this->subscriptionService->purchasePlan($dealer, $plan);

            return redirect()->route('dealer.plans.index')->with('success', 'Free plan activated successfully!');
        }

        $balance = $this->walletService->getBalance($dealer->id);

        if ($balance < $plan->price) {
            return back()->with('error', 'Insufficient wallet balance. Please add funds.');
        }

        return redirect()->route('dealer.payments.checkout', [
            'type' => 'plan_purchase',
            'plan_id' => $plan->id,
            'amount' => $plan->price,
        ]);
    }

    public function show(Plan $plan)
    {
        $dealer = auth('dealer')->user();
        $balance = $this->walletService->getBalance($dealer->id);
        $currentPlan = $dealer->activeSubscription();

        return view('dealer.plans.show', compact('plan', 'dealer', 'balance', 'currentPlan'));
    }
}
