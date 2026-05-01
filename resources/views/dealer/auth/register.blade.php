@extends('layouts.app')

@section('title', 'Dealer Registration - SAHI GADI')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2">Dealer Registration</h1>
            <p>Join SAHI GADI - Your trusted car marketplace</p>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <form action="{{ route('dealer.register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h5 class="mb-3"><i class="bi bi-person me-2"></i>Basic Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter your full name">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="your@email.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <div class="input-group">
                                    <input type="text" id="phone_input" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="10-digit phone number" pattern="[0-9]{10}">
                                    <button class="btn btn-outline-primary" type="button" id="btnSendOtp">Send OTP</button>
                                </div>
                                <div id="phoneHelp" class="form-text text-success d-none"><i class="bi bi-check-circle-fill"></i> Phone Number Verified</div>
                                @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 d-none" id="otpSection">
                                <label class="form-label">Enter OTP *</label>
                                <div class="input-group">
                                    <input type="text" id="otp_input" class="form-control" placeholder="6-digit OTP" maxlength="6">
                                    <button class="btn btn-primary" type="button" id="btnVerifyOtp">Verify</button>
                                </div>
                                <div class="form-text text-muted mt-1" id="otpTimerText">Resend OTP in <span id="timerCount">30</span>s</div>
                                <button class="btn btn-link btn-sm p-0 text-decoration-none d-none mt-1" type="button" id="btnResendOtp">Resend OTP</button>
                                <div id="otpMessage" class="small mt-1 text-danger"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="Your business name (optional)">
                                @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Full Address *</label>
                                <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required placeholder="Enter your complete street address">{{ old('address') }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City *</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required placeholder="Your city">
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State *</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" required placeholder="Your state">
                                @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode *</label>
                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode') }}" required placeholder="Pincode / ZIP">
                                @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Min 8 characters">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm your password">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>KYC Documents * (Mandatory)</h5>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Required:</strong> Please upload both your **Aadhaar Card** and **PAN Card** to complete your KYC registration.
                        </div>
                        
                        <!-- Aadhaar Card Details -->
                        <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Aadhaar Card</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Aadhaar Number *</label>
                                <input type="text" name="kyc_document_number" class="form-control @error('kyc_document_number') is-invalid @enderror" value="{{ old('kyc_document_number') }}" required placeholder="12-digit Aadhaar Number">
                                @error('kyc_document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upload Aadhaar Document * <small>(PDF, JPG, PNG - Max 5MB)</small></label>
                                <input type="file" name="kyc_document" class="form-control @error('kyc_document') is-invalid @enderror" required accept=".pdf,.jpg,.jpeg,.png">
                                @error('kyc_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- PAN Card Details -->
                        <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">PAN Card</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">PAN Number *</label>
                                <input type="text" name="pan_number" style="text-transform:uppercase" class="form-control @error('pan_number') is-invalid @enderror" value="{{ old('pan_number') }}" required placeholder="10-digit PAN Number" maxlength="10">
                                @error('pan_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upload PAN Document * <small>(PDF, JPG, PNG - Max 5MB)</small></label>
                                <input type="file" name="pan_document" class="form-control @error('pan_document') is-invalid @enderror" required accept=".pdf,.jpg,.jpeg,.png">
                                @error('pan_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>GST Document (Optional)</h5>
                        <div class="alert alert-secondary mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            If you have a GST registration, please provide the details below.
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">GST Number</label>
                                <input type="text" name="gst_number" class="form-control @error('gst_number') is-invalid @enderror" value="{{ old('gst_number') }}" placeholder="15-digit GST number" maxlength="15">
                                @error('gst_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upload GST Document (PDF, JPG, PNG - Max 5MB)</label>
                                <input type="file" name="gst_document" class="form-control @error('gst_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                @error('gst_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="submitBtn" class="btn btn-accent btn-lg py-3" disabled>
                                <i class="bi bi-check-circle me-2"></i>Register as Dealer
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-white border-0 pb-4">
                    <p class="mb-0">Already have an account? <a href="{{ route('dealer.login') }}" class="text-accent fw-bold">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.text-accent { color: var(--accent) !important; }
.btn-accent {
    background: var(--accent);
    color: white;
    border: none;
}
.btn-accent:hover {
    background: #d63850;
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone_input');
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
        
        let timerInterval;

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
            
            fetch("{{ route('dealer.send-otp') }}", {
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

            fetch("{{ route('dealer.verify-otp') }}", {
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
                    submitBtn.disabled = false;
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
    });
</script>
@endpush
