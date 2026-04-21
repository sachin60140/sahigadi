@extends('layouts.app')

@section('title', 'Sell Your Used Car Online In India - Best Market Price | SAHIGADI')
@section('meta_description', 'Looking to sell your car in Bihar, Patna, or anywhere in India? List your second-hand car on SAHIGADI for free valuation, instant payout, and hassle-free RC transfer.')
@section('meta_keywords', 'sell my car in Bihar, sell second hand car online, used car dealer in Bihar, second hand cars in Patna, used cars in Muzaffarpur, sell used car online')
@section('canonical', route('sell-car.index'))

@push('json_ld')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebPage",
  "name": "Sell Used Car Online - Instant Valuation | SAHIGADI",
  "description": "Sell your car quickly for the best market price. Fast inspection, instant payout, and hassle-free RC transfer across Patna, Bihar, and PAN India.",
  "url": "{{ route('sell-car.index') }}"
}
</script>
@endpush
@section('content')
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-3">Sell Your Car Online at the Best Price</h1>
            <p class="lead text-white-50">Get free valuation, doorstep inspection, and connect with verified buyers</p>
        </div>
    </div>
</section>

<!-- SEO Content Block for Local & National Keywords -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-10 mx-auto">
               <h2 class="h3 fw-bold text-dark">Ready to "Sell My Car" in Bihar or Across India?</h2>
               <p class="text-muted lead">
                   Whether you are looking for a reliable <strong>used car dealer in Bihar</strong> or want to sell <strong>second hand cars in Patna</strong> and <strong>used cars in Muzaffarpur</strong>, SAHIGADI is your top trusted platform. We provide a transparent bridge between individual sellers and verified buyers networks across our PAN-India marketplace.
               </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-4 bg-light rounded-4 h-100">
                    <i class="bi bi-cash-coin text-accent mb-3" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-2">Maximum Resale Value</h5>
                    <p class="text-muted small">Our marketplace ensures your listing reaches thousands of active buyers guaranteeing you the highest competitive market price.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 bg-light rounded-4 h-100">
                    <i class="bi bi-shield-check text-accent mb-3" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-2">Hassle-Free RC Transfer</h5>
                    <p class="text-muted small">Skip the complicated RTO paperwork! Verified dealers in our network handle document transfers securely and legally.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 bg-light rounded-4 h-100">
                    <i class="bi bi-lightning-charge text-accent mb-3" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-2">Fast & Instant Payouts</h5>
                    <p class="text-muted small">Once the car inspection completes and the final deal is approved, get your money deposited to your bank account instantly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5">
                        <h4 class="fw-bold mb-4"><i class="bi bi-car-front me-2 text-danger"></i>Car Details</h4>
                        <form action="{{ route('sell-car.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Listing Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                           placeholder="e.g., 2020 Maruti Swift VXi" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Brand</label>
                                    <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Model</label>
                                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                                           placeholder="e.g., Swift VXi" value="{{ old('model') }}">
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Year</label>
                                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" 
                                           placeholder="e.g., 2020" value="{{ old('year') }}" min="1900" max="{{ date('Y') }}">
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Fuel Type</label>
                                    <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @foreach($fuelTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('fuel_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fuel_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transmission</label>
                                    <select name="transmission" class="form-select @error('transmission') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @foreach($transmissions as $key => $label)
                                            <option value="{{ $key }}" {{ old('transmission') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('transmission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kilometers Driven</label>
                                    <input type="number" name="km_driven" class="form-control @error('km_driven') is-invalid @enderror" 
                                           placeholder="e.g., 45000" value="{{ old('km_driven') }}" min="0">
                                    @error('km_driven')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expected Price (₹)</label>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                           placeholder="e.g., 500000" value="{{ old('price') }}" min="0">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City</label>
                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                           placeholder="e.g., Patna" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                @error('latitude')
                                    <div class="col-12 mt-1">
                                        <div class="text-danger small">{{ $message }}</div>
                                    </div>
                                @enderror

                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const submitBtn = document.getElementById('submitBtn');
                                    
                                    function handleLocationError(error) {
                                        submitBtn.disabled = true;
                                        let errorMsg = 'Location access is strictly required to list your vehicle.';
                                        switch(error.code) {
                                            case error.PERMISSION_DENIED:
                                                errorMsg = 'You denied the request for Geolocation. Location access is strictly required to sell your car. Please allow permissions and refresh the page.';
                                                break;
                                        }
                                        alert(errorMsg);
                                    }

                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function(position) {
                                            document.getElementById('latitude').value = position.coords.latitude;
                                            document.getElementById('longitude').value = position.coords.longitude;
                                            
                                            // Enable submission silently
                                            submitBtn.disabled = false;
                                        }, handleLocationError, {
                                            enableHighAccuracy: true,
                                            timeout: 10000,
                                            maximumAge: 0
                                        });
                                    } else {
                                        alert('Geolocation is not supported by your browser.');
                                        submitBtn.disabled = true;
                                    }
                                });
                                </script>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" 
                                           placeholder="e.g., BR01AB1234" value="{{ old('registration_number') }}">
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Number of Owners</label>
                                    <select name="owners" class="form-select @error('owners') is-invalid @enderror">
                                        <option value="1" {{ old('owners') == 1 ? 'selected' : '' }}>1st Owner</option>
                                        <option value="2" {{ old('owners') == 2 ? 'selected' : '' }}>2nd Owner</option>
                                        <option value="3" {{ old('owners') == 3 ? 'selected' : '' }}>3rd Owner</option>
                                        <option value="4" {{ old('owners') == 4 ? 'selected' : '' }}>4th Owner</option>
                                    </select>
                                    @error('owners')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Car Images <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal">(Minimum 5, Maximum 10 images)</small>
                                    </label>
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="row g-2" id="imagePreviewContainer">
                                        </div>
                                        <div class="mt-3">
                                            <label for="car_images" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus-circle me-1"></i>Select Images
                                            </label>
                                            <input type="file" name="images[]" id="car_images" class="d-none" multiple accept="image/*" onchange="previewImages(this)" required>
                                            <small class="text-muted d-block mt-2">Supported formats: JPG, PNG, JPEG, GIF (Max 2MB each)</small>
                                            @error('images')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            @error('images.*')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted" id="imageCount">0 / 10 images selected</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h4 class="fw-bold mb-4"><i class="bi bi-person me-2 text-danger"></i>Owner Details</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Your Name</label>
                                    <input type="text" name="owner_name" class="form-control @error('owner_name') is-invalid @enderror" 
                                           placeholder="Enter your name" value="{{ old('owner_name') }}">
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="owner_phone" class="form-control @error('owner_phone') is-invalid @enderror" 
                                           placeholder="Enter your phone number" value="{{ old('owner_phone') }}" required>
                                    @error('owner_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">WhatsApp Number</label>
                                    <input type="text" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                           placeholder="Enter your WhatsApp number" value="{{ old('whatsapp_number') }}">
                                    @error('whatsapp_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-accent btn-lg w-100" disabled>
                                    <i class="bi bi-send me-2"></i>Submit Listing
                                </button>
                            </div>

                            <p class="text-muted small mt-3 text-center">
                                <i class="bi bi-info-circle me-1"></i>
                                Your listing will be reviewed by our team before it goes live. We will contact you for verification.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function previewImages(input) {
    const container = document.getElementById('imagePreviewContainer');
    const countLabel = document.getElementById('imageCount');
    container.innerHTML = '';
    
    if (input.files) {
        const totalFiles = input.files.length;
        countLabel.textContent = totalFiles + ' / 10 images selected';
        
        if (totalFiles > 10) {
            alert('Maximum 10 images allowed');
            input.value = '';
            countLabel.textContent = '0 / 10 images selected';
            return;
        }
        
        if (totalFiles < 5) {
            countLabel.className = 'text-danger';
        } else {
            countLabel.className = 'text-success';
        }
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 col-lg-2 mb-2';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(this, ${index})">
                                <i class="bi bi-x"></i>
                            </button>
                            <small class="d-block text-center mt-1 text-muted">${file.name.substring(0, 10)}...</small>
                        </div>
                    `;
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function removeImage(btn, index) {
    const input = document.getElementById('car_images');
    const dt = new DataTransfer();
    const files = input.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    input.files = dt.files;
    btn.closest('.col-4').remove();
    
    const countLabel = document.getElementById('imageCount');
    const totalFiles = input.files.length;
    countLabel.textContent = totalFiles + ' / 10 images selected';
    
    if (totalFiles < 5) {
        countLabel.className = 'text-danger';
    } else {
        countLabel.className = 'text-success';
    }
}
</script>
@endsection
