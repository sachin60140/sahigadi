<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Inertia\Inertia;

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

        return Inertia::render('Dealer/Plans/Index', [
            'plans' => $plans->map(fn (Plan $plan) => $this->mapPlan($plan, $currentPlan)),
            'currentPlan' => $currentPlan ? [
                'name' => $currentPlan->plan->name,
                'active_listings' => $currentPlan->getActiveListingsCount(),
                'listing_limit' => $currentPlan->plan->listing_limit,
                'expires_at' => optional($currentPlan->expires_at)->format('d M Y'),
            ] : null,
        ]);
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

        return Inertia::render('Dealer/Plans/Show', [
            'plan' => $this->mapPlan($plan, $currentPlan),
            'balance' => $balance,
            'actions' => [
                'index' => route('dealer.plans.index'),
                'purchase' => route('dealer.plans.purchase', $plan),
                'wallet' => route('dealer.wallet.add'),
            ],
        ]);
    }

    private function mapPlan(Plan $plan, $currentPlan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'listing_limit' => $plan->listing_limit,
            'duration_days' => $plan->duration_days,
            'description' => $plan->description,
            'is_current' => (bool) ($currentPlan && $currentPlan->plan_id === $plan->id),
            'show_url' => route('dealer.plans.show', $plan),
        ];
    }
}
