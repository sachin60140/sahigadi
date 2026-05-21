<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicController extends Controller
{
    /**
     * Get data for the mobile app home screen (Brands, Cities, Featured Cars)
     */
    public function home()
    {
        // 1. Featured Cars (Dealer + Customer)
        $featuredCars = Car::with(['dealer:id,company_name,slug,city,profile_image', 'brand:id,name', 'images'])
            ->approved()
            ->active()
            ->featured()
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $featuredCustomerListings = CustomerCarListing::with(['brand:id,name'])
            ->approved()
            ->active()
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_expires_at')
                    ->orWhere('featured_expires_at', '>', now());
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $allFeatured = $featuredCars->concat($featuredCustomerListings)->shuffle()->take(8);

        // 2. Brands with active car count
        $brands = Brand::active()
            ->withCount(['cars' => function ($q) {
                $q->approved()->active();
            }])
            ->get()
            ->filter(function ($brand) {
                $customerListingCount = CustomerCarListing::where('brand_id', $brand->id)
                    ->approved()
                    ->active()
                    ->count();
                return $brand->cars_count > 0 || $customerListingCount > 0;
            })
            ->map(function ($brand) {
                $brand->cars_count += CustomerCarListing::where('brand_id', $brand->id)
                    ->approved()
                    ->active()
                    ->count();
                return $brand;
            })
            ->sortBy('name')
            ->values();

        // 3. Unique Cities
        $dealerCities = Car::approved()->active()->whereNotNull('city')->distinct()->pluck('city');
        $customerCities = CustomerCarListing::approved()->active()->whereNotNull('city')->distinct()->pluck('city');
        $cities = $dealerCities->concat($customerCities)
            ->map(fn($c) => trim($c))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // 4. Recent Listings
        $recentDealerCars = Car::with(['dealer:id,company_name,slug,city,profile_image', 'brand:id,name', 'images'])
            ->approved()
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentCustomerCars = CustomerCarListing::with(['brand:id,name'])
            ->approved()
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentListings = $recentDealerCars->concat($recentCustomerCars)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'featured_cars' => $allFeatured,
                'brands' => $brands,
                'cities' => $cities,
                'recent_listings' => $recentListings,
            ]
        ]);
    }

    /**
     * Search and Filter Cars with Pagination
     */
    public function cars(Request $request)
    {
        // Dealer Cars Query
        $query1 = Car::with(['dealer:id,company_name,slug,city,profile_image', 'brand:id,name', 'images'])
            ->approved()
            ->active();

        // Customer Cars Query
        $query2 = CustomerCarListing::with(['brand:id,name'])
            ->approved()
            ->active();

        // Apply Filters to both queries
        $filters = ['keyword' => 'title', 'city' => 'city', 'brand' => 'brand_id', 'fuel_type' => 'fuel_type', 'transmission' => 'transmission'];
        
        foreach ($filters as $requestKey => $dbColumn) {
            if ($request->filled($requestKey)) {
                if ($requestKey === 'keyword') {
                    $query1->where($dbColumn, 'like', '%' . $request->keyword . '%');
                    $query2->where($dbColumn, 'like', '%' . $request->keyword . '%');
                } else {
                    $query1->where($dbColumn, $request->{$requestKey});
                    $query2->where($dbColumn, $request->{$requestKey});
                }
            }
        }

        if ($request->filled('min_price')) {
            $query1->where('price', '>=', $request->min_price);
            $query2->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query1->where('price', '<=', $request->max_price);
            $query2->where('price', '<=', $request->max_price);
        }

        // Get results
        $cars = $query1->get();
        $customerListings = $query2->get();

        // Merge and sort
        $allCars = $cars->concat($customerListings)->sortByDesc('created_at')->values();

        // Manual Pagination for merged collection
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        
        $paginatedItems = new LengthAwarePaginator(
            $allCars->forPage($page, $perPage)->values(),
            $allCars->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'data' => $paginatedItems
        ]);
    }

    /**
     * Single Car Details
     */
    public function carDetail($slug)
    {
        $car = Car::with(['dealer:id,company_name,slug,city,profile_image,phone,wallet_balance', 'brand', 'images'])
            ->where('slug', $slug)
            ->approved()
            ->active()
            ->first();

        if ($car) {
            $car->type = 'dealer';
            return response()->json(['success' => true, 'data' => $car]);
        }

        $customerListing = CustomerCarListing::with(['brand', 'customer:id,name,phone'])
            ->where('slug', $slug)
            ->approved()
            ->active()
            ->first();

        if ($customerListing) {
            $customerListing->type = 'customer';
            return response()->json(['success' => true, 'data' => $customerListing]);
        }

        return response()->json(['success' => false, 'message' => 'Car not found'], 404);
    }
}
