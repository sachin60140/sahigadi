<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;

class CustomerCarListingController extends Controller
{
    private function buildFilterQuery(Request $request)
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

        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        return $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END")
                     ->orderBy('created_at', 'desc');
    }

    public function index(Request $request)
    {
        $query = $this->buildFilterQuery($request);
        $listings = $query->paginate(20);
        $pendingCount = CustomerCarListing::pending()->count();

        return view('admin.customer-listings.index', compact('listings', 'pendingCount'));
    }

    public function exportExcel(Request $request)
    {
        $query = CustomerCarListing::with('brand')->orderBy('created_at', 'desc');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomerListingsExport($query), 'customer-listings.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = CustomerCarListing::with('brand')->orderBy('created_at', 'desc');
        $listings = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.customer-listings.pdf', compact('listings'))->setPaper('a4', 'landscape');
        return $pdf->download('customer-listings.pdf');
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

        $data = $request->only([
            'title', 'brand_id', 'model', 'year', 'fuel_type', 'transmission',
            'km_driven', 'price', 'city', 'registration_number',
            'owners', 'owner_name', 'owner_phone', 'whatsapp_number', 'status',
        ]);

        $imagesArray = json_decode($listing->images, true) ?? [];

        if ($request->hasFile('images')) {
            $request->validate([
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            foreach ($request->file('images') as $image) {
                $filename = \Illuminate\Support\Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('customer-listings', $filename, 'public');
                
                try {
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($fullPath);
                    if ($img->width() > 800) {
                        $img->scaleDown(width: 800);
                        $img->save($fullPath, quality: 75);
                    }
                } catch (\Exception $e) {}

                $imagesArray[] = $path;
            }
        }

        if ($request->filled('primary_image')) {
            $primaryImage = $request->primary_image;
            if (in_array($primaryImage, $imagesArray)) {
                $imagesArray = array_values(array_diff($imagesArray, [$primaryImage]));
                array_unshift($imagesArray, $primaryImage);
            }
        }

        $data['images'] = json_encode(array_values($imagesArray));

        $listing->update($data);

        return redirect()->route('admin.customer-listings.index')->with('success', 'Listing updated successfully');
    }

    public function approve($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();
        $listing->update(['status' => 'approved']);

        $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
        if ($email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\UserListingStatusNotification($listing, 'approved'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

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

        $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
        if ($email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\UserListingStatusNotification($listing, 'rejected', $request->rejection_reason));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send rejection email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Listing rejected');
    }

    public function makeFeatured(Request $request, $customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();

        $request->validate([
            'days' => 'required|integer|in:7,14,30',
        ]);

        $featuredDealerCars = \App\Models\Car::featured()->count();
        $featuredCustomerCars = CustomerCarListing::where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_expires_at')
                    ->orWhere('featured_expires_at', '>', now());
            })->count();
            
        if (($featuredDealerCars + $featuredCustomerCars) >= 8) {
            return back()->with('error', 'Maximum 8 featured cars allowed across the platform. Please remove a featured car first.');
        }

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
        
        $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
        if ($email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\UserListingStatusNotification($listing, 'deleted'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send deletion email: ' . $e->getMessage());
            }
        }
        
        $listing->delete();

        return redirect()->route('admin.customer-listings.index')->with('success', 'Listing deleted successfully');
    }

    public function deleteImage($customer_listing, Request $request)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();
        
        $request->validate([
            'image' => 'required|string',
        ]);

        $imagePath = $request->image;
        $images = json_decode($listing->images, true) ?? [];

        if (in_array($imagePath, $images)) {
            $images = array_values(array_diff($images, [$imagePath]));
            $listing->update(['images' => json_encode($images)]);
            
            // Optional: delete from storage
            if (\Storage::disk('public')->exists($imagePath)) {
                \Storage::disk('public')->delete($imagePath);
            }

            return back()->with('success', 'Image deleted successfully');
        }

        return back()->with('error', 'Image not found');
    }
}
