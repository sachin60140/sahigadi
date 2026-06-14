<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerCarListing;
use App\Models\FeaturedPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class FeaturedCarController extends Controller
{
    public function showPlans(CustomerCarListing $customerListing)
    {
        // Ensure customer owns the listing
        if ($customerListing->owner_phone !== auth('customer')->user()->phone) {
            abort(403, 'Unauthorized access to this listing.');
        }

        $plans = FeaturedPlan::active()->orderBy('duration_days')->get();
        $customer = auth('customer')->user();

        return Inertia::render('Customer/Listings/FeaturedPlans', [
            'listing' => [
                'title' => $customerListing->title,
                'model' => $customerListing->model,
                'unique_id' => $customerListing->unique_id,
                'is_featured' => $customerListing->isFeatured(),
                'featured_expires_at' => optional($customerListing->featured_expires_at)->format('d M Y, h:i A'),
            ],
            'plans' => $plans->map(fn (FeaturedPlan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'duration_days' => $plan->duration_days,
            ]),
            'walletBalance' => (float) ($customer->wallet?->balance ?? 0),
            'actions' => [
                'dashboard' => route('customer.dashboard'),
                'purchase' => route('customer.listing.featured-purchase', $customerListing),
                'wallet' => route('customer.wallet.index'),
            ],
        ]);
    }

    public function purchase(Request $request, CustomerCarListing $customerListing)
    {
        if ($customerListing->owner_phone !== auth('customer')->user()->phone) {
            abort(403);
        }

        $request->validate([
            'plan_id' => 'required|exists:featured_plans,id',
        ]);

        $plan = FeaturedPlan::active()->findOrFail($request->plan_id);
        $customer = auth('customer')->user();
        
        // Ensure customer has a wallet
        $wallet = $customer->wallet()->firstOrCreate(['customer_id' => $customer->id], ['balance' => 0]);

        if ($wallet->balance < $plan->price) {
            return back()->with('error', 'Insufficient wallet balance. Please recharge.');
        }

        try {
            DB::beginTransaction();

            // Deduct from wallet
            $transaction = $wallet->deductFunds(
                $plan->price,
                "Featured Listing: {$customerListing->unique_id} - {$plan->name}"
            );

            // Calculate expiry (stacking logic)
            $activeFeature = $customerListing->featuredListings()->active()->latest('expires_at')->first();
            $baseDate = $activeFeature ? Carbon::parse($activeFeature->expires_at) : now();
            $expiresAt = $baseDate->copy()->addDays($plan->duration_days);

            // Create Featured Listing Record
            $featuredListing = $customerListing->featuredListings()->create([
                'user_type' => get_class($customer),
                'user_id' => $customer->id,
                'featured_plan_id' => $plan->id,
                'amount_paid' => $plan->price,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'active',
                'transaction_id' => $transaction->id
            ]);

            // Sync legacy fields for fast querying
            $customerListing->update([
                'is_featured' => true,
                'featured_expires_at' => $expiresAt,
            ]);

            DB::commit();

            // Send emails
            $emailDetails = [
                'car_id' => $customerListing->unique_id,
                'car_title' => $customerListing->title,
                'plan_name' => $plan->name,
                'duration_days' => $plan->duration_days,
                'amount_paid' => $plan->price,
                'expires_at' => $expiresAt,
                'action_url' => route('customer.dashboard')
            ];

            try {
                if ($customer->email) {
                    $emailDetails['is_admin'] = false;
                    $emailDetails['user_name'] = $customer->name;
                    \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
                }

                $emailDetails['is_admin'] = true;
                $emailDetails['user_name'] = 'Admin';
                \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
            } catch (\Exception $e) {
                \Log::error('Failed to send featured plan subscription email: ' . $e->getMessage());
            }

            return redirect()->route('customer.dashboard')->with('success', 'Listing successfully featured!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Featured plan purchase failed: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }
}
