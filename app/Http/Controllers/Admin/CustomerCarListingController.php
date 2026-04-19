<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;

class CustomerCarListingController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerCarListing::with('brand');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('model', 'like', '%'.$request->search.'%')
                    ->orWhere('owner_phone', 'like', '%'.$request->search.'%')
                    ->orWhere('whatsapp_number', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(20);
        $pendingCount = CustomerCarListing::pending()->count();

        return view('admin.customer-listings.index', compact('listings', 'pendingCount'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();

        return view('admin.customer-listings.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
        ]);

        CustomerCarListing::create($request->only([
            'title', 'brand_id', 'model', 'year', 'fuel_type', 'transmission',
            'km_driven', 'price', 'city', 'registration_number',
            'owners', 'owner_name', 'owner_phone', 'whatsapp_number', 'status',
        ]));

        return redirect()->route('admin.customer-listings.index')->with('success', 'Listing created successfully');
    }

    public function show($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->with('brand')->firstOrFail();

        return view('admin.customer-listings.show', compact('listing'));
    }

    public function edit($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();
        $brands = Brand::active()->orderBy('name')->get();

        return view('admin.customer-listings.edit', compact('listing', 'brands'));
    }

    public function update(Request $request, $customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
        ]);

        $listing->update($request->only([
            'title', 'brand_id', 'model', 'year', 'fuel_type', 'transmission',
            'km_driven', 'price', 'city', 'registration_number',
            'owners', 'owner_name', 'owner_phone', 'whatsapp_number', 'status',
        ]));

        return redirect()->route('admin.customer-listings.index')->with('success', 'Listing updated successfully');
    }

    public function approve($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();
        $listing->update(['status' => 'approved']);

        return back()->with('success', 'Listing approved successfully');
    }

    public function reject(Request $request, $customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $listing->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Listing rejected');
    }

    public function makeFeatured(Request $request, $customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();

        $request->validate([
            'days' => 'required|integer|in:7,14,30',
        ]);

        $listing->update([
            'is_featured' => true,
            'featured_expires_at' => now()->addDays((int) $request->days),
        ]);

        return back()->with('success', 'Listing marked as featured for '.$request->days.' days');
    }

    public function removeFeatured($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();

        $listing->update([
            'is_featured' => false,
            'featured_expires_at' => null,
        ]);

        return back()->with('success', 'Featured status removed');
    }

    public function destroy($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();
        $listing->delete();

        return redirect()->route('admin.customer-listings.index')->with('success', 'Listing deleted successfully');
    }
}
