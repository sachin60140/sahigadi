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

class CarController extends Controller
{
    public function __construct(protected SeoService $seoService) {}

    public function index(Request $request)
    {
        $query = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active();

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->keyword.'%')
                    ->orWhere('model', 'like', '%'.$request->keyword.'%');
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        if ($request->filled('min_km')) {
            $query->where('km_driven', '>=', $request->min_km);
        }

        if ($request->filled('max_km')) {
            $query->where('km_driven', '<=', $request->max_km);
        }

        $customerListingQuery = CustomerCarListing::with('brand')
            ->approved()
            ->active()
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('title', 'like', '%'.$request->keyword.'%')
                        ->orWhere('model', 'like', '%'.$request->keyword.'%');
                });
            })
            ->when($request->filled('city'), function ($q) use ($request) {
                $q->where('city', $request->city);
            })
            ->when($request->filled('brand'), function ($q) use ($request) {
                $q->where('brand_id', $request->brand);
            })
            ->when($request->filled('fuel_type'), function ($q) use ($request) {
                $q->where('fuel_type', $request->fuel_type);
            })
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            })
            ->when($request->filled('transmission'), function ($q) use ($request) {
                $q->where('transmission', $request->transmission);
            });

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    $customerListingQuery->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    $customerListingQuery->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    $customerListingQuery->orderBy('created_at', 'desc');
                    break;
                case 'km_low':
                    $query->orderBy('km_driven', 'asc');
                    $customerListingQuery->orderBy('km_driven', 'asc');
                    break;
                case 'relevance':
                default:
                    $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                    $customerListingQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
            $customerListingQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        $cars = $query->paginate(12);
        $customerListings = $customerListingQuery->get();

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
        if ($cars->isNotEmpty()) {
            $ogImage = $this->getFirstImage($cars->first(), null);
        } elseif ($customerListings->isNotEmpty()) {
            $ogImage = $this->getFirstImage(null, $customerListings->first());
        }

        return view('frontend.cars.index', compact('cars', 'customerListings', 'brands', 'cities', 'ogImage'));
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

        return view('frontend.cars.show', array_merge(
            compact('car', 'customerListing', 'relatedCars'),
            [
                'seo' => $seoData,
                'firstImage' => $firstImage,
                'allImages' => $allImages,
                'isCustomerListing' => $isCustomerListing,
                'structuredData' => $structuredData,
                'breadcrumbSchema' => $breadcrumbSchema,
            ]
        ));
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

        $carQuery = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active()
            ->where('city', 'like', '%'.$cityName.'%');

        $customerListingQuery = CustomerCarListing::with(['brand'])
            ->approved()
            ->active()
            ->where('city', 'like', '%'.$cityName.'%');

        if ($request->filled('brand')) {
            $carQuery->where('brand_id', $request->brand);
            $customerListingQuery->where('brand_id', $request->brand);
        }

        if ($request->filled('min_price')) {
            $carQuery->where('price', '>=', $request->min_price);
            $customerListingQuery->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $carQuery->where('price', '<=', $request->max_price);
            $customerListingQuery->where('price', '<=', $request->max_price);
        }

        $cars = $carQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->get();
        $customerListings = $customerListingQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->get();

        $allCars = $cars->concat($customerListings)->sortByDesc('created_at')->values();

        $brands = Brand::active()->orderBy('name')->get();

        $seoTitle = "Used Cars in {$cityName} - Best Deals on Second Hand Cars";
        $seoDescription = "Buy verified used cars in {$cityName}. Best price deals, easy financing, doorstep delivery. Find your perfect second hand vehicle.";

        $ogImage = null;
        if ($allCars->isNotEmpty()) {
            $first = $allCars->first();
            if ($first instanceof \App\Models\Car) {
                $ogImage = $this->getFirstImage($first, null);
            } else {
                $ogImage = $this->getFirstImage(null, $first);
            }
        }

        return view('frontend.cars.city', compact('allCars', 'brands', 'city', 'cityName', 'seoTitle', 'seoDescription', 'ogImage'));
    }

    public function byBrand(Request $request, string $brand, ?string $city = null)
    {
        $brandModel = Brand::where('slug', $brand)->firstOrFail();
        $brandName = $brandModel->name;
        $cityName = $city ? ucwords(str_replace('-', ' ', $city)) : null;

        $carQuery = Car::with(['dealer', 'brand', 'images'])
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

        if ($request->filled('min_price')) {
            $carQuery->where('price', '>=', $request->min_price);
            $customerListingQuery->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $carQuery->where('price', '<=', $request->max_price);
            $customerListingQuery->where('price', '<=', $request->max_price);
        }

        $cars = $carQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->get();
        $customerListings = $customerListingQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->get();

        $allCars = $cars->concat($customerListings)->sortByDesc('created_at')->values();

        if ($cityName) {
            $seoTitle = "Used {$brandName} Cars in {$cityName} - Best Deals on Second Hand Cars";
            $seoDescription = "Buy verified used {$brandName} cars in {$cityName}. Best price deals, easy financing, doorstep delivery.";
        } else {
            $seoTitle = "Used {$brandName} Cars - Best Deals on Second Hand Cars";
            $seoDescription = "Buy verified used {$brandName} cars. Best price deals, easy financing, doorstep delivery.";
        }

        $ogImage = null;
        if ($allCars->isNotEmpty()) {
            $first = $allCars->first();
            if ($first instanceof \App\Models\Car) {
                $ogImage = $this->getFirstImage($first, null);
            } else {
                $ogImage = $this->getFirstImage(null, $first);
            }
        }

        return view('frontend.cars.brand', [
            'allCars' => $allCars,
            'brand' => $brand,
            'brandName' => $brandName,
            'city' => $city,
            'cityName' => $cityName,
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'ogImage' => $ogImage,
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

    public function dealerCatalog(string $dealerSlug)
    {
        $dealer = Dealer::where('slug', $dealerSlug)->firstOrFail();

        $cars = Car::with(['dealer', 'brand', 'images'])
            ->where('dealer_id', $dealer->id)
            ->approved()
            ->active()
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $seoTitle = "{$dealer->name} - Car Listings | SAHI GADI";
        $seoDescription = "Browse all pre-owned cars from {$dealer->name}. View {$cars->total()} verified listings with photos, prices, and dealer details.";

        $ogImage = null;
        if ($cars->isNotEmpty()) {
            $ogImage = $this->getFirstImage($cars->first(), null);
        }

        return view('frontend.cars.dealer-catalog', compact('dealer', 'cars', 'seoTitle', 'seoDescription', 'ogImage'));
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

        return view('frontend.cars.verified-dealers', compact('dealers', 'seoTitle', 'seoDescription'));
    }
}
