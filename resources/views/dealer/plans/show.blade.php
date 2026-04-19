@extends('layouts.dealer')

@section('title', $plan->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Plan Details</h2>
    <a href="{{ route('dealer.plans.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $plan->name }}</h5>
            </div>
            <div class="card-body">
                @if($plan->price == 0)
                <h3 class="text-success">FREE</h3>
                @else
                <h3 class="text-primary">₹{{ number_format($plan->price) }}</h3>
                @endif
                <p class="text-muted">{{ $plan->duration_days }} Days</p>
                <hr>
                <p><i class="bi bi-check-circle text-success"></i> {{ $plan->listing_limit }} Car Listings</p>
                <p><i class="bi bi-check-circle text-success"></i> {{ $plan->duration_days }} Days Validity</p>
                @if($plan->description)
                <p class="text-muted">{{ $plan->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $plan->price == 0 ? 'Activate Free Plan' : 'Payment Summary' }}</h5>
            </div>
            <div class="card-body">
                @if($plan->price == 0)
                <form action="{{ route('dealer.plans.purchase', $plan) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i> Activate Free Plan
                    </button>
                </form>
                @else
                <table class="table">
                    <tr>
                        <td>Plan Price</td>
                        <td class="text-end">₹{{ number_format($plan->price) }}</td>
                    </tr>
                    <tr>
                        <td>Wallet Balance</td>
                        <td class="text-end">₹{{ number_format($balance, 2) }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Balance After Purchase</td>
                        <td class="text-end">₹{{ number_format($balance - $plan->price, 2) }}</td>
                    </tr>
                </table>
                
                @if($balance >= $plan->price)
                <form action="{{ route('dealer.plans.purchase', $plan) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Confirm Purchase
                    </button>
                </form>
                @else
                <div class="alert alert-danger">
                    Insufficient wallet balance. 
                    <a href="{{ route('dealer.wallet.add') }}" class="alert-link">Add Funds</a>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
