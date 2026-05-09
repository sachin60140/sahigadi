<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with(['dealer', 'brand', 'images']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('model', 'like', '%'.$request->search.'%')
                    ->orWhere('registration_number', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        $cars = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.cars.index', compact('cars'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $dealers = Dealer::orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return view('admin.cars.create', compact('brands', 'dealers', 'fuelTypes', 'transmissions'));
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

        return view('admin.cars.show', compact('car'));
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

    public function featured(Car $car)
    {
        $featuredDealerCars = Car::featured()->count();
        $featuredCustomerCars = \App\Models\CustomerCarListing::where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_expires_at')
                    ->orWhere('featured_expires_at', '>', now());
            })->count();
            
        if (($featuredDealerCars + $featuredCustomerCars) >= 8) {
            return back()->with('error', 'Maximum 8 featured cars allowed across the platform. Please remove a featured car first.');
        }

        $car->update([
            'is_featured' => true,
            'featured_expires_at' => now()->addDays(7),
        ]);

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

        return view('admin.cars.edit', compact('car', 'brands', 'dealers', 'fuelTypes', 'transmissions'));
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
}
