@extends('layouts.app')

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@section('og_type', 'website')
@section('og_url', url()->current())
@section('og_title', $seoTitle)
@section('og_description', $seoDescription)
@section('twitter_title', $seoTitle)
@section('twitter_description', $seoDescription)

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Used Cars in {{ $cityName }}</h1>
    <p class="text-muted mb-4">{{ $cars->total() }} cars available</p>

    <div class="row">
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Filter Cars</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cars.city', $city) }}" method="GET">
                        @if(request('brand'))<input type="hidden" name="brand" value="{{ request('brand') }}">@endif
                        @if(request('min_price'))<input type="hidden" name="min_price" value="{{ request('min_price') }}">@endif
                        @if(request('max_price'))<input type="hidden" name="max_price" value="{{ request('max_price') }}">@endif
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <select name="brand" class="form-select">
                                <option value="">All Brands</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" {{ request('brand') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                @forelse($cars as $car)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            @if($car->image_url)
                                <img src="{{ $car->image_url }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $car->title }}">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="bi bi-car-front text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($car->title, 35) }}</h6>
                            <p class="card-text text-muted small mb-1">
                                @if($car->year){{ $car->year }} | @endif
                                @if($car->km_driven){{ number_format($car->km_driven) }} km | @endif
                                {{ ucfirst($car->fuel_type ?? '') }}
                            </p>
                            <h5 class="text-primary fw-bold">₹{{ number_format($car->price ?? 0) }}</h5>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('car.detail', $car->slug) }}" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-car-front" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No cars found in {{ $cityName }}.</p>
                </div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $cars->withQueryString()->links() }}
            </div>

            <!-- SEO Content Block -->
            <div class="mt-5 p-4 bg-light rounded shadow-sm border">
                <h2 class="h4 fw-bold text-dark mb-3">Buy Used Cars in {{ $cityName }}</h2>
                <p class="text-muted">
                    Looking for the best deals on <strong>used cars in {{ $cityName }}</strong>? At SAHIGADI, we offer a wide range of verified second hand cars tailored to your budget and lifestyle. Whether you need a compact hatchback for daily commutes, a premium sedan, or a sturdy SUV for family trips, our extensive inventory has you covered. 
                </p>
                <p class="text-muted mb-0">
                    Buying a <strong>second hand car in {{ $cityName }}</strong> has never been easier. We ensure every vehicle passes stringent quality checks. Enjoy transparent pricing, easy financing options, and top-notch customer support. Start browsing to find the perfect pre-owned car that matches your needs today!
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
