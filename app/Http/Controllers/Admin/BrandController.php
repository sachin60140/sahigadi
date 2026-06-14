<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount(['cars', 'customerListings'])->orderBy('name')->get();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands->map(fn (Brand $brand) => $this->mapBrand($brand))->values(),
            'stats' => [
                'total' => $brands->count(),
                'active' => $brands->where('is_active', true)->count(),
                'inactive' => $brands->where('is_active', false)->count(),
                'vehicle_links' => $brands->sum(fn (Brand $brand) => $brand->cars_count + $brand->customer_listings_count),
            ],
            'actions' => [
                'create' => route('admin.brands.create'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Brands/Create', [
            'actions' => [
                'store' => route('admin.brands.store'),
                'back' => route('admin.brands.index'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $data['logo'] = $path;
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully');
    }

    public function edit(Brand $brand)
    {
        $brand->loadCount(['cars', 'customerListings']);

        return Inertia::render('Admin/Brands/Edit', [
            'brand' => $this->mapBrand($brand),
            'actions' => [
                'update' => route('admin.brands.update', $brand),
                'back' => route('admin.brands.index'),
            ],
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,'.$brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $data['logo'] = $path;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully');
    }

    private function mapBrand(Brand $brand): array
    {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'logo_url' => $brand->logo ? asset('storage/'.$brand->logo) : null,
            'is_active' => (bool) $brand->is_active,
            'cars_count' => (int) ($brand->cars_count ?? 0),
            'customer_listings_count' => (int) ($brand->customer_listings_count ?? 0),
            'inventory_count' => (int) (($brand->cars_count ?? 0) + ($brand->customer_listings_count ?? 0)),
            'actions' => [
                'edit' => route('admin.brands.edit', $brand),
                'destroy' => route('admin.brands.destroy', $brand),
            ],
        ];
    }
}
