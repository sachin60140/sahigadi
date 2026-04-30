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
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

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
                                            
                                            // Enable submission only if phone is verified
                                            if (!document.getElementById('otpSection').classList.contains('d-none') || document.getElementById('phoneHelp').classList.contains('d-none')) {
                                                submitBtn.disabled = true;
                                            } else {
                                                submitBtn.disabled = false;
                                            }
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
                                            <input type="hidden" name="primary_image_index" id="primaryImageIndex" value="0">
                                            <small class="text-muted d-block mt-2">Supported formats: JPG, PNG, JPEG, GIF (Max 2MB each)</small>
                                            <small class="text-muted d-block">Select a thumbnail above to set it as the featured primary image.</small>
                                            @error('images')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            @foreach($errors->keys() as $key)
                                                @if(\Illuminate\Support\Str::startsWith($key, 'images.'))
                                                    <div class="text-danger small mt-1">{{ $errors->first($key) }}</div>
                                                @endif
                                            @endforeach
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
                                    <label class="form-label fw-semibold">Email Address (Optional)</label>
                                    <input type="email" name="owner_email" class="form-control @error('owner_email') is-invalid @enderror" 
                                           placeholder="For notifications" value="{{ old('owner_email') }}">
                                    @error('owner_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="owner_phone" name="owner_phone" class="form-control @error('owner_phone') is-invalid @enderror" 
                                               placeholder="10-digit phone number" value="{{ old('owner_phone') }}" required pattern="[0-9]{10}">
                                        <button class="btn btn-outline-primary" type="button" id="btnSendOtp">Send OTP</button>
                                    </div>
                                    <div id="phoneHelp" class="form-text text-success d-none"><i class="bi bi-check-circle-fill"></i> Phone Number Verified</div>
                                    @error('owner_phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 d-none" id="otpSection">
                                    <label class="form-label fw-semibold">Enter OTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="otp_input" class="form-control" placeholder="6-digit OTP" maxlength="6">
                                        <button class="btn btn-primary" type="button" id="btnVerifyOtp">Verify</button>
                                    </div>
                                    <div class="form-text text-muted mt-1" id="otpTimerText">Resend OTP in <span id="timerCount">30</span>s</div>
                                    <button class="btn btn-link btn-sm p-0 text-decoration-none d-none mt-1" type="button" id="btnResendOtp">Resend OTP</button>
                                    <div id="otpMessage" class="small mt-1 text-danger"></div>
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
        
        let invalidSize = false;
        Array.from(input.files).forEach((file, index) => {
            if (file.size > 2 * 1024 * 1024) { // 2MB
                invalidSize = true;
            }
        });

        if (invalidSize) {
            alert('One or more images exceed the 2MB limit. Please select smaller images. Use an image compressor if necessary.');
            input.value = '';
            countLabel.textContent = '0 / 10 images selected';
            countLabel.className = 'text-muted';
            return;
        }
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 col-lg-2 mb-2 preview-item-col';
                    col.innerHTML = `
                        <div class="position-relative border p-1 rounded ${index === 0 ? 'bg-primary-subtle border-primary' : ''}" id="preview_col_${index}">
                            <img src="${e.target.result}" class="img-thumbnail border-0 bg-transparent p-0" style="height: 100px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeImage(this, ${index})" style="padding: 0.1rem 0.3rem;">
                                <i class="bi bi-x"></i>
                            </button>
                            <div class="mt-1 text-center">
                                <div class="form-check d-inline-block" style="min-height: auto;">
                                    <input class="form-check-input" type="radio" name="preview_primary" id="feature_${index}" value="${index}" ${index === 0 ? 'checked' : ''} onchange="setFeatured(${index})">
                                    <label class="form-check-label small" style="font-size: 0.75rem;" for="feature_${index}">Featured</label>
                                </div>
                            </div>
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

function setFeatured(index) {
    document.getElementById('primaryImageIndex').value = index;
    document.querySelectorAll('.preview-item-col > div').forEach(el => {
        el.classList.remove('border-primary', 'bg-primary-subtle');
    });
    document.getElementById(`preview_col_${index}`).classList.add('border-primary', 'bg-primary-subtle');
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('owner_phone');
        const btnSendOtp = document.getElementById('btnSendOtp');
        const otpSection = document.getElementById('otpSection');
        const otpInput = document.getElementById('otp_input');
        const btnVerifyOtp = document.getElementById('btnVerifyOtp');
        const btnResendOtp = document.getElementById('btnResendOtp');
        const otpTimerText = document.getElementById('otpTimerText');
        const timerCount = document.getElementById('timerCount');
        const otpMessage = document.getElementById('otpMessage');
        const submitBtn = document.getElementById('submitBtn');
        const phoneHelp = document.getElementById('phoneHelp');
        
        // Initial state
        submitBtn.disabled = true; // Disable until OTP verify
        
        let timerInterval;

        @if(session('sell_car_phone_verified') === old('owner_phone') && old('owner_phone') != '')
            // Pre-verify on reload if phone was already verified
            otpSection.classList.add('d-none');
            phoneHelp.classList.remove('d-none');
            phoneInput.readOnly = true;
            btnSendOtp.classList.add('d-none');
            // Allow submit only if location is also available
            if(document.getElementById('latitude').value !== '') {
                submitBtn.disabled = false;
            }
        @endif

        function startTimer() {
            let count = 30;
            btnResendOtp.classList.add('d-none');
            otpTimerText.classList.remove('d-none');
            timerCount.textContent = count;
            
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                count--;
                timerCount.textContent = count;
                if(count <= 0) {
                    clearInterval(timerInterval);
                    otpTimerText.classList.add('d-none');
                    btnResendOtp.classList.remove('d-none');
                }
            }, 1000);
        }

        function sendOtpAJAX() {
            const phone = phoneInput.value.trim();
            if(!/^[0-9]{10}$/.test(phone)) {
                alert("Please enter a valid 10-digit phone number.");
                return;
            }

            btnSendOtp.disabled = true;
            btnSendOtp.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            otpMessage.textContent = '';
            otpMessage.className = 'small mt-1 text-info';
            
            fetch("{{ route('sell-car.send-otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(res => res.json())
            .then(data => {
                btnSendOtp.innerHTML = 'Send OTP';
                if(data.success) {
                    otpSection.classList.remove('d-none');
                    phoneInput.readOnly = true;
                    btnSendOtp.classList.add('d-none');
                    startTimer();
                    otpMessage.textContent = data.message;
                    otpMessage.className = 'small mt-1 text-success';
                } else {
                    btnSendOtp.disabled = false;
                    alert(data.message || 'Failed to send OTP.');
                }
            })
            .catch(err => {
                btnSendOtp.innerHTML = 'Send OTP';
                btnSendOtp.disabled = false;
                alert('An error occurred. Please try again.');
            });
        }

        btnSendOtp.addEventListener('click', sendOtpAJAX);
        btnResendOtp.addEventListener('click', sendOtpAJAX);

        btnVerifyOtp.addEventListener('click', function() {
            const phone = phoneInput.value.trim();
            const otp = otpInput.value.trim();

            if(!/^[0-9]{6}$/.test(otp)) {
                otpMessage.textContent = 'Please enter a valid 6-digit OTP.';
                otpMessage.className = 'small mt-1 text-danger';
                return;
            }

            btnVerifyOtp.disabled = true;
            btnVerifyOtp.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch("{{ route('sell-car.verify-otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: phone, otp: otp })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    otpSection.classList.add('d-none');
                    phoneHelp.classList.remove('d-none');
                    submitBtn.disabled = document.getElementById('latitude').value == ''; // Recheck location
                    clearInterval(timerInterval);
                } else {
                    btnVerifyOtp.disabled = false;
                    btnVerifyOtp.innerHTML = 'Verify';
                    otpMessage.textContent = data.message || 'Invalid OTP';
                    otpMessage.className = 'small mt-1 text-danger';
                }
            })
            .catch(err => {
                btnVerifyOtp.disabled = false;
                btnVerifyOtp.innerHTML = 'Verify';
                otpMessage.textContent = 'An error occurred during verification.';
                otpMessage.className = 'small mt-1 text-danger';
            });
        });
        
        // Also enable submitBtn on map location load if phone is already verified (in case old validation failed but phone was verified)
        // Though Laravel session will handle this mostly, in pure client-side we keep submitBtn disabled until OTP verify.
        
        // Let's modify the map load behavior as well, if location takes long.
        // The original location script sets submitBtn.disabled = false, we need to ensure it only sets it if phone is also verified.
        // Since phone isn't verified on page load, we should just disable it.
    });
</script>
@endsection
