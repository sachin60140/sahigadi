<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedListing;
use App\Models\FeaturedPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeaturedPlanController extends Controller
{
    public function index()
    {
        $plans = FeaturedPlan::withCount([
            'featuredListings',
            'featuredListings as active_featured_listings_count' => fn ($query) => $query
                ->where('status', 'active')
                ->where('expires_at', '>', now()),
        ])->orderBy('duration_days')->get();

        return Inertia::render('Admin/FeaturedPlans/Index', [
            'plans' => $plans->map(fn (FeaturedPlan $plan) => $this->mapPlan($plan))->values(),
            'stats' => [
                'total' => $plans->count(),
                'active' => $plans->where('is_active', true)->count(),
                'inactive' => $plans->where('is_active', false)->count(),
                'live_promotions' => FeaturedListing::active()->count(),
            ],
            'actions' => [
                'create' => route('admin.featured-plans.create'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/FeaturedPlans/Create', [
            'actions' => [
                'store' => route('admin.featured-plans.store'),
                'back' => route('admin.featured-plans.index'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        FeaturedPlan::create($validated);

        return redirect()->route('admin.featured-plans.index')
            ->with('success', 'Featured Plan created successfully.');
    }

    public function edit(FeaturedPlan $featuredPlan)
    {
        $featuredPlan->loadCount([
            'featuredListings',
            'featuredListings as active_featured_listings_count' => fn ($query) => $query
                ->where('status', 'active')
                ->where('expires_at', '>', now()),
        ]);

        return Inertia::render('Admin/FeaturedPlans/Edit', [
            'plan' => $this->mapPlan($featuredPlan),
            'actions' => [
                'update' => route('admin.featured-plans.update', $featuredPlan),
                'back' => route('admin.featured-plans.index'),
            ],
        ]);
    }

    public function update(Request $request, FeaturedPlan $featuredPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $featuredPlan->update($validated);

        return redirect()->route('admin.featured-plans.index')
            ->with('success', 'Featured Plan updated successfully.');
    }

    public function destroy(FeaturedPlan $featuredPlan)
    {
        $featuredPlan->delete();

        return redirect()->route('admin.featured-plans.index')
            ->with('success', 'Featured Plan deleted successfully.');
    }

    private function mapPlan(FeaturedPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'duration_days' => (int) $plan->duration_days,
            'price' => (float) $plan->price,
            'is_active' => (bool) $plan->is_active,
            'featured_listings_count' => (int) ($plan->featured_listings_count ?? 0),
            'active_featured_listings_count' => (int) ($plan->active_featured_listings_count ?? 0),
            'actions' => [
                'edit' => route('admin.featured-plans.edit', $plan),
                'destroy' => route('admin.featured-plans.destroy', $plan),
            ],
        ];
    }
}
