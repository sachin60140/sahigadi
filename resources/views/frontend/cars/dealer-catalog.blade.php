@extends('layouts.app')

@push('styles')
<style>
.dealer-profile-card .card {
    border-radius: 16px;
}
.dealer-profile-card .rounded-circle {
    background: linear-gradient(135deg, rgba(232, 84, 43, 0.1) 0%, rgba(232, 155, 43, 0.1) 100%);
}
</style>
@endpush

@section('title', $seoTitle)
@section('meta_description', $seoDescription)

@if(isset($ogImage) && $ogImage)
    @section('og_image', $ogImage)
@endif

@section('content')
<section class="py-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cars.index') }}" class="text-white text-decoration-none">Cars</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">{{ $dealer->name }}</li>
            </ol>
        </nav>
        <div class="text-white">
            <h1 class="fw-bold mb-2">{{ $dealer->name }}'s Car Listings</h1>
            <p class="mb-0">Browse {{ $cars->total() }} verified pre-owned cars from this dealer</p>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="dealer-profile-card mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill" style="font-size: 2.5rem; color: var(--accent);"></i>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $dealer->name }}</h5>
                        @if($dealer->company_name)
                            <p class="text-muted small mb-2">{{ $dealer->company_name }}</p>
                        @endif
                        <div class="d-flex flex-column gap-2 text-start mt-3">
                            @if($dealer->phone)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone text-accent me-2"></i>
                                <a href="tel:{{ $dealer->phone }}" class="text-decoration-none">{{ $dealer->phone }}</a>
                            </div>
                            @endif
                            @if($dealer->email)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-envelope text-accent me-2"></i>
                                <a href="mailto:{{ $dealer->email }}" class="text-decoration-none">{{ $dealer->email }}</a>
                            </div>
                            @endif
                            @if($dealer->city)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt text-accent me-2"></i>
                                <span>{{ $dealer->city }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <span class="badge bg-accent px-3 py-2">
                                <i class="bi bi-car-front me-1"></i>{{ $cars->total() }} Listings
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2"></i>SAHI GADI Verified Dealer</h6>
                <p class="small text-muted mb-0">All listings from this dealer are verified by SAHI GADI for quality and authenticity.</p>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-dark px-3 py-2">{{ $cars->total() }} Cars Found</span>
                </div>
                <select class="form-select w-auto" onchange="window.location.href=this.value" style="min-width: 200px;">
                    <option value="{{ route('dealer.catalog', $dealer->slug) }}" {{ !request('sort') ? 'selected' : '' }}>Sort by: Relevance</option>
                    <option value="{{ route('dealer.catalog', array_merge([$dealer->slug], request()->except('sort'), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="{{ route('dealer.catalog', array_merge([$dealer->slug], request()->except('sort'), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ route('dealer.catalog', array_merge([$dealer->slug], request()->except('sort'), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="{{ route('dealer.catalog', array_merge([$dealer->slug], request()->except('sort'), ['sort' => 'km_low'])) }}" {{ request('sort') == 'km_low' ? 'selected' : '' }}>KM: Low to High</option>
                </select>
            </div>

            <div class="row">
                @forelse($cars as $car)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            @if($car->image_url)
                                <img src="{{ $car->image_url }}" class="card-img-top" alt="{{ $car->title }}">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            @if($car->isFeatured())
                                <span class="position-absolute top-0 start-0 badge badge-featured m-2">
                                    <i class="bi bi-star-fill me-1"></i>Featured
                                </span>
                            @endif
                            <span class="position-absolute top-0 end-0 badge bg-dark text-white m-2">
                                {{ $car->year ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title fw-bold">{{ Str::limit($car->title, 30) }}</h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                                @if($car->km_driven)
                                <span><i class="bi bi-speedometer2 me-1"></i>{{ number_format($car->km_driven) }} km</span>
                                @endif
                                <span><i class="bi bi-gear me-1"></i>{{ ucfirst($car->transmission ?? 'N/A') }}</span>
                                <span><i class="bi bi-fuelPump me-1"></i>{{ ucfirst($car->fuel_type ?? 'N/A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">₹{{ number_format($car->price ?? 0) }}</span>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $car->city ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="{{ route('car.detail', $car->slug) }}" class="btn btn-outline-accent btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-car-front text-secondary" style="font-size: 5rem;"></i>
                    <h4 class="mt-3 text-secondary">No Cars Available</h4>
                    <p class="text-muted">This dealer currently has no active listings.</p>
                    <a href="{{ route('cars.index') }}" class="btn btn-accent">Browse All Cars</a>
                </div>
                @endforelse
            </div>

            @if($cars->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $cars->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
