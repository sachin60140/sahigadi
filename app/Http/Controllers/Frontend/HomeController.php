<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CustomerCarListing;
use App\Services\SeoService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(protected SeoService $seoService) {}

    public function index(Request $request)
    {
        // 1. Featured Cars (Dealer + Customer)
        $featuredDealerCars = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active()
            ->featured()
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $featuredCustomerCars = CustomerCarListing::with('brand')
            ->approved()
            ->active()
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_expires_at')
                    ->orWhere('featured_expires_at', '>', now());
            })
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $featuredCars = $featuredDealerCars->concat($featuredCustomerCars)->shuffle()->take(6);

        // Fallback: If no featured cars, get the latest active cars
        if ($featuredCars->isEmpty()) {
            $latestDealer = Car::with(['dealer', 'brand', 'images'])->approved()->active()->orderBy('created_at', 'desc')->limit(3)->get();
            $latestCustomer = CustomerCarListing::with('brand')->approved()->active()->orderBy('created_at', 'desc')->limit(3)->get();
            $featuredCars = $latestDealer->concat($latestCustomer)->sortByDesc('created_at')->take(6)->values();
        }

        $featuredSlugs = $featuredCars->pluck('slug')->toArray();

        // 2. Latest Cars (Recently Added)
        $latestDealerCars = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active()
            ->whereNotIn('slug', $featuredSlugs)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        $latestCustomerCars = CustomerCarListing::with('brand')
            ->approved()
            ->active()
            ->whereNotIn('slug', $featuredSlugs)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        $latestCars = $latestDealerCars->concat($latestCustomerCars)->sortByDesc('created_at')->take(12)->values();

        // 3. Brands & Cities for dropdowns
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
                $customerListingCount = CustomerCarListing::where('brand_id', $brand->id)
                    ->approved()
                    ->active()
                    ->count();
                $brand->cars_count = $brand->cars_count + $customerListingCount;

                return $brand;
            })
            ->sortBy('name')
            ->values();

        $dealerCities = Car::approved()->active()->whereNotNull('city')->distinct()->pluck('city');
        $customerCities = CustomerCarListing::approved()->active()->whereNotNull('city')->distinct()->pluck('city');
        $cities = $dealerCities->concat($customerCities)
            ->map(fn($c) => trim($c))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $homepageSchema = $this->generateHomepageSchema($featuredCars);

        $safeFeaturedCars = $featuredCars->map(fn($car) => $this->mapSafeCarPayload($car))->values()->toArray();
        $safeLatestCars = $latestCars->map(fn($car) => $this->mapSafeCarPayload($car))->values()->toArray();

        // Pass budgetOptions and fuelTypes explicitly since they were requested
        $budgetOptions = [
            ['label' => 'Under 3 Lakh', 'value' => '300000'],
            ['label' => 'Under 5 Lakh', 'value' => '500000'],
            ['label' => 'Under 7 Lakh', 'value' => '700000'],
            ['label' => 'Under 10 Lakh', 'value' => '1000000'],
            ['label' => 'Under 15 Lakh', 'value' => '1500000'],
        ];

        $fuelTypes = ['Petrol', 'Diesel', 'CNG', 'Electric'];
        $plans = \App\Models\Plan::active()->orderBy('price')->get();

        return \Inertia\Inertia::render('Public/Home', array_merge(
            [
                'featuredCars' => $safeFeaturedCars,
                'latestCars' => $safeLatestCars,
                'brands' => $brands,
                'cities' => $cities,
                'budgetOptions' => $budgetOptions,
                'fuelTypes' => $fuelTypes,
                'plans' => $plans
            ],
            ['homepageSchema' => $homepageSchema]
        ));
    }

    protected function generateHomepageSchema($allFeatured): array
    {
        $itemListElement = [];

        foreach ($allFeatured as $index => $item) {
            $image = $item instanceof Car ? ($item->primaryImage()?->url ?? asset('images/default-car.jpg')) :
                     (isset($item->images[0]) ? asset('storage/'.$item->images[0]) : asset('images/default-car.jpg'));

            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => route('car.detail', $item->slug),
                'item' => [
                    '@type' => 'Product',
                    'name' => $item->title,
                    'description' => $item->title.' - '.($item->year ?? 'N/A').' '.($item->brand->name ?? ''),
                    'image' => $image,
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => (string) ($item->price ?? 0),
                        'priceCurrency' => 'INR',
                        'availability' => 'https://schema.org/InStock',
                    ],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Featured Cars - SAHI GADI',
            'description' => 'Featured pre-owned cars from SAHI GADI marketplace',
            'url' => url('/'),
            'numberOfItems' => count($itemListElement),
            'itemListElement' => $itemListElement,
        ];
    }
    
    /**
     * Strictly map car models to a safe public payload to prevent data leakage (e.g. phone numbers).
     */
    protected function mapSafeCarPayload($car)
    {
        return [
            'id' => $car->id,
            'slug' => $car->slug,
            'title' => $car->title,
            'brand' => $car->brand ? ['id' => $car->brand->id, 'name' => $car->brand->name, 'slug' => $car->brand->slug] : null,
            'model' => $car->model,
            'variant' => $car->variant,
            'year' => $car->year,
            'fuel_type' => $car->fuel_type,
            'transmission' => $car->transmission,
            'km_driven' => $car->km_driven,
            'price' => $car->price,
            'city' => $car->city,
            'is_featured' => $car->is_featured,
            'is_verified' => isset($car->dealer_id) ? true : ($car->is_verified ?? false),
            // Map the primary image out safely
            'image_url' => $car instanceof Car ? ($car->primaryImage()?->url ?? '/images/default-car.jpg') : 
                (is_string($car->images) ? 
                    (json_decode($car->images)[0] ?? '/images/default-car.jpg') : 
                    ($car->images[0] ?? '/images/default-car.jpg')),
        ];
    }
}
