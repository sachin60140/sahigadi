@extends('layouts.app')

@section('title', 'Forgot Password - Dealer Portal | SAHIGADI')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock text-primary display-4 mb-3"></i>
                            <h3 class="fw-bold">Reset Password</h3>
                            <p class="text-muted">Enter your registered phone number to receive an OTP and reset your password.</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('dealer.forgot-password.reset') }}" id="resetPasswordForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required pattern="[0-9]{10}" placeholder="10-digit mobile number">
                                    <button class="btn btn-outline-primary" type="button" id="btnSendOtp">Send OTP</button>
                                </div>
                                <div id="phoneHelp" class="form-text text-success d-none"><i class="bi bi-check-circle-fill"></i> OTP Sent Successfully</div>
                                @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 d-none" id="otpSection">
                                <label class="form-label">Enter OTP <span class="text-danger">*</span></label>
                                <input type="text" name="otp" id="otp" class="form-control @error('otp') is-invalid @enderror" placeholder="6-digit OTP" maxlength="6">
                                <div class="form-text text-muted mt-1" id="otpTimerText">Resend OTP in <span id="timerCount">30</span>s</div>
                                <button class="btn btn-link btn-sm p-0 text-decoration-none d-none mt-1" type="button" id="btnResendOtp">Resend OTP</button>
                                @error('otp')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 d-none" id="passwordSection">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters">
                                </div>
                                @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 d-none" id="passwordConfirmSection">
                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 d-none" id="btnSubmitReset">Reset Password</button>

                            <div class="text-center mt-4">
                                <p class="mb-0">Remembered your password? <a href="{{ route('dealer.login') }}" class="text-primary text-decoration-none fw-bold">Login here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        const btnSendOtp = document.getElementById('btnSendOtp');
        const otpSection = document.getElementById('otpSection');
        const passwordSection = document.getElementById('passwordSection');
        const passwordConfirmSection = document.getElementById('passwordConfirmSection');
        const btnSubmitReset = document.getElementById('btnSubmitReset');
        const btnResendOtp = document.getElementById('btnResendOtp');
        const otpTimerText = document.getElementById('otpTimerText');
        const timerCount = document.getElementById('timerCount');
        const phoneHelp = document.getElementById('phoneHelp');

        let timerInterval;

        // If validation failed, sections might need to be visible
        if ("{{ old('otp') }}" || document.querySelector('.invalid-feedback.d-block')) {
            // Keep it simple: let user re-send OTP if they fail
        }

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
            btnSendOtp.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            fetch("{{ route('dealer.forgot-password.send-otp') }}", {
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
                    passwordSection.classList.remove('d-none');
                    passwordConfirmSection.classList.remove('d-none');
                    btnSubmitReset.classList.remove('d-none');
                    phoneInput.readOnly = true;
                    btnSendOtp.classList.add('d-none');
                    phoneHelp.classList.remove('d-none');
                    startTimer();
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
    });
</script>
@endsection
