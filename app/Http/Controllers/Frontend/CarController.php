<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CustomerCarListing;
use App\Models\Dealer;
use App\Models\Enquiry;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CarController extends Controller
{
    public function __construct(protected SeoService $seoService) {}

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
            'image_url' => $car instanceof Car ? $this->getFirstImage($car, null) : $this->getFirstImage(null, $car),
        ];
    }

    protected function mergeAndPaginate($carQuery, $customerListingQuery, $perPage = 12, $request)
    {
        // Get all matching results
        $cars = $carQuery->get();
        $customerListings = $customerListingQuery->get();

        // Merge and sort
        $allCars = $cars->concat($customerListings);

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $allCars = $allCars->sortBy('price')->values();
                    break;
                case 'price_high':
                    $allCars = $allCars->sortByDesc('price')->values();
                    break;
                case 'newest':
                    $allCars = $allCars->sortByDesc('created_at')->values();
                    break;
                case 'km_low':
                    $allCars = $allCars->sortBy('km_driven')->values();
                    break;
                case 'relevance':
                default:
                    $allCars = $allCars->sortByDesc('created_at')->sortByDesc('is_featured')->values();
            }
        } else {
            $allCars = $allCars->sortByDesc('created_at')->sortByDesc('is_featured')->values();
        }

        // Apply mapSafeCarPayload
        $safeCars = $allCars->map(fn($car) => $this->mapSafeCarPayload($car));

        // Paginate manually
        $page = $request->get('page', 1);
        $total = $safeCars->count();
        $results = $safeCars->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $paginator;
    }


    public function index(Request $request)
    {
        $carQuery = Car::with(['brand', 'images'])
            ->approved()
            ->active();

        $customerListingQuery = CustomerCarListing::with('brand')
            ->approved()
            ->active();

        $this->applyFilters($carQuery, $customerListingQuery, $request);

        $carsPaginated = $this->mergeAndPaginate($carQuery, $customerListingQuery, 12, $request);

        $brands = Brand::active()->orderBy('name')->get();
        
        $dealerCities = Car::approved()->active()->whereNotNull('city')->distinct()->pluck('city');
        $customerCities = CustomerCarListing::approved()->active()->whereNotNull('city')->distinct()->pluck('city');
        $cities = $dealerCities->concat($customerCities)
            ->map(fn($c) => trim($c))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $ogImage = null;
        if ($carsPaginated->count() > 0) {
            $ogImage = $carsPaginated->first()['image_url'] ?? null;
        }

        return \Inertia\Inertia::render('Public/Cars/Index', [
            'cars' => $carsPaginated,
            'brands' => $brands,
            'cities' => $cities,
            'ogImage' => $ogImage,
            'fuelTypes' => ['Petrol', 'Diesel', 'CNG', 'Electric'],
            'transmissions' => ['Manual', 'Automatic'],
            'budgetOptions' => [
                ['label' => 'Under 3 Lakh', 'value' => '300000'],
                ['label' => 'Under 5 Lakh', 'value' => '500000'],
                ['label' => 'Under 7 Lakh', 'value' => '700000'],
                ['label' => 'Under 10 Lakh', 'value' => '1000000'],
                ['label' => 'Under 15 Lakh', 'value' => '1500000'],
            ],
            'sortOptions' => [
                ['label' => 'Relevance', 'value' => 'relevance'],
                ['label' => 'Price: Low to High', 'value' => 'price_low'],
                ['label' => 'Price: High to Low', 'value' => 'price_high'],
                ['label' => 'Newest First', 'value' => 'newest'],
                ['label' => 'KM: Low to High', 'value' => 'km_low'],
            ],
            'filters' => $request->only(['keyword', 'city', 'brand', 'fuel_type', 'min_price', 'max_price', 'transmission', 'sort'])
        ]);
    }

    protected function applyFilters($carQuery, $customerListingQuery, $request)
    {
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $carQuery->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('model', 'like', "%{$keyword}%");
            });
            $customerListingQuery->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('model', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('city')) {
            $carQuery->where('city', $request->city);
            $customerListingQuery->where('city', $request->city);
        }

        if ($request->filled('brand')) {
            $carQuery->where('brand_id', $request->brand);
            $customerListingQuery->where('brand_id', $request->brand);
        }

        if ($request->filled('fuel_type')) {
            $carQuery->where('fuel_type', $request->fuel_type);
            $customerListingQuery->where('fuel_type', $request->fuel_type);
        }

        if ($request->filled('min_price')) {
            $carQuery->where('price', '>=', $request->min_price);
            $customerListingQuery->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $carQuery->where('price', '<=', $request->max_price);
            $customerListingQuery->where('price', '<=', $request->max_price);
        }

        if ($request->filled('transmission')) {
            $carQuery->where('transmission', $request->transmission);
            $customerListingQuery->where('transmission', $request->transmission);
        }
    }

    public function show(string $slug)
    {
        $car = Car::with(['dealer', 'brand', 'images'])
            ->where('slug', $slug)
            ->approved()
            ->active()
            ->first();

        $customerListing = null;
        if (! $car) {
            $customerListing = CustomerCarListing::with(['brand', 'customer'])
                ->where('slug', $slug)
                ->approved()
                ->active()
                ->firstOrFail();
        }

        $item = $car ?? $customerListing;
        $isCustomerListing = $customerListing !== null;

        $seoData = $this->seoService->generateListingSeo([
            'title' => $item->title,
            'brand' => $item->brand,
            'year' => $item->year,
            'model' => $item->model,
            'price' => $item->price,
            'city' => $item->city,
            'km_driven' => $item->km_driven,
            'fuel_type' => $item->fuel_type,
            'transmission' => $item->transmission,
        ]);

        $firstImage = $this->getFirstImage($car, $customerListing);
        $allImages = $this->getAllImages($car, $customerListing);

        $structuredData = $this->seoService->generateStructuredData([
            'title' => $item->title,
            'brand' => $item->brand,
            'year' => $item->year,
            'model' => $item->model,
            'price' => $item->price,
            'km_driven' => $item->km_driven,
            'fuel_type' => $item->fuel_type,
            'transmission' => $item->transmission,
            'images' => $allImages,
            'seller_name' => $isCustomerListing ? (($item->customer && $item->customer->name) ? $item->customer->name : ($item->owner_name ?? 'Owner')) : ($item->dealer->name ?? 'SAHI GADI'),
            'seller_type' => $isCustomerListing ? 'Person' : 'Organization',
            'url' => route('car.detail', $item->slug),
        ]);

        $breadcrumbSchema = $this->seoService->generateBreadcrumbSchema([
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Cars', 'url' => route('cars.index')],
            ['name' => $item->title, 'url' => route('car.detail', $item->slug)],
        ]);

        $relatedCars = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active()
            ->when($car, function ($q) use ($car) {
                $q->where('id', '!=', $car->id);
            })
            ->where(function ($query) use ($car, $customerListing) {
                $brandId = $car?->brand_id ?? $customerListing?->brand_id;
                $city = $car?->city ?? $customerListing?->city;
                if ($brandId) {
                    $query->where('brand_id', $brandId);
                }
                if ($city) {
                    $query->orWhere('city', $city);
                }
            })
            ->limit(4)
            ->get();

        $safeRelatedCars = $relatedCars->map(function ($relatedCar) {
            return $this->mapSafeCarPayload($relatedCar);
        })->values()->toArray();

        $sellerPublicProfile = [
            'display_name' => $isCustomerListing 
                ? (($item->customer && $item->customer->name) ? $item->customer->name : ($item->owner_name ?? 'Owner')) 
                : ($item->dealer->name ?? 'SAHI GADI'),
            'type' => $isCustomerListing ? 'Owner' : 'Dealer',
            'city' => $item->city,
            'is_verified' => $item->is_verified ?? true,
            'member_since' => $isCustomerListing 
                ? ($item->customer->created_at ?? $item->created_at)->format('Y')
                : ($item->dealer->created_at ?? $item->created_at)->format('Y'),
            'masked_mobile' => $isCustomerListing 
                ? substr($item->owner_phone ?? $item->customer->phone ?? '0000000000', 0, 3) . '****' . substr($item->owner_phone ?? $item->customer->phone ?? '0000000000', -3)
                : substr($item->dealer->phone ?? '0000000000', 0, 3) . '****' . substr($item->dealer->phone ?? '0000000000', -3),
        ];

        // Ensure registration number is masked
        $regNumber = $item->registration_number ?? $item->reg_number ?? '';
        $maskedReg = $regNumber ? substr($regNumber, 0, 4) . '****' . substr($regNumber, -4) : null;

        $safeCarDetail = [
            'id' => $item->id,
            'slug' => $item->slug,
            'title' => $item->title,
            'brand' => $item->brand->name ?? '',
            'model' => $item->model,
            'variant' => $item->variant,
            'year' => $item->year,
            'fuel_type' => $item->fuel_type,
            'transmission' => $item->transmission,
            'km_driven' => $item->km_driven,
            'price' => $item->price,
            'city' => $item->city,
            'state' => $item->state ?? null,
            'image_urls' => $allImages,
            'main_image_url' => $firstImage,
            'is_verified' => $item->is_verified ?? true,
            'description' => $item->description,
            'ownership' => $item->ownership ?? 'First',
            'registration_number_masked' => $maskedReg,
            'insurance_status' => $item->insurance_status ?? 'Valid',
            'color' => $item->color ?? null,
            'body_type' => $item->body_type ?? null,
            'engine' => $item->engine ?? null,
            'isCustomerListing' => $isCustomerListing,
            'seller_public_profile' => $sellerPublicProfile,
        ];

        return \Inertia\Inertia::render('Public/Cars/Show', [
            'car' => $safeCarDetail,
            'relatedCars' => $safeRelatedCars,
            'seo' => $seoData,
            'structuredData' => $structuredData,
            'breadcrumbSchema' => $breadcrumbSchema,
        ]);
    }

    protected function getFirstImage(?Car $car, ?CustomerCarListing $customerListing): string
    {
        $path = null;
        if ($car && $car->image_url) {
            $path = $car->images->first()->image_path ?? null;
        } elseif ($customerListing) {
            $images = json_decode($customerListing->images ?? '[]', true);
            $path = count($images) > 0 ? $images[0] : null;
        }

        if ($path && !filter_var($path, FILTER_VALIDATE_URL)) {
            return route('og.image.generate', ['path' => $path]);
        }

        // Fallback for URLs or missing
        if ($car) {
            return $car->image_url ?? asset('images/default-car.jpg');
        }

        if ($customerListing) {
            $images = json_decode($customerListing->images ?? '[]', true);
            return count($images) > 0 ? asset('storage/'.$images[0]) : asset('images/default-car.jpg');
        }

        return asset('images/default-car.jpg');
    }

    protected function getAllImages(?Car $car, ?CustomerCarListing $customerListing): array
    {
        if ($car) {
            return $car->images->map(function ($img) {
                $path = $img->image_path;
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }

                return asset('storage/'.$path);
            })->toArray();
        }

        if ($customerListing) {
            $images = json_decode($customerListing->images ?? '[]', true);

            return array_map(fn ($img) => asset('storage/'.$img), $images);
        }

        return [];
    }

    public function byCity(string $city, Request $request)
    {
        $cityName = ucwords(str_replace('-', ' ', $city));

        $carQuery = Car::with(['brand', 'images'])
            ->approved()
            ->active()
            ->where('city', 'like', '%'.$cityName.'%');

        $customerListingQuery = CustomerCarListing::with(['brand'])
            ->approved()
            ->active()
            ->where('city', 'like', '%'.$cityName.'%');

        $this->applyFilters($carQuery, $customerListingQuery, $request);

        $carsPaginated = $this->mergeAndPaginate($carQuery, $customerListingQuery, 12, $request);
        $brands = Brand::active()->orderBy('name')->get();

        $seoTitle = "Used Cars in {$cityName} - Best Deals on Second Hand Cars";
        $seoDescription = "Buy verified used cars in {$cityName}. Best price deals, easy financing, doorstep delivery. Find your perfect second hand vehicle.";

        $ogImage = null;
        if ($carsPaginated->count() > 0) {
            $ogImage = $carsPaginated->first()['image_url'] ?? null;
        }

        return \Inertia\Inertia::render('Public/Cars/Index', [
            'cars' => $carsPaginated,
            'brands' => $brands, 
            'cities' => [$cityName], // For the filter pre-select
            'cityName' => $cityName, 
            'seoTitle' => $seoTitle, 
            'seoDescription' => $seoDescription, 
            'ogImage' => $ogImage,
            'fuelTypes' => ['Petrol', 'Diesel', 'CNG', 'Electric'],
            'transmissions' => ['Manual', 'Automatic'],
            'budgetOptions' => [
                ['label' => 'Under 3 Lakh', 'value' => '300000'],
                ['label' => 'Under 5 Lakh', 'value' => '500000'],
                ['label' => 'Under 7 Lakh', 'value' => '700000'],
                ['label' => 'Under 10 Lakh', 'value' => '1000000'],
                ['label' => 'Under 15 Lakh', 'value' => '1500000'],
            ],
            'sortOptions' => [
                ['label' => 'Relevance', 'value' => 'relevance'],
                ['label' => 'Price: Low to High', 'value' => 'price_low'],
                ['label' => 'Price: High to Low', 'value' => 'price_high'],
                ['label' => 'Newest First', 'value' => 'newest'],
                ['label' => 'KM: Low to High', 'value' => 'km_low'],
            ],
            'filters' => array_merge($request->only(['keyword', 'brand', 'fuel_type', 'min_price', 'max_price', 'transmission', 'sort']), ['city' => $cityName])
        ]);
    }

    public function byBrand(Request $request, string $brand, ?string $city = null)
    {
        $brandModel = Brand::where('slug', $brand)->firstOrFail();
        $brandName = $brandModel->name;
        $cityName = $city ? ucwords(str_replace('-', ' ', $city)) : null;

        $carQuery = Car::with(['brand', 'images'])
            ->approved()
            ->active()
            ->where('brand_id', $brandModel->id);

        $customerListingQuery = CustomerCarListing::with(['brand'])
            ->approved()
            ->active()
            ->where('brand_id', $brandModel->id);

        if ($cityName) {
            $carQuery->where('city', 'like', '%'.$cityName.'%');
            $customerListingQuery->where('city', 'like', '%'.$cityName.'%');
        }

        $this->applyFilters($carQuery, $customerListingQuery, $request);

        $carsPaginated = $this->mergeAndPaginate($carQuery, $customerListingQuery, 12, $request);

        if ($cityName) {
            $seoTitle = "Used {$brandName} Cars in {$cityName} - Best Deals on Second Hand Cars";
            $seoDescription = "Buy verified used {$brandName} cars in {$cityName}. Best price deals, easy financing, doorstep delivery.";
        } else {
            $seoTitle = "Used {$brandName} Cars - Best Deals on Second Hand Cars";
            $seoDescription = "Buy verified used {$brandName} cars. Best price deals, easy financing, doorstep delivery.";
        }

        $ogImage = null;
        if ($carsPaginated->count() > 0) {
            $ogImage = $carsPaginated->first()['image_url'] ?? null;
        }

        return \Inertia\Inertia::render('Public/Cars/Index', [
            'cars' => $carsPaginated,
            'brands' => [$brandModel], 
            'cities' => $cityName ? [$cityName] : [], 
            'brandName' => $brandName,
            'cityName' => $cityName,
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'ogImage' => $ogImage,
            'fuelTypes' => ['Petrol', 'Diesel', 'CNG', 'Electric'],
            'transmissions' => ['Manual', 'Automatic'],
            'budgetOptions' => [
                ['label' => 'Under 3 Lakh', 'value' => '300000'],
                ['label' => 'Under 5 Lakh', 'value' => '500000'],
                ['label' => 'Under 7 Lakh', 'value' => '700000'],
                ['label' => 'Under 10 Lakh', 'value' => '1000000'],
                ['label' => 'Under 15 Lakh', 'value' => '1500000'],
            ],
            'sortOptions' => [
                ['label' => 'Relevance', 'value' => 'relevance'],
                ['label' => 'Price: Low to High', 'value' => 'price_low'],
                ['label' => 'Price: High to Low', 'value' => 'price_high'],
                ['label' => 'Newest First', 'value' => 'newest'],
                ['label' => 'KM: Low to High', 'value' => 'km_low'],
            ],
            'filters' => array_merge($request->only(['keyword', 'fuel_type', 'min_price', 'max_price', 'transmission', 'sort']), ['brand' => $brandModel->id, 'city' => $cityName ?? ''])
        ]);
    }

    public function enquiry(Request $request, string $slug)
    {
        $car = Car::where('slug', $slug)->approved()->active()->first();

        if (! $car) {
            $customerListing = CustomerCarListing::where('slug', $slug)->approved()->active()->firstOrFail();
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:1000',
        ]);

        Enquiry::create([
            'car_id' => $car->id,
            'dealer_id' => $car->dealer_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'message' => $request->message,
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Enquiry submitted successfully! The dealer will contact you soon.');
    }

    public function dealerCatalog(Request $request, string $dealerSlug)
    {
        $dealer = Dealer::where('slug', $dealerSlug)->firstOrFail();

        $carsQuery = Car::with(['dealer', 'brand', 'images'])
            ->where('dealer_id', $dealer->id)
            ->approved()
            ->active();

        match ($request->get('sort')) {
            'newest' => $carsQuery->orderBy('created_at', 'desc'),
            'price_low' => $carsQuery->orderBy('price', 'asc'),
            'price_high' => $carsQuery->orderBy('price', 'desc'),
            'km_low' => $carsQuery->orderBy('km_driven', 'asc'),
            default => $carsQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
        };

        $cars = $carsQuery->paginate(12)->withQueryString();

        $seoTitle = "{$dealer->name} - Car Listings | SAHI GADI";
        $seoDescription = "Browse all pre-owned cars from {$dealer->name}. View {$cars->total()} verified listings with photos, prices, and dealer details.";

        $ogImage = null;
        if ($cars->isNotEmpty()) {
            $ogImage = $this->getFirstImage($cars->first(), null);
        }

        $safeCars = $cars->through(fn ($car) => $this->mapSafeCarPayload($car));

        return \Inertia\Inertia::render('Public/DealerCatalog', [
            'dealer' => [
                'id' => $dealer->id,
                'name' => $dealer->name,
                'company_name' => $dealer->company_name,
                'slug' => $dealer->slug,
                'phone' => $dealer->phone,
                'email' => $dealer->email,
                'city' => $dealer->city,
                'gst_verified' => (bool) $dealer->gst_verified,
                'email_verified' => (bool) $dealer->email_verified_at,
            ],
            'cars' => $safeCars,
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'ogImage' => $ogImage,
            'filters' => $request->only(['sort']),
            'sortOptions' => [
                ['label' => 'Relevance', 'value' => 'relevance'],
                ['label' => 'Newest First', 'value' => 'newest'],
                ['label' => 'Price: Low to High', 'value' => 'price_low'],
                ['label' => 'Price: High to Low', 'value' => 'price_high'],
                ['label' => 'KM: Low to High', 'value' => 'km_low'],
            ],
        ]);
    }

    public function verifiedDealers()
    {
        $dealers = Dealer::where('status', 'approved')
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->orWhere('gst_verified', true);
            })
            ->withCount('approvedCars')
            ->orderByDesc('gst_verified')
            ->orderBy('approved_cars_count', 'desc')
            ->paginate(12);

        $seoTitle = 'Verified Dealers | SAHI GADI';
        $seoDescription = 'Browse verified car dealers with authentic listings. Find trusted dealers with quality pre-owned cars.';

        $safeDealers = $dealers->through(fn ($dealer) => [
            'id' => $dealer->id,
            'name' => $dealer->name,
            'company_name' => $dealer->company_name,
            'city' => $dealer->city,
            'phone' => $dealer->phone,
            'slug' => $dealer->slug,
            'gst_verified' => (bool) $dealer->gst_verified,
            'email_verified' => (bool) $dealer->email_verified_at,
            'approved_cars_count' => $dealer->approved_cars_count ?? 0,
        ]);

        return \Inertia\Inertia::render('Public/VerifiedDealers', [
            'dealers' => $safeDealers,
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
        ]);
    }
}
