<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CustomerCarListingController extends Controller
{
    private function buildFilterQuery(Request $request)
    {
        $query = CustomerCarListing::with('brand');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('model', 'like', '%'.$request->search.'%')
                    ->orWhere('owner_phone', 'like', '%'.$request->search.'%')
                    ->orWhere('whatsapp_number', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('city')) {
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
        $featuredPlans = \App\Models\FeaturedPlan::active()->orderBy('duration_days')->get();

        return Inertia::render('Admin/CustomerListings/Index', [
            'listings' => $listings->through(fn ($listing) => $this->mapListingList($listing)),
            'pendingCount' => $pendingCount,
            'featuredPlans' => $featuredPlans->map(fn ($plan) => $this->mapFeaturedPlan($plan))->values(),
            'filters' => [
                'status' => $request->query('status', 'all'),
                'date' => $request->query('date', ''),
                'city' => $request->query('city', ''),
                'search' => $request->query('search', ''),
            ],
            'cities' => CustomerCarListing::query()
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->distinct()
                ->orderBy('city')
                ->pluck('city')
                ->values(),
            'stats' => [
                'total' => CustomerCarListing::count(),
                'pending' => $pendingCount,
                'approved' => CustomerCarListing::where('status', 'approved')->count(),
                'rejected' => CustomerCarListing::where('status', 'rejected')->count(),
                'featured' => CustomerCarListing::where('is_featured', true)
                    ->where(fn ($query) => $query->whereNull('featured_expires_at')->orWhere('featured_expires_at', '>', now()))
                    ->count(),
            ],
            'exportUrls' => [
                'excel' => route('admin.customer-listings.exportExcel'),
                'pdf' => route('admin.customer-listings.exportPdf'),
            ],
        ]);
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

        return Inertia::render('Admin/CustomerListings/Create', [
            'options' => $this->formOptions($brands),
            'actions' => [
                'store' => route('admin.customer-listings.store'),
                'back' => route('admin.customer-listings.index'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'title', 'brand_id', 'model', 'year', 'fuel_type', 'transmission',
            'km_driven', 'price', 'city', 'registration_number',
            'owners', 'owner_name', 'owner_phone', 'whatsapp_number', 'status',
        ]);

        $data['images'] = json_encode($this->storeImages($request));

        CustomerCarListing::create($data);

        return redirect()->route('admin.customer-listings.index')->with('success', 'Listing created successfully');
    }

    public function show($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->with('brand')->firstOrFail();

        $featuredPlans = \App\Models\FeaturedPlan::active()->orderBy('duration_days')->get();

        return Inertia::render('Admin/CustomerListings/Show', [
            'listing' => $this->mapListingDetail($listing),
            'featuredPlans' => $featuredPlans->map(fn ($plan) => $this->mapFeaturedPlan($plan))->values(),
        ]);
    }

    public function edit($customer_listing)
    {
        $listing = CustomerCarListing::where('slug', $customer_listing)->firstOrFail();
        $brands = Brand::active()->orderBy('name')->get();

        return Inertia::render('Admin/CustomerListings/Edit', [
            'listing' => $this->mapListingDetail($listing),
            'options' => $this->formOptions($brands),
            'actions' => [
                'update' => route('admin.customer-listings.update', $listing),
                'back' => route('admin.customer-listings.show', $listing),
                'index' => route('admin.customer-listings.index'),
            ],
        ]);
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
                $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('customer-listings', $filename, 'public');
                
                try {
                    $fullPath = Storage::disk('public')->path($path);
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

        $emailDetails = [
            'car_id' => $listing->unique_id,
            'car_title' => $listing->title,
            'plan_name' => "Admin Assigned (".$request->days." Days)",
            'duration_days' => $request->days,
            'amount_paid' => 0,
            'expires_at' => now()->addDays((int) $request->days),
            'action_url' => route('customer.dashboard')
        ];

        try {
            $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
            $customerName = $listing->owner_name ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('name');
            
            if ($email) {
                $emailDetails['is_admin'] = false;
                $emailDetails['user_name'] = $customerName ?? 'Customer';
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
            }

            $emailDetails['is_admin'] = true;
            $emailDetails['user_name'] = 'Admin';
            \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
        } catch (\Exception $e) {
            \Log::error('Failed to send featured plan subscription email: ' . $e->getMessage());
        }

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
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->with('success', 'Image deleted successfully');
        }

        return back()->with('error', 'Image not found');
    }

    private function formOptions($brands): array
    {
        return [
            'brands' => $brands->map(fn ($brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
            ])->values(),
            'fuelTypes' => [
                ['value' => 'petrol', 'label' => 'Petrol'],
                ['value' => 'diesel', 'label' => 'Diesel'],
                ['value' => 'electric', 'label' => 'Electric'],
                ['value' => 'hybrid', 'label' => 'Hybrid'],
                ['value' => 'cng', 'label' => 'CNG'],
            ],
            'transmissions' => [
                ['value' => 'manual', 'label' => 'Manual'],
                ['value' => 'automatic', 'label' => 'Automatic'],
            ],
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'approved', 'label' => 'Approved'],
                ['value' => 'rejected', 'label' => 'Rejected'],
            ],
        ];
    }

    private function mapListingList(CustomerCarListing $listing): array
    {
        $images = $this->listingImages($listing);

        return [
            'id' => $listing->id,
            'slug' => $listing->slug,
            'unique_id' => $listing->unique_id,
            'title' => $listing->title,
            'brand' => $listing->brand?->name,
            'model' => $listing->model,
            'year' => $listing->year,
            'fuel_type' => $listing->fuel_type,
            'transmission' => $listing->transmission,
            'km_driven' => $listing->km_driven,
            'price' => (float) ($listing->price ?? 0),
            'city' => $listing->city,
            'registration_number' => $listing->registration_number,
            'owners' => $listing->owners,
            'owner_name' => $listing->owner_name,
            'owner_phone' => $listing->owner_phone,
            'whatsapp_number' => $listing->whatsapp_number,
            'status' => $listing->status,
            'rejection_reason' => $listing->rejection_reason,
            'is_featured' => $listing->isFeatured(),
            'featured_expires_at' => $this->formatDate($listing->featured_expires_at),
            'paid_featured_active' => $listing->featuredListings()->active()->exists(),
            'image_url' => $images[0]['url'] ?? null,
            'image_count' => count($images),
            'latitude' => $listing->latitude,
            'longitude' => $listing->longitude,
            'map_url' => $listing->latitude && $listing->longitude ? 'https://www.google.com/maps?q='.$listing->latitude.','.$listing->longitude : null,
            'created_at' => $this->formatDateTime($listing->created_at),
            'actions' => $this->listingActions($listing),
        ];
    }

    private function mapListingDetail(CustomerCarListing $listing): array
    {
        return [
            ...$this->mapListingList($listing),
            'brand_id' => $listing->brand_id,
            'images' => $this->listingImages($listing),
        ];
    }

    private function listingActions(CustomerCarListing $listing): array
    {
        return [
            'show' => route('admin.customer-listings.show', $listing),
            'edit' => route('admin.customer-listings.edit', $listing),
            'destroy' => route('admin.customer-listings.destroy', $listing),
            'approve' => route('admin.customer-listings.approve', $listing),
            'reject' => route('admin.customer-listings.reject', $listing),
            'featured' => route('admin.customer-listings.featured', $listing),
            'remove_featured' => route('admin.customer-listings.remove-featured', $listing),
            'delete_image' => route('admin.customer-listings.image.delete', $listing),
        ];
    }

    private function listingImages(CustomerCarListing $listing): array
    {
        $images = json_decode($listing->images, true) ?: [];

        return collect($images)->map(fn ($path, $index) => [
            'path' => $path,
            'url' => $path ? asset('storage/'.$path) : null,
            'is_primary' => $index === 0,
        ])->values()->all();
    }

    private function storeImages(Request $request): array
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        $paths = [];
        foreach ($request->file('images') as $image) {
            $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
            $paths[] = $image->storeAs('customer-listings', $filename, 'public');
        }

        return $paths;
    }

    private function mapFeaturedPlan($plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'duration_days' => (int) $plan->duration_days,
            'price' => (float) ($plan->price ?? 0),
        ];
    }

    private function formatDate($value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d M Y');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function formatDateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d M Y, h:i A');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
