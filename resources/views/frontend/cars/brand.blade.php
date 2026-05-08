@extends('layouts.app')

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@section('og_type', 'website')
@section('og_url', url()->current())
@section('og_title', $seoTitle)
@section('og_description', $seoDescription)
@section('twitter_title', $seoTitle)
@section('twitter_description', $seoDescription)

@if(isset($ogImage) && $ogImage)
    @section('og_image', $ogImage)
@endif

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Used {{ $brandName }} Cars{{ $cityName ? ' in ' . $cityName : '' }}</h1>
    <p class="text-muted mb-4">{{ $allCars->count() }} cars available</p>

    <div class="row">
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Filter Cars</h6>
                </div>
                <div class="card-body">
                    <form action="{{ $city ? route('cars.brand.city', [$brand, $city]) : route('cars.brand', $brand) }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row">
                @forelse($allCars as $item)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            @if($item instanceof \App\Models\Car && $item->image_url)
                                <img src="{{ $item->image_url }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $item->title }}" loading="lazy">
                            @elseif($item instanceof \App\Models\CustomerCarListing)
                                @php
                                    $images = is_string($item->images) ? json_decode($item->images, true) : $item->images;
                                @endphp
                                @if($images && count($images) > 0)
                                    <img src="{{ asset('storage/'.$images[0]) }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $item->title }}" loading="lazy">
                                @else
                                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="bi bi-car-front text-white" style="font-size: 4rem;"></i>
                                    </div>
                                @endif
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="bi bi-car-front text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($item->title, 35) }}</h6>
                            <p class="card-text text-muted small mb-1">
                                @if($item->year){{ $item->year }} | @endif
                                @if($item->km_driven){{ number_format($item->km_driven) }} km | @endif
                                {{ ucfirst($item->fuel_type ?? '') }}
                            </p>
                            <h5 class="text-primary fw-bold">₹{{ number_format($item->price ?? 0) }}</h5>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $item->city ?? 'N/A' }}</small>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('car.detail', $item->slug) }}" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-car-front" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No {{ $brandName }} cars found{{ $cityName ? ' in ' . $cityName : '' }}.</p>
                </div>
                @endforelse
            </div>

            <!-- SEO Content Block -->
            <div class="mt-5 p-4 bg-light rounded shadow-sm border">
                <h2 class="h4 fw-bold text-dark mb-3">Buy Used {{ $brandName }} Cars{{ $cityName ? ' in ' . $cityName : '' }}</h2>
                <p class="text-muted">
                    Searching for reliable <strong>used {{ $brandName }} cars{{ $cityName ? ' in ' . $cityName : '' }}</strong>? Your search ends here. SAHI GADI brings to you an exclusively verified collection of pre-owned {{ $brandName }} vehicles. Known for their build quality, reliable engines, and great resale value, {{ $brandName }} cars are a favorite among buyers. 
                </p>
                <p class="text-muted mb-0">
                    Filter your search by price, model, and year to find the perfect <strong>second hand {{ $brandName }} car{{ $cityName ? ' in ' . $cityName : '' }}</strong>. We make sure every listing passes thorough inspections, ensuring our buyers get nothing but the best. Skip the hassle of unverified sellers and grab the best deals with easy RC transfer and financing support today.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection