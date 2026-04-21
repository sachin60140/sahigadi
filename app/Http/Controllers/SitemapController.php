<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CustomerCarListing;

class SitemapController extends Controller
{
    public function index()
    {
        $cars = Car::approved()
            ->active()
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get();

        $customerListings = CustomerCarListing::approved()
            ->active()
            ->orderBy('updated_at', 'desc')
            ->limit(500)
            ->get();

        $brands = Brand::active()->get();

        $carCities = Car::approved()->active()->whereNotNull('city')->distinct()->pluck('city')->toArray();
        $listingCities = CustomerCarListing::approved()->active()->whereNotNull('city')->distinct()->pluck('city')->toArray();
        $cities = collect(array_merge($carCities, $listingCities))->unique()->map(function($city) {
            return ucwords(str_replace('-', ' ', $city));
        })->unique()->values();

        // Optional: Get combinations of Brand x City that have cars.
        $brandCityCombinations = [];
        $carCombos = Car::approved()->active()->whereNotNull('city')->select('brand_id', 'city')->distinct()->get();
        $listingCombos = CustomerCarListing::approved()->active()->whereNotNull('city')->select('brand_id', 'city')->distinct()->get();
        
        $allCombos = collect($carCombos)->concat($listingCombos)->unique(function ($item) {
            return $item['brand_id'].$item['city'];
        });

        foreach($allCombos as $combo) {
            $brand = $brands->firstWhere('id', $combo->brand_id);
            if($brand) {
                $brandCityCombinations[] = [
                    'brand_slug' => $brand->slug,
                    'city' => str_replace(' ', '-', strtolower($combo->city))
                ];
            }
        }

        $content = view('sitemap.index', compact('cars', 'customerListings', 'brands', 'cities', 'brandCityCombinations'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
            'charset' => 'utf-8',
        ]);
    }
}
