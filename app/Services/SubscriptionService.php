<?php

namespace App\Services;

use App\Models\Dealer;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function purchasePlan(Dealer $dealer, Plan $plan): Subscription
    {
        return DB::transaction(function () use ($dealer, $plan) {
            $existingSubscription = $dealer->activeSubscription();
            if ($existingSubscription) {
                $existingSubscription->update(['is_active' => false]);
            }

            $subscription = Subscription::create([
                'dealer_id' => $dealer->id,
                'plan_id' => $plan->id,
                'listings_used' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays($plan->duration_days),
                'is_active' => true,
            ]);

            return $subscription;
        });
    }

    public function canAddListing(Dealer $dealer): bool
    {
        $subscription = $dealer->activeSubscription();

        if (! $subscription) {
            return false;
        }

        $activeCarsCount = $dealer->cars()->whereNull('deleted_at')->count();

        return $activeCarsCount < $subscription->plan->listing_limit;
    }

    public function incrementListingCount(Dealer $dealer): void
    {
        // No longer needed - we count active cars dynamically
    }

    public function decrementListingCount(Dealer $dealer): void
    {
        // No longer needed - we count active cars dynamically
    }

    public function getRemainingListings(Dealer $dealer): int
    {
        $subscription = $dealer->activeSubscription();

        if (! $subscription) {
            return 0;
        }

        return $subscription->remainingListings();
    }
}
