<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\FeaturedPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeaturedCarController extends Controller
{
    public function showPlans(Car $car)
    {
        // Ensure dealer owns the car
        if ($car->dealer_id !== auth('dealer')->id()) {
            abort(403, 'Unauthorized access to this car.');
        }

        $plans = FeaturedPlan::active()->orderBy('duration_days')->get();
        return view('dealer.cars.featured-plans', compact('car', 'plans'));
    }

    public function purchase(Request $request, Car $car)
    {
        if ($car->dealer_id !== auth('dealer')->id()) {
            abort(403);
        }

        $request->validate([
            'plan_id' => 'required|exists:featured_plans,id',
        ]);

        $plan = FeaturedPlan::active()->findOrFail($request->plan_id);
        $dealer = auth('dealer')->user();
        $wallet = $dealer->wallet;

        if (!$wallet || $wallet->balance < $plan->price) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance. Please recharge.',
                'redirect' => route('dealer.wallet.index')
            ]);
        }

        try {
            DB::beginTransaction();

            // Deduct from wallet
            $transaction = $wallet->deductFunds(
                $plan->price,
                "Featured Listing: {$car->unique_id} - {$plan->name}"
            );

            // Calculate expiry (stacking logic)
            $activeFeature = $car->featuredListings()->active()->latest('expires_at')->first();
            $baseDate = $activeFeature ? Carbon::parse($activeFeature->expires_at) : now();
            $expiresAt = $baseDate->copy()->addDays($plan->duration_days);

            // Create Featured Listing Record
            $featuredListing = $car->featuredListings()->create([
                'user_type' => get_class($dealer),
                'user_id' => $dealer->id,
                'featured_plan_id' => $plan->id,
                'amount_paid' => $plan->price,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'active',
                'transaction_id' => $transaction->id
            ]);

            // Sync legacy fields for fast querying
            $car->update([
                'is_featured' => true,
                'featured_expires_at' => $expiresAt,
            ]);

            DB::commit();

            // Send emails
            $emailDetails = [
                'car_id' => $car->unique_id,
                'car_title' => $car->title,
                'plan_name' => $plan->name,
                'duration_days' => $plan->duration_days,
                'amount_paid' => $plan->price,
                'expires_at' => $expiresAt,
                'action_url' => route('dealer.cars.index')
            ];

            try {
                if ($dealer->email) {
                    $emailDetails['is_admin'] = false;
                    $emailDetails['user_name'] = $dealer->name;
                    \Illuminate\Support\Facades\Mail::to($dealer->email)->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
                }

                $emailDetails['is_admin'] = true;
                $emailDetails['user_name'] = 'Admin';
                \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
            } catch (\Exception $e) {
                \Log::error('Failed to send featured plan subscription email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Car successfully featured!',
                'redirect' => route('dealer.cars.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Dealer featured plan purchase failed: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
