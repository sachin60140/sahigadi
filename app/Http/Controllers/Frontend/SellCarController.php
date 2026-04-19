<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SellCarController extends Controller
{
    public function index()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return view('frontend.sell-car.index', compact('brands', 'fuelTypes', 'transmissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,cng',
            'transmission' => 'nullable|in:manual,automatic',
            'km_driven' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'city' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:20',
            'owners' => 'nullable|integer|min:1|max:10',
            'owner_name' => 'nullable|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'latitude.required' => 'Location access is strictly required to list a vehicle. Please allow location permissions in your browser.',
            'longitude.required' => 'Location data is missing. Please ensure location services are enabled.',
        ]);

        if ($request->hasFile('images')) {
            if (count($request->file('images')) < 5) {
                return redirect()->back()->withInput()->with('error', 'Please upload at least 5 images.');
            }
            if (count($request->file('images')) > 10) {
                return redirect()->back()->withInput()->with('error', 'Maximum 10 images allowed.');
            }
        }

        $slug = Str::slug($request->title).'-'.Str::random(5);
        while (CustomerCarListing::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->title).'-'.Str::random(5);
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('customer-listings', $filename, 'public');
                $imagePaths[] = $path;
            }
        }

        CustomerCarListing::create([
            'title' => $request->title,
            'slug' => $slug,
            'brand_id' => $request->brand_id,
            'model' => $request->model,
            'year' => $request->year,
            'fuel_type' => $request->fuel_type,
            'transmission' => $request->transmission,
            'km_driven' => $request->km_driven,
            'price' => $request->price,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'registration_number' => $request->registration_number,
            'owners' => $request->owners ?? 1,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'whatsapp_number' => $request->whatsapp_number,
            'images' => json_encode($imagePaths),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your car listing has been submitted successfully! We will review it and get back to you soon.');
    }
}
