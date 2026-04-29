<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarImage;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();
        $query = $dealer->cars()->with('images');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        $cars = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('dealer.cars.index', compact('cars'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return view('dealer.cars.create', compact('brands', 'fuelTypes', 'transmissions'));
    }

    public function store(Request $request)
    {
        $dealer = auth('dealer')->user();

        if (! $this->subscriptionService->canAddListing($dealer)) {
            return back()->with('error', 'You have reached your listing limit. Please purchase a plan.');
        }

        $request->validate([
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $slug = Str::slug($request->title).'-'.Str::random(5);
        while (Car::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->title).'-'.Str::random(5);
        }

        $car = Car::create([
            'dealer_id' => $dealer->id,
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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'registration_number' => $request->registration_number,
            'owners' => $request->owners ?? 1,
            'status' => 'pending',
        ]);

        $this->subscriptionService->incrementListingCount($dealer);

        if ($request->hasFile('images')) {
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            foreach ($request->file('images') as $index => $image) {
                $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = 'cars/'.$car->id.'/'.$filename;

                \Storage::disk('public')->putFileAs('cars/'.$car->id, $image, $filename);

                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $path,
                    'is_primary' => $index === $primaryIndex,
                    'sort_order' => $index,
                ]);
            }
        }

        try {
            \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\AdminNewListingNotification($car, true));
            if ($dealer->email) {
                \Illuminate\Support\Facades\Mail::to($dealer->email)->send(new \App\Mail\UserNewListingNotification($car));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send dealer car listing emails: ' . $e->getMessage());
        }

        return redirect()->route('dealer.cars.index')->with('success', 'Car listed successfully! It will be reviewed by admin.');
    }

    public function edit(Car $car)
    {
        $this->authorizeCarAccess($car);
        $brands = Brand::active()->orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return view('dealer.cars.edit', compact('car', 'brands', 'fuelTypes', 'transmissions'));
    }

    public function update(Request $request, Car $car)
    {
        $this->authorizeCarAccess($car);

        $request->validate([
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car->update($request->only([
            'title', 'brand_id', 'model', 'year', 'fuel_type', 'transmission',
            'km_driven', 'price', 'description', 'city', 'latitude', 'longitude', 'registration_number', 'owners',
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

        if ($car->status === 'rejected') {
            $car->update(['status' => 'pending']);
        }

        return back()->with('success', 'Car updated successfully!');
    }

    public function destroy(Car $car)
    {
        $this->authorizeCarAccess($car);
        $car->delete();

        return redirect()->route('dealer.cars.index')->with('success', 'Car deleted successfully');
    }

    public function deleteImage(Car $car, CarImage $carImage)
    {
        $this->authorizeCarAccess($carImage->car);

        if ($carImage->is_primary && $carImage->car->images()->count() > 1) {
            $nextImage = $carImage->car->images()->where('id', '!=', $carImage->id)->first();
            $nextImage->update(['is_primary' => true]);
        }

        $carImage->delete();

        return back()->with('success', 'Image deleted successfully');
    }

    public function setPrimaryImage(Car $car, CarImage $carImage)
    {
        $this->authorizeCarAccess($carImage->car);

        $carImage->car->images()->update(['is_primary' => false]);
        $carImage->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set successfully');
    }

    public function makeFeatured(Car $car, Request $request)
    {
        $this->authorizeCarAccess($car);

        $request->validate([
            'days' => 'required|integer|in:7,14,30',
        ]);

        $price = match ($request->days) {
            7 => 99,
            14 => 179,
            30 => 299,
            default => 99,
        };

        return redirect()->route('dealer.payments.checkout', [
            'type' => 'featured_listing',
            'car_id' => $car->id,
            'days' => $request->days,
            'amount' => $price,
        ]);
    }

    protected function authorizeCarAccess(Car $car): void
    {
        if ($car->dealer_id !== auth('dealer')->id()) {
            abort(403);
        }
    }
}
