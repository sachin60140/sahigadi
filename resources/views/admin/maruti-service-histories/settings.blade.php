@extends('layouts.admin')

@section('title', 'Maruti Service History Settings - SAHIGADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-gear me-2"></i>Maruti Service History Settings</h2>
    <a href="{{ route('admin.maruti-service-histories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Charge Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.maruti-service-histories.settings') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Customer Charge (Rs.)</label>
                        <input type="number" name="charge" class="form-control" value="{{ $charge }}" required step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dealer Charge (Rs.)</label>
                        <input type="number" name="dealer_charge" class="form-control" value="{{ $dealerCharge }}" required step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Charges</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3>{{ $totalSearches }}</h3>
                        <p class="text-muted mb-0">Total Searches</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3>{{ $successfulSearches }}</h3>
                        <p class="text-muted mb-0">Successful</p>
                    </div>
                    <div class="col-12">
                        <h3 class="text-success">Rs.{{ number_format($totalRevenue) }}</h3>
                        <p class="text-muted mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection