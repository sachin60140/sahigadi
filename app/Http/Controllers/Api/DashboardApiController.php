<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CustomerCarListing;

class DashboardApiController extends Controller
{
    /**
     * Get Customer Dashboard Data
     */
    public function customerDashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->currentAccessToken()->can('role:customer')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $listings = CustomerCarListing::with('brand')
            ->where('owner_phone', $user->phone)
            ->orderBy('created_at', 'desc')
            ->get();

        $featured_cars = Car::with('brand', 'images')
            ->where('is_featured', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'listings' => $listings,
                'featured_cars' => $featured_cars,
                'total_listings' => $listings->count(),
                'approved_listings' => $listings->where('status', 'approved')->count(),
                'pending_listings' => $listings->where('status', 'pending')->count(),
            ]
        ]);
    }

    /**
     * Get Dealer Dashboard Data
     */
    public function dealerDashboard(Request $request)
    {
        $dealer = $request->user();

        if (!$dealer->currentAccessToken()->can('role:dealer')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cars = Car::with('brand', 'images')
            ->where('dealer_id', $dealer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'dealer' => $dealer,
                'cars' => $cars,
                'total_cars' => $cars->count(),
                'active_cars' => $cars->where('status', 'active')->count(),
                'sold_cars' => $cars->where('status', 'sold')->count(),
            ]
        ]);
    }
}
