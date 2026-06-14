<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with(['dealer', 'brand', 'images']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('model', 'like', '%'.$request->search.'%')
                    ->orWhere('registration_number', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $featuredPlans = \App\Models\FeaturedPlan::active()->orderBy('duration_days')->get();
        $cars = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Cars/Index', [
            'cars' => $cars->through(fn ($car) => $this->mapCarList($car)),
            'featuredPlans' => $featuredPlans->map(fn ($plan) => $this->mapFeaturedPlan($plan))->values(),
            'filters' => [
                'status' => $request->query('status', 'all'),
                'search' => $request->query('search', ''),
                'city' => $request->query('city', ''),
            ],
            'cities' => Car::query()
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->distinct()
                ->orderBy('city')
                ->pluck('city')
                ->values(),
            'stats' => [
                'total' => Car::count(),
                'approved' => Car::where('status', 'approved')->count(),
                'pending' => Car::where('status', 'pending')->count(),
                'rejected' => Car::where('status', 'rejected')->count(),
                'featured' => Car::where('is_featured', true)
                    ->where(fn ($query) => $query->whereNull('featured_expires_at')->orWhere('featured_expires_at', '>', now()))
                    ->count(),
            ],
        ]);
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $dealers = Dealer::orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return Inertia::render('Admin/Cars/Create', [
            'options' => $this->formOptions($brands, $dealers, $fuelTypes, $transmissions),
            'actions' => [
                'store' => route('admin.cars.store'),
                'back' => route('admin.cars.index'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'title' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,cng',
            'transmission' => 'nullable|in:manual,automatic',
            'km_driven' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:20',
            'owners' => 'nullable|integer|min:1|max:10',
            'status' => 'required|in:pending,approved,rejected',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $slug = Str::slug($request->title).'-'.Str::random(5);
        while (Car::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->title).'-'.Str::random(5);
        }

        $car = Car::create([
            'dealer_id' => $request->dealer_id,
            'brand_id' => $request->brand_id,
            'title' => $request->title,
            'slug' => $slug,
            'model' => $request->model,
            'year' => $request->year,
            'fuel_type' => $request->fuel_type,
            'transmission' => $request->transmission,
            'km_driven' => $request->km_driven,
            'price' => $request->price,
            'description' => $request->description,
            'city' => $request->city,
            'registration_number' => $request->registration_number,
            'owners' => $request->owners ?? 1,
            'status' => $request->status,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = 'cars/'.$car->id.'/'.$filename;

                \Storage::disk('public')->putFileAs('cars/'.$car->id, $image, $filename);

                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.cars.index')->with('success', 'Car added successfully!');
    }

    public function show(Car $car)
    {
        $car->load(['dealer', 'brand', 'images']);

        $featuredPlans = \App\Models\FeaturedPlan::active()->orderBy('duration_days')->get();

        return Inertia::render('Admin/Cars/Show', [
            'car' => $this->mapCarDetail($car),
            'featuredPlans' => $featuredPlans->map(fn ($plan) => $this->mapFeaturedPlan($plan))->values(),
        ]);
    }

    public function approve(Car $car)
    {
        $car->update(['status' => 'approved']);

        return back()->with('success', 'Car approved successfully');
    }

    public function reject(Request $request, Car $car)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $car->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Car rejected');
    }

    public function featured(Request $request, Car $car)
    {
        $days = $request->input('days', 7);

        $car->update([
            'is_featured' => true,
            'featured_expires_at' => now()->addDays($days),
        ]);

        $emailDetails = [
            'car_id' => $car->unique_id,
            'car_title' => $car->title,
            'plan_name' => "Admin Assigned ($days Days)",
            'duration_days' => $days,
            'amount_paid' => 0,
            'expires_at' => now()->addDays($days),
            'action_url' => route('dealer.cars.index')
        ];

        try {
            if ($car->dealer && $car->dealer->email) {
                $emailDetails['is_admin'] = false;
                $emailDetails['user_name'] = $car->dealer->name;
                \Illuminate\Support\Facades\Mail::to($car->dealer->email)->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
            }

            $emailDetails['is_admin'] = true;
            $emailDetails['user_name'] = 'Admin';
            \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\FeaturedPlanSubscribed($emailDetails));
        } catch (\Exception $e) {
            \Log::error('Failed to send featured plan subscription email: ' . $e->getMessage());
        }

        return back()->with('success', 'Car marked as featured');
    }

    public function removeFeatured(Car $car)
    {
        $car->update([
            'is_featured' => false,
            'featured_expires_at' => null,
        ]);

        return back()->with('success', 'Featured status removed');
    }

    public function edit(Car $car)
    {
        $car->load(['images']);
        $brands = Brand::active()->orderBy('name')->get();
        $dealers = Dealer::orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return Inertia::render('Admin/Cars/Edit', [
            'car' => $this->mapCarDetail($car),
            'options' => $this->formOptions($brands, $dealers, $fuelTypes, $transmissions),
            'actions' => [
                'update' => route('admin.cars.update', $car),
                'back' => route('admin.cars.show', $car),
                'index' => route('admin.cars.index'),
            ],
        ]);
    }

    public function update(Request $request, Car $car)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'title' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,cng',
            'transmission' => 'nullable|in:manual,automatic',
            'km_driven' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:20',
            'owners' => 'nullable|integer|min:1|max:10',
            'status' => 'required|in:pending,approved,rejected',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car->update($request->only([
            'dealer_id', 'title', 'brand_id', 'model', 'year', 'fuel_type', 'transmission',
            'km_driven', 'price', 'description', 'city', 'latitude', 'longitude', 'registration_number', 'owners', 'status',
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = 'cars/'.$car->id.'/'.$filename;

                \Storage::disk('public')->putFileAs('cars/'.$car->id, $image, $filename);

                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $car->images()->count() + $index,
                ]);
            }
        }

        return back()->with('success', 'Car updated successfully!');
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->route('admin.cars.index')->with('success', 'Car deleted successfully');
    }

    public function deleteImage(Car $car, CarImage $carImage)
    {
        if ($carImage->is_primary && $carImage->car->images()->count() > 1) {
            $nextImage = $carImage->car->images()->where('id', '!=', $carImage->id)->first();
            $nextImage->update(['is_primary' => true]);
        }

        $carImage->delete();

        return back()->with('success', 'Image deleted successfully');
    }

    public function setPrimaryImage(Car $car, CarImage $carImage)
    {
        $carImage->car->images()->update(['is_primary' => false]);
        $carImage->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set successfully');
    }

    private function formOptions($brands, $dealers, array $fuelTypes, array $transmissions): array
    {
        return [
            'brands' => $brands->map(fn ($brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
            ])->values(),
            'dealers' => $dealers->map(fn ($dealer) => [
                'id' => $dealer->id,
                'label' => trim($dealer->name.' ('.$dealer->email.')'),
                'name' => $dealer->name,
                'email' => $dealer->email,
            ])->values(),
            'fuelTypes' => collect($fuelTypes)->map(fn ($label, $value) => [
                'value' => $value,
                'label' => $label,
            ])->values(),
            'transmissions' => collect($transmissions)->map(fn ($label, $value) => [
                'value' => $value,
                'label' => $label,
            ])->values(),
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'approved', 'label' => 'Approved'],
                ['value' => 'rejected', 'label' => 'Rejected'],
            ],
        ];
    }

    private function mapCarList(Car $car): array
    {
        return [
            'id' => $car->id,
            'title' => $car->title,
            'unique_id' => $car->unique_id,
            'model' => $car->model,
            'brand' => $car->brand?->name,
            'year' => $car->year,
            'fuel_type' => $car->fuel_type,
            'transmission' => $car->transmission,
            'km_driven' => $car->km_driven,
            'price' => (float) ($car->price ?? 0),
            'city' => $car->city,
            'registration_number' => $car->registration_number,
            'owners' => $car->owners,
            'status' => $car->status,
            'rejection_reason' => $car->rejection_reason,
            'is_featured' => $car->isFeatured(),
            'featured_expires_at' => $this->formatDate($car->featured_expires_at),
            'paid_featured_active' => $car->featuredListings()->active()->exists(),
            'image_url' => $car->image_url,
            'latitude' => $car->latitude,
            'longitude' => $car->longitude,
            'map_url' => $car->latitude && $car->longitude ? 'https://www.google.com/maps?q='.$car->latitude.','.$car->longitude : null,
            'dealer' => [
                'id' => $car->dealer?->id,
                'name' => $car->dealer?->name,
                'email' => $car->dealer?->email,
                'phone' => $car->dealer?->phone,
                'show_url' => $car->dealer ? route('admin.dealers.show', $car->dealer) : null,
            ],
            'created_at' => $this->formatDate($car->created_at),
            'actions' => $this->carActions($car),
        ];
    }

    private function mapCarDetail(Car $car): array
    {
        return [
            ...$this->mapCarList($car),
            'dealer_id' => $car->dealer_id,
            'brand_id' => $car->brand_id,
            'description' => $car->description,
            'is_active' => (bool) $car->is_active,
            'images' => $car->images->map(fn ($image) => [
                'id' => $image->id,
                'url' => $image->url,
                'is_primary' => (bool) $image->is_primary,
                'sort_order' => $image->sort_order,
                'actions' => [
                    'delete' => route('admin.cars.image.delete', [$car, $image]),
                    'primary' => route('admin.cars.image.primary', [$car, $image]),
                ],
            ])->values(),
        ];
    }

    private function carActions(Car $car): array
    {
        return [
            'show' => route('admin.cars.show', $car),
            'edit' => route('admin.cars.edit', $car),
            'destroy' => route('admin.cars.destroy', $car),
            'approve' => route('admin.cars.approve', $car),
            'reject' => route('admin.cars.reject', $car),
            'featured' => route('admin.cars.featured', $car),
            'remove_featured' => route('admin.cars.remove-featured', $car),
        ];
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
}
