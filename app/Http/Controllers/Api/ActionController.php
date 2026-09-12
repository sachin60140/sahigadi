<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerCarListing;
use App\Models\Enquiry;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ActionController extends Controller
{
    /**
     * Submit an enquiry for a car
     */
    public function createEnquiry(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
            'is_customer_listing' => 'required|boolean',
        ]);

        $user = $request->user();
        $isCustomerListing = $request->boolean('is_customer_listing');
        $isCustomer = $user->currentAccessToken()->can('role:customer');

        // Resolve the listing and work out who owns it, blocking self-enquiry.
        if ($isCustomerListing) {
            $listing = CustomerCarListing::findOrFail($request->car_id);

            if ($isCustomer && $listing->owner_phone === $user->phone) {
                return response()->json(['success' => false, 'message' => 'You cannot enquire about your own listing.'], 403);
            }

            // A null dealer_id is how the rest of the app marks an enquiry as
            // belonging to a customer listing: see Enquiry::getActualCarAttribute()
            // and the customer enquiry list, which both key off it.
            $dealerId = null;
        } else {
            $car = Car::findOrFail($request->car_id);

            if (! $isCustomer && $car->dealer_id === $user->id) {
                return response()->json(['success' => false, 'message' => 'You cannot enquire about your own listing.'], 403);
            }

            $dealerId = $car->dealer_id;
        }

        $enquiry = Enquiry::create([
            'car_id' => $request->car_id,
            'dealer_id' => $dealerId,
            'customer_name' => $user->name ?: ($user->company_name ?: 'User'),
            'customer_phone' => $user->phone,
            'customer_email' => $user->email,
            'message' => 'Enquiry sent from the SAHI GADI app.',
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Enquiry submitted successfully. The owner will be notified.',
            'data' => $enquiry
        ]);
    }

    /**
     * Create a Customer Car Listing
     */
    public function createCustomerListing(Request $request)
    {
        $user = $request->user();

        // Only customers can use this specific endpoint to create free listings
        if (!$user->currentAccessToken()->can('role:customer')) {
            return response()->json(['success' => false, 'message' => 'Only customers can create listings here.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:'.date('Y'),
            'fuel_type' => 'required|in:petrol,diesel,electric,hybrid,cng',
            'transmission' => 'required|in:manual,automatic',
            'km_driven' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'city' => 'required|string|max:100',
            'registration_number' => 'nullable|string|max:20',
            'owners' => 'required|integer|min:1|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        $data = $request->except(['images']);
        $data['owner_name'] = $user->name;
        $data['owner_phone'] = $user->phone;
        $data['owner_email'] = $user->email;
        $data['status'] = 'pending'; // Requires admin approval
        
        $baseSlug = Str::slug($request->title . ' ' . $request->year);
        $slug = $baseSlug;
        $counter = 1;
        while (CustomerCarListing::where('slug', $slug)->exists() || Car::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('customer_cars', 'public');
                $images[] = $path;
            }
        }
        $data['images'] = $images;

        $listing = CustomerCarListing::create($data);

        return response()->json([
            'success' => true, 
            'message' => 'Listing created successfully and is pending approval.',
            'data' => $listing
        ]);
    }
}
