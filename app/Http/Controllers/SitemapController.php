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

        $content = view('sitemap.index', compact('cars', 'customerListings', 'brands'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
            'charset' => 'utf-8',
        ]);
    }
}
