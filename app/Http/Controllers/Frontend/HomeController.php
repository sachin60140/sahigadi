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
        $featuredCars = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active()
            ->featured()
            ->limit(4)
            ->get();

        $featuredCustomerListings = CustomerCarListing::with('brand')
            ->approved()
            ->active()
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_expires_at')
                    ->orWhere('featured_expires_at', '>', now());
            })
            ->limit(4)
            ->get();

        $allFeatured = $featuredCars->concat($featuredCustomerListings);

        $featuredSlugs = $allFeatured->pluck('slug')->toArray();

        $query2 = Car::with(['dealer', 'brand', 'images'])
            ->approved()
            ->active()
            ->whereNotIn('slug', $featuredSlugs);

        if ($request->has('keyword')) {
            $query2->where('title', 'like', '%'.$request->keyword.'%');
        }

        if ($request->has('city')) {
            $query2->where('city', $request->city);
        }

        if ($request->has('brand')) {
            $query2->where('brand_id', $request->brand);
        }

        if ($request->has('fuel_type')) {
            $query2->where('fuel_type', $request->fuel_type);
        }

        if ($request->has('min_price')) {
            $query2->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query2->where('price', '<=', $request->max_price);
        }

        if ($request->has('transmission')) {
            $query2->where('transmission', $request->transmission);
        }

        $cars = $query2->orderBy('created_at', 'desc')->get();

        $customerListings = CustomerCarListing::with('brand')
            ->approved()
            ->active()
            ->whereNotIn('slug', $featuredSlugs)
            ->orderBy('created_at', 'desc')
            ->get();

        $allCars = $cars->concat($customerListings)->sortByDesc('created_at')->values();

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
        $cities = Car::approved()->active()->whereNotNull('city')->distinct()->pluck('city');

        $homepageSchema = $this->generateHomepageSchema($allFeatured);

        return view('frontend.home', array_merge(
            compact('allFeatured', 'allCars', 'brands', 'cities'),
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
            'name' => 'Featured Cars - SAHIGADI',
            'description' => 'Featured pre-owned cars from SAHIGADI marketplace',
            'url' => url('/'),
            'numberOfItems' => count($itemListElement),
            'itemListElement' => $itemListElement,
        ];
    }
}
