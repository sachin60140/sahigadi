@extends('layouts.dealer')

@section('title', 'Subscription Plans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subscription Plans</h2>
</div>

@if($currentPlan)
<div class="alert alert-info">
    <strong>Current Plan:</strong> {{ $currentPlan->plan->name }} 
    | <strong>Active Listings:</strong> {{ $currentPlan->getActiveListingsCount() }} / {{ $currentPlan->plan->listing_limit }}
    | <strong>Expires:</strong> {{ $currentPlan->expires_at->format('d M Y') }}
</div>
@else
<div class="alert alert-warning">
    You don't have an active subscription. Purchase a plan to start listing cars.
</div>
@endif

<div class="row">
    @foreach($plans as $plan)
    <div class="col-lg-4 mb-4">
        <div class="card h-100 {{ $currentPlan && $currentPlan->plan_id === $plan->id ? 'border-success' : '' }}">
            <div class="card-header bg-{{ $currentPlan && $currentPlan->plan_id === $plan->id ? 'success text-white' : 'light' }}">
                <h5 class="mb-0">{{ $plan->name }}</h5>
            </div>
            <div class="card-body text-center">
                <h2 class="text-primary">₹{{ number_format($plan->price) }}</h2>
                <p class="text-muted">{{ $plan->duration_days }} Days</p>
                <hr>
                <p><i class="bi bi-check-circle text-success"></i> {{ $plan->listing_limit }} Car Listings</p>
                <p><i class="bi bi-check-circle text-success"></i> {{ $plan->duration_days }} Days Validity</p>
                @if($plan->description)
                <p class="text-muted small">{{ $plan->description }}</p>
                @endif
            </div>
            <div class="card-footer bg-white border-top-0">
                @if($currentPlan && $currentPlan->plan_id === $plan->id)
                <button class="btn btn-success w-100" disabled>Current Plan</button>
                @else
                <a href="{{ route('dealer.plans.show', $plan) }}" class="btn btn-primary w-100">Purchase Plan</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
