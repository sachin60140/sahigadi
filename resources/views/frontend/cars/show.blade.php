@extends('layouts.app')

@push('styles')
<style>
.text-accent { color: #e94560 !important; }
.spec-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}
.spec-item i {
    font-size: 1.5rem;
    color: #e94560;
}
.img-thumbnail {
    transition: all 0.3s;
}
.img-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
</style>
@endpush

@php
$item = $car ?? $customerListing;
$totalImages = count($allImages);
@endphp

@section('title', $seo['seo_title'])
@section('meta_description', $seo['meta_description'])
@section('meta_keywords', $seo['meta_keywords'])
@section('canonical', route('car.detail', $item->slug))
@section('og_type', 'product')
@section('og_url', route('car.detail', $item->slug))
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_image', $firstImage)
@section('twitter_title', $seo['og_title'])
@section('twitter_description', $seo['og_description'])

@once
@section('json_ld')
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
@endonce

@section('content')
<section class="py-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cars.index') }}" class="text-white text-decoration-none">Cars</a></li>
                <li class="breadcrumb-item text-white-50 active" aria-current="page">{{ Str::limit($item->title, 30) }}</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                @if($totalImages > 0)
                <div id="carCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                    <div class="carousel-inner">
                        @foreach($allImages as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ $image }}" 
                                 class="d-block w-100" 
                                 style="max-height: 500px; object-fit: cover;" 
                                 alt="Car Image {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    @if($totalImages > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
                @if($totalImages > 1)
                <div class="card-body pb-0">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($allImages as $index => $image)
                        <img src="{{ $image }}" 
                             class="img-thumbnail {{ $index === 0 ? 'border-primary' : '' }}" 
                             style="width: 100px; height: 70px; object-fit: cover; cursor: pointer;" 
                             alt="Thumbnail {{ $index + 1 }}"
                             onclick="setActiveSlide({{ $index }})">
                        @endforeach
                    </div>
                </div>
                @endif
                @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="bi bi-car-front text-secondary" style="font-size: 8rem;"></i>
                </div>
                @endif
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ $item->title }}</h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt me-1"></i>{{ $item->city ?? 'N/A' }}
                            @if($item->brand)
                                | <i class="bi bi-tag me-1"></i>{{ $item->brand->name }}
                            @endif
                        </p>
                    </div>
                    @if($isCustomerListing)
                        <span class="badge bg-info"><i class="bi bi-person me-1"></i>Owner Sale</span>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Car Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-currency-rupee"></i>
                                <div>
                                    <small class="text-muted">Price</small>
                                    <h5 class="mb-0 text-accent fw-bold">₹{{ number_format($item->price ?? 0) }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-calendar3"></i>
                                <div>
                                    <small class="text-muted">Year</small>
                                    <h6 class="mb-0">{{ $item->year ?? 'N/A' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-speedometer2"></i>
                                <div>
                                    <small class="text-muted">KM Driven</small>
                                    <h6 class="mb-0">{{ $item->km_driven ? number_format($item->km_driven) . ' km' : 'N/A' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-fuelPump"></i>
                                <div>
                                    <small class="text-muted">Fuel Type</small>
                                    <h6 class="mb-0">{{ ucfirst($item->fuel_type ?? 'N/A') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-gear"></i>
                                <div>
                                    <small class="text-muted">Transmission</small>
                                    <h6 class="mb-0">{{ ucfirst($item->transmission ?? 'N/A') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-people"></i>
                                <div>
                                    <small class="text-muted">Owners</small>
                                    <h6 class="mb-0">{{ $item->owners ?? 1 }} Owner(s)</h6>
                                </div>
                            </div>
                        </div>
                        @if($item->registration_number)
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-card-text"></i>
                                <div>
                                    <small class="text-muted">Reg. Number</small>
                                    <h6 class="mb-0">{{ substr($item->registration_number, 0, 4) }}****</h6>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($item->model)
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-car-front"></i>
                                <div>
                                    <small class="text-muted">Model</small>
                                    <h6 class="mb-0">{{ $item->model }}</h6>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($isCustomerListing)
            <div class="card mb-4 sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header text-white" style="background: #e94560;">
                    <h4 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>{{ number_format($item->price ?? 0) }}</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-person me-2"></i>Owner Information</h5>
                    <div class="dealer-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="bi bi-person-fill" style="font-size: 2rem; color: #e94560;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $item->owner_name ?? 'Owner' }}</h6>
                            </div>
                        </div>
                        @if($item->owner_phone)
                        <p class="mb-2"><i class="bi bi-telephone me-2" style="color: #e94560;"></i>{{ $item->owner_phone }}</p>
                        @endif
                        @if($item->whatsapp_number)
                        <p class="mb-2"><i class="bi bi-whatsapp me-2 text-success"></i>{{ $item->whatsapp_number }}</p>
                        @endif
                        @if($item->city)
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" style="color: #e94560;"></i>{{ $item->city }}</p>
                        @endif
                    </div>
                    @if($item->owner_phone)
                    <a href="tel:{{ $item->owner_phone }}" class="btn w-100 py-3 mb-3" style="background: #e94560; color: white;">
                        <i class="bi bi-telephone me-2"></i>Call Owner
                    </a>
                    @endif
                    @if($item->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->whatsapp_number) }}" target="_blank" class="btn btn-success w-100 py-3">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4 sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header text-white" style="background: #e94560;">
                    <h4 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>{{ number_format($car->price ?? 0) }}</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-person-badge me-2"></i>Dealer Information</h5>
                    <div class="dealer-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="bi bi-person-fill" style="font-size: 2rem; color: #e94560;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $car->dealer->name }}</h6>
                                @if($car->dealer->company_name)
                                <small class="text-muted">{{ $car->dealer->company_name }}</small>
                                @endif
                            </div>
                        </div>
                        @if($car->dealer->phone)
                        <p class="mb-2"><i class="bi bi-telephone me-2" style="color: #e94560;"></i>{{ $car->dealer->phone }}</p>
                        @endif
                        @if($car->dealer->city)
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" style="color: #e94560;"></i>{{ $car->dealer->city }}</p>
                        @endif
                    </div>
                    <button class="btn w-100 py-3 mb-3" style="background: #e94560; color: white;" data-bs-toggle="modal" data-bs-target="#enquiryModal">
                        <i class="bi bi-chat-dots me-2"></i>Send Enquiry
                    </button>
                    <a href="tel:{{ $car->dealer->phone }}" class="btn btn-outline-secondary w-100 py-3">
                        <i class="bi bi-telephone me-2"></i>Call Dealer
                    </a>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check" style="font-size: 3rem; color: #e94560;"></i>
                    <h6 class="mt-2 mb-2">SAHIGADI Verified</h6>
                    <small class="text-muted">This listing is verified by SAHIGADI</small>
                </div>
            </div>
        </div>
    </div>

    @if($relatedCars->count() > 0)
    <section class="mt-5">
        <h4 class="mb-4"><i class="bi bi-grid me-2"></i>Related Cars</h4>
        <div class="row">
            @foreach($relatedCars as $relatedCar)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        @if($relatedCar->image_url)
                            <img src="{{ $relatedCar->image_url }}" class="card-img-top" alt="{{ $relatedCar->title }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="bi bi-car-front text-secondary" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold">{{ Str::limit($relatedCar->title, 25) }}</h6>
                        <span style="font-size: 1.1rem; font-weight: bold; color: #e94560;">₹{{ number_format($relatedCar->price ?? 0) }}</span>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="{{ route('car.detail', $relatedCar->slug) }}" class="btn btn-sm w-100" style="background: #e94560; color: white;">View Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
@endsection
