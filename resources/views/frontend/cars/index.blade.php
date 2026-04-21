@extends('layouts.app')

@section('title', 'Browse Used Cars - SAHIGADI')
@section('meta_title', 'Browse Used Cars - Pre-Owned Cars Marketplace in Bihar | SAHIGADI')
@section('meta_description', 'Browse thousands of verified pre-owned cars in Patna and Bihar. Filter by brand, price, km driven, fuel type. Quality assured used cars from trusted dealers.')
@section('meta_keywords', 'browse used cars, pre-owned cars, car marketplace, filter cars, used cars Patna, used cars Bihar')
@section('canonical', route('cars.index'))

@section('content')
<section class="py-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2">Browse Used Cars</h1>
            <p class="mb-0">Find your perfect pre-owned car from our verified listings</p>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="filter-section">
                <h5 class="fw-bold mb-4"><i class="bi bi-funnel me-2"></i>Filter Cars</h5>
                <form action="{{ route('cars.index') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Car name, model...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">City</label>
                        <select name="city" class="form-select">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Brand</label>
                        <select name="brand" class="form-select">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Fuel Type</label>
                        <select name="fuel_type" class="form-select">
                            <option value="">All</option>
                            <option value="petrol" {{ request('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="diesel" {{ request('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ request('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="hybrid" {{ request('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="cng" {{ request('fuel_type') == 'cng' ? 'selected' : '' }}>CNG</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Price Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Transmission</label>
                        <select name="transmission" class="form-select">
                            <option value="">All</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-accent w-100 mb-2">
                        <i class="bi bi-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Clear Filters
                    </a>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-dark px-3 py-2">{{ $cars->total() + $customerListings->count() }} Cars Found</span>
                </div>
                <select class="form-select w-auto" onchange="window.location.href=this.value" style="min-width: 200px;">
                    <option value="{{ route('cars.index', array_merge(request()->except('sort'), ['sort' => ''])) }}" {{ !request('sort') ? 'selected' : '' }}>Sort by: Relevance</option>
                    <option value="{{ route('cars.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="{{ route('cars.index', array_merge(request()->except('sort'), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ route('cars.index', array_merge(request()->except('sort'), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="{{ route('cars.index', array_merge(request()->except('sort'), ['sort' => 'km_low'])) }}" {{ request('sort') == 'km_low' ? 'selected' : '' }}>KMs: Low to High</option>
                </select>
            </div>

            <div class="row">
                @foreach($cars as $car)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            @if($car->image_url)
                                <img src="{{ $car->image_url }}" class="card-img-top" alt="{{ $car->title }}" loading="lazy">
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
                                <div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $car->city ?? 'N/A' }}</small>
                                    @if($car->latitude && $car->longitude)
                                    <a href="https://www.google.com/maps?q={{ $car->latitude }},{{ $car->longitude }}" target="_blank" class="text-decoration-none small ms-1" title="View on Map">
                                        <i class="bi bi-map"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="{{ route('car.detail', $car->slug) }}" class="btn btn-outline-accent btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach

                @foreach($customerListings as $listing)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            @php
                                $images = json_decode($listing->images, true) ?? [];
                            @endphp
                            @if(count($images) > 0)
                                <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top" alt="{{ $listing->title }}" loading="lazy">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            <span class="position-absolute top-0 start-0 badge bg-info m-2">
                                <i class="bi bi-person me-1"></i>Owner Sale
                            </span>
                            <span class="position-absolute top-0 end-0 badge bg-dark text-white m-2">
                                {{ $listing->year ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title fw-bold">{{ Str::limit($listing->title, 30) }}</h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                                @if($listing->km_driven)
                                <span><i class="bi bi-speedometer2 me-1"></i>{{ number_format($listing->km_driven) }} km</span>
                                @endif
                                <span><i class="bi bi-gear me-1"></i>{{ ucfirst($listing->transmission ?? 'N/A') }}</span>
                                <span><i class="bi bi-fuelPump me-1"></i>{{ ucfirst($listing->fuel_type ?? 'N/A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">₹{{ number_format($listing->price ?? 0) }}</span>
                                <div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $listing->city ?? 'N/A' }}</small>
                                    @if($listing->latitude && $listing->longitude)
                                    <a href="https://www.google.com/maps?q={{ $listing->latitude }},{{ $listing->longitude }}" target="_blank" class="text-decoration-none small ms-1" title="View on Map">
                                        <i class="bi bi-map"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="{{ route('car.detail', $listing->slug) }}" class="btn btn-outline-accent btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($cars->total() + $customerListings->count() == 0)
            <div class="text-center py-5">
                <i class="bi bi-car-front text-secondary" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-secondary">No Cars Found</h4>
                <p class="text-muted">Try adjusting your filters</p>
                <a href="{{ route('cars.index') }}" class="btn btn-accent">Clear All Filters</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
