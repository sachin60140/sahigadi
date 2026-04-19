@extends('layouts.admin')

@push('styles')
<style>
.kpi-card .kpi-icon.danger { background: rgba(220, 53, 69, 0.1); color: var(--danger); }
</style>
@endpush

@section('title', 'Dashboard - SAHIGADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-grid-1x2 me-2"></i>Dashboard Overview</h4>
        <small class="text-muted">Welcome back! Here's what's happening today.</small>
    </div>
    <div>
        <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ now()->format('d M Y') }}</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="kpi-label mb-1">Pending Dealers</p>
                    <h2 class="kpi-value mb-0">{{ $stats['pending_dealers'] }}</h2>
                </div>
                <div class="kpi-icon warning">
                    <i class="bi bi-person-dash"></i>
                </div>
            </div>
            <a href="{{ route('admin.dealers.index') }}?status=pending" class="btn btn-sm btn-outline-warning mt-3">
                Review Now <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="kpi-label mb-1">Total Dealers</p>
                    <h2 class="kpi-value mb-0">{{ $stats['total_dealers'] }}</h2>
                </div>
                <div class="kpi-icon primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <a href="{{ route('admin.dealers.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="kpi-label mb-1">Pending Cars</p>
                    <h2 class="kpi-value mb-0">{{ $stats['pending_cars'] }}</h2>
                </div>
                <div class="kpi-icon danger">
                    <i class="bi bi-car-front-fill"></i>
                </div>
            </div>
            <a href="{{ route('admin.cars.index') }}?status=pending" class="btn btn-sm btn-outline-danger mt-3">
                Review Now <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="kpi-label mb-1">Total Cars</p>
                    <h2 class="kpi-value mb-0">{{ $stats['total_cars'] }}</h2>
                </div>
                <div class="kpi-icon success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-sm btn-outline-success mt-3">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="kpi-label mb-1">Pending Customer Listings</p>
                    <h2 class="kpi-value mb-0">{{ $stats['pending_customer_listings'] }}</h2>
                </div>
                <div class="kpi-icon warning">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
            <a href="{{ route('admin.customer-listings.index') }}?status=pending" class="btn btn-sm btn-outline-warning mt-3">
                Review Now <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="kpi-label mb-1">Total Customer Listings</p>
                    <h2 class="kpi-value mb-0">{{ $stats['total_customer_listings'] }}</h2>
                </div>
                <div class="kpi-icon info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <a href="{{ route('admin.customer-listings.index') }}" class="btn btn-sm btn-outline-info mt-3">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="stat-card">
            <h5 class="mb-4"><i class="bi bi-graph-up me-2"></i>Quick Actions</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('admin.dealers.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-0">Manage Dealers</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.cars.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-car-front-fill text-danger" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-0">Manage Cars</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.plans.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-box-seam-fill text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-0">Manage Plans</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.customer-listings.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-person-badge text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-0">Customer Listings</h6>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat-card">
            <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>System Info</h5>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Customer Listings</span>
                <span class="fw-bold">{{ $stats['total_customer_listings'] }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Plans</span>
                <span class="fw-bold">{{ $stats['total_plans'] }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">App Version</span>
                <span class="fw-bold">1.0.0</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">PHP Version</span>
                <span class="fw-bold">{{ PHP_VERSION }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
