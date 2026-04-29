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
$actualPhone = $isCustomerListing ? $item->owner_phone : ($car->dealer->phone ?? '');
$maskedPhone = $actualPhone ? substr($actualPhone, 0, 3) . '****' . substr($actualPhone, -3) : '';
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
@section('twitter_image', $firstImage)

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
                <li class="breadcrumb-item"><a href="{{ route('cars.index') }}" class="text-white text-decoration-none">Used Cars</a></li>
                @if($item->city)
                <li class="breadcrumb-item"><a href="{{ route('cars.city', str_replace(' ', '-', strtolower($item->city))) }}" class="text-white text-decoration-none">{{ $item->city }}</a></li>
                @endif
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
                             class="img-thumbnail {{ $index === 0 ? 'border-primary' : 'border-transparent' }}" 
                             style="width: 100px; height: 70px; object-fit: cover; cursor: pointer; border-width: 2px;" 
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
                        <div id="contact-info-container-customer">
                            <p class="mb-2"><i class="bi bi-telephone me-2" style="color: #e94560;"></i><span id="display-phone-customer" class="fw-bold fs-5">{{ $maskedPhone }}</span></p>
                            <button class="btn btn-outline-danger w-100 py-2 mb-3 fw-bold" data-bs-toggle="modal" data-bs-target="#contactUnlockModal">
                                <i class="bi bi-unlock me-2"></i>View Contact Number
                            </button>
                        </div>
                        @endif
                        @if($item->whatsapp_number)
                        <div id="whatsapp-info-container" class="d-none">
                            <p class="mb-2"><i class="bi bi-whatsapp me-2 text-success"></i><span id="display-whatsapp">{{ $item->whatsapp_number }}</span></p>
                        </div>
                        @endif
                        @if($item->city)
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" style="color: #e94560;"></i>{{ $item->city }}</p>
                        @endif
                    </div>
                    <div id="action-buttons-container-customer" class="d-none">
                        @if($item->owner_phone)
                        <a href="#" id="call-btn-customer" class="btn w-100 py-3 mb-3" style="background: #e94560; color: white;">
                            <i class="bi bi-telephone me-2"></i>Call Owner
                        </a>
                        @endif
                        @if($item->whatsapp_number)
                        <a href="#" id="whatsapp-btn-customer" target="_blank" class="btn btn-success w-100 py-3">
                            <i class="bi bi-whatsapp me-2"></i>WhatsApp
                        </a>
                        @endif
                    </div>
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
                        <div id="contact-info-container-dealer">
                            <p class="mb-2"><i class="bi bi-telephone me-2" style="color: #e94560;"></i><span id="display-phone-dealer" class="fw-bold fs-5">{{ $maskedPhone }}</span></p>
                            <button class="btn btn-outline-danger w-100 py-2 mb-3 fw-bold" data-bs-toggle="modal" data-bs-target="#contactUnlockModal">
                                <i class="bi bi-unlock me-2"></i>View Contact Number
                            </button>
                        </div>
                        @endif
                        @if($car->dealer->city)
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" style="color: #e94560;"></i>{{ $car->dealer->city }}</p>
                        @endif
                    </div>
                    <div id="action-buttons-container-dealer" class="d-none">
                        <button class="btn w-100 py-3 mb-3" style="background: #e94560; color: white;" data-bs-toggle="modal" data-bs-target="#enquiryModal">
                            <i class="bi bi-chat-dots me-2"></i>Send Enquiry
                        </button>
                        <a href="#" id="call-btn-dealer" class="btn btn-outline-secondary w-100 py-3">
                            <i class="bi bi-telephone me-2"></i>Call Dealer
                        </a>
                    </div>
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
        <h4 class="mb-4"><i class="bi bi-grid me-2"></i>Related Used Cars {{ $item->city ? 'in ' . $item->city : '' }}</h4>
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

    <!-- Contact Unlock Modal -->
    <div class="modal fade" id="contactUnlockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-shield-lock me-2 text-warning"></i>Unlock Contact Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-4">Please verify your mobile number to view the seller's contact details.</p>
                    
                    <div id="unlockStep1">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unlockName" placeholder="Enter your full name">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Your Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="text" class="form-control" id="unlockPhone" placeholder="10-digit mobile number" maxlength="10">
                            </div>
                        </div>
                        <button type="button" id="btnSendOtp" class="btn w-100 py-2 fw-bold" style="background: #e94560; color: white;">
                            Send OTP <span id="spinnerSendOtp" class="spinner-border spinner-border-sm d-none ms-2"></span>
                        </button>
                    </div>

                    <div id="unlockStep2" class="d-none">
                        <div class="alert alert-success bg-success bg-opacity-10 border-0 mb-4">
                            <i class="bi bi-check-circle-fill text-success me-2"></i> OTP sent to <span id="displayUnlockPhone" class="fw-bold"></span>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Enter 6-Digit OTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg text-center fw-bold letter-spacing-1" id="unlockOtp" placeholder="------" maxlength="6">
                            <div class="form-text mt-2 text-end">
                                <a href="#" id="btnResendOtp" class="text-decoration-none" style="color: #e94560;">Resend OTP</a>
                            </div>
                        </div>
                        <button type="button" id="btnVerifyOtp" class="btn w-100 py-2 fw-bold" style="background: #1a1a2e; color: white;">
                            Verify & Unlock <span id="spinnerVerifyOtp" class="spinner-border spinner-border-sm d-none ms-2"></span>
                        </button>
                    </div>

                    <div id="unlockError" class="alert alert-danger mt-3 d-none border-0"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setActiveSlide(index) {
        let myCarousel = document.getElementById('carCarousel');
        let carousel = bootstrap.Carousel.getInstance(myCarousel);
        if (!carousel) {
            carousel = new bootstrap.Carousel(myCarousel);
        }
        carousel.to(index);
    }

    document.addEventListener('DOMContentLoaded', function() {
        let carouselElement = document.getElementById('carCarousel');
        if (carouselElement) {
            carouselElement.addEventListener('slide.bs.carousel', function (e) {
                let thumbnails = document.querySelectorAll('.img-thumbnail');
                thumbnails.forEach((thumb, i) => {
                    if (i === e.to) {
                        thumb.classList.add('border-primary');
                        thumb.classList.remove('border-transparent');
                    } else {
                        thumb.classList.remove('border-primary');
                        thumb.classList.add('border-transparent');
                    }
                });
            });
        }

        // OTP Verification Logic
        const btnSendOtp = document.getElementById('btnSendOtp');
        const btnVerifyOtp = document.getElementById('btnVerifyOtp');
        const btnResendOtp = document.getElementById('btnResendOtp');
        const unlockStep1 = document.getElementById('unlockStep1');
        const unlockStep2 = document.getElementById('unlockStep2');
        const unlockError = document.getElementById('unlockError');
        
        const carId = '{{ $item->id }}';
        const isCustomerListing = {{ $isCustomerListing ? 'true' : 'false' }};

        function showError(msg) {
            unlockError.textContent = msg;
            unlockError.classList.remove('d-none');
        }

        function hideError() {
            unlockError.classList.add('d-none');
        }

        btnSendOtp.addEventListener('click', function() {
            const name = document.getElementById('unlockName').value.trim();
            const phone = document.getElementById('unlockPhone').value.trim();
            
            if (!name) return showError('Please enter your name.');
            if (!/^[0-9]{10}$/.test(phone)) return showError('Please enter a valid 10-digit mobile number.');
            
            hideError();
            btnSendOtp.disabled = true;
            document.getElementById('spinnerSendOtp').classList.remove('d-none');

            fetch('{{ route('api.enquiry.send-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ phone: phone, car_id: carId, is_customer_listing: isCustomerListing })
            })
            .then(r => r.json())
            .then(data => {
                btnSendOtp.disabled = false;
                document.getElementById('spinnerSendOtp').classList.add('d-none');
                
                if(data.success) {
                    document.getElementById('displayUnlockPhone').textContent = '+91 ' + phone;
                    unlockStep1.classList.add('d-none');
                    unlockStep2.classList.remove('d-none');
                } else {
                    showError(data.message || 'Failed to send OTP.');
                }
            })
            .catch(e => {
                btnSendOtp.disabled = false;
                document.getElementById('spinnerSendOtp').classList.add('d-none');
                showError('Network error occurred.');
            });
        });

        btnResendOtp.addEventListener('click', function(e) {
            e.preventDefault();
            unlockStep2.classList.add('d-none');
            unlockStep1.classList.remove('d-none');
            btnSendOtp.click();
        });

        btnVerifyOtp.addEventListener('click', function() {
            const name = document.getElementById('unlockName').value.trim();
            const phone = document.getElementById('unlockPhone').value.trim();
            const otp = document.getElementById('unlockOtp').value.trim();
            
            if (!/^[0-9]{6}$/.test(otp)) return showError('Please enter a valid 6-digit OTP.');
            
            hideError();
            btnVerifyOtp.disabled = true;
            document.getElementById('spinnerVerifyOtp').classList.remove('d-none');

            fetch('{{ route('api.enquiry.verify-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name: name, phone: phone, otp: otp, car_id: carId, is_customer_listing: isCustomerListing })
            })
            .then(r => r.json())
            .then(data => {
                btnVerifyOtp.disabled = false;
                document.getElementById('spinnerVerifyOtp').classList.add('d-none');
                
                if(data.success) {
                    // Update UI
                    let modal = bootstrap.Modal.getInstance(document.getElementById('contactUnlockModal'));
                    modal.hide();
                    
                    // Reveal contact
                    if (isCustomerListing) {
                        document.getElementById('display-phone-customer').textContent = data.contact_number;
                        document.getElementById('contact-info-container-customer').querySelector('button').classList.add('d-none');
                        document.getElementById('action-buttons-container-customer').classList.remove('d-none');
                        document.getElementById('call-btn-customer').href = 'tel:' + data.contact_number;
                        let waInfo = document.getElementById('whatsapp-info-container');
                        if (waInfo) waInfo.classList.remove('d-none');
                    } else {
                        document.getElementById('display-phone-dealer').textContent = data.contact_number;
                        document.getElementById('contact-info-container-dealer').querySelector('button').classList.add('d-none');
                        document.getElementById('action-buttons-container-dealer').classList.remove('d-none');
                        document.getElementById('call-btn-dealer').href = 'tel:' + data.contact_number;
                    }
                } else {
                    showError(data.message || 'Invalid OTP.');
                }
            })
            .catch(e => {
                btnVerifyOtp.disabled = false;
                document.getElementById('spinnerVerifyOtp').classList.add('d-none');
                showError('Network error occurred.');
            });
        });
    });
</script>
@endpush
