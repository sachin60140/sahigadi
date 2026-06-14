<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount([
            'subscriptions',
            'subscriptions as active_subscriptions_count' => fn ($query) => $query
                ->where('is_active', true)
                ->where('expires_at', '>=', now()),
        ])->orderBy('price')->get();

        return Inertia::render('Admin/Plans/Index', [
            'plans' => $plans->map(fn (Plan $plan) => $this->mapPlan($plan))->values(),
            'stats' => [
                'total' => $plans->count(),
                'active' => $plans->where('is_active', true)->count(),
                'inactive' => $plans->where('is_active', false)->count(),
                'live_subscriptions' => Subscription::where('is_active', true)
                    ->where('expires_at', '>=', now())
                    ->count(),
            ],
            'actions' => [
                'create' => route('admin.plans.create'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Plans/Create', [
            'actions' => [
                'store' => route('admin.plans.store'),
                'back' => route('admin.plans.index'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'listing_limit' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully');
    }

    public function edit(Plan $plan)
    {
        $plan->loadCount([
            'subscriptions',
            'subscriptions as active_subscriptions_count' => fn ($query) => $query
                ->where('is_active', true)
                ->where('expires_at', '>=', now()),
        ]);

        return Inertia::render('Admin/Plans/Edit', [
            'plan' => $this->mapPlan($plan),
            'actions' => [
                'update' => route('admin.plans.update', $plan),
                'back' => route('admin.plans.index'),
            ],
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'listing_limit' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully');
    }

    private function mapPlan(Plan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'listing_limit' => (int) $plan->listing_limit,
            'duration_days' => (int) $plan->duration_days,
            'description' => $plan->description,
            'is_active' => (bool) $plan->is_active,
            'subscriptions_count' => (int) ($plan->subscriptions_count ?? 0),
            'active_subscriptions_count' => (int) ($plan->active_subscriptions_count ?? 0),
            'actions' => [
                'edit' => route('admin.plans.edit', $plan),
                'destroy' => route('admin.plans.destroy', $plan),
            ],
        ];
    }
}
