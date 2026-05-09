<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedPlan;
use Illuminate\Http\Request;

class FeaturedPlanController extends Controller
{
    public function index()
    {
        $plans = FeaturedPlan::orderBy('duration_days')->get();
        return view('admin.featured-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.featured-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        FeaturedPlan::create($validated);

        return redirect()->route('admin.featured-plans.index')
            ->with('success', 'Featured Plan created successfully.');
    }

    public function edit(FeaturedPlan $featuredPlan)
    {
        return view('admin.featured-plans.edit', compact('featuredPlan'));
    }

    public function update(Request $request, FeaturedPlan $featuredPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

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
}
