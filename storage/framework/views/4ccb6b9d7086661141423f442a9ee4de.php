<?php $__env->startSection('title', 'Customer Login - SAHI GADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle text-accent" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-2">Customer Login</h3>
                        <p class="text-muted">Login with your mobile number</p>
                    </div>

                    <form id="sendOtpForm" class="no-loader">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">+91</span>
                                <input type="text" name="phone" id="phone" class="form-control border-start-0 ps-0" placeholder="Enter 10-digit number" required pattern="[0-9]{10}" inputmode="numeric" autocomplete="tel">
                            </div>
                            <div class="invalid-feedback" id="phoneError"></div>
                        </div>
                        <button type="submit" class="btn btn-accent w-100 py-2 fw-bold" id="sendOtpBtn">
                            Get OTP
                        </button>
                    </form>

                    <form id="verifyOtpForm" style="display: none;" class="no-loader">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="phone" id="verifyPhone">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Enter OTP</label>
                            <input type="text" name="otp" id="otp" class="form-control text-center letter-spacing-2 fs-4" placeholder="••••••" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
                            <div class="invalid-feedback" id="otpError"></div>
                        </div>
                        <button type="submit" class="btn btn-accent w-100 py-2 fw-bold" id="verifyOtpBtn">
                            Verify & Login
                        </button>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-link text-decoration-none text-muted" id="changeNumberBtn">Change Mobile Number</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('sendOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phone = document.getElementById('phone').value;
        const btn = document.getElementById('sendOtpBtn');
        const errorDiv = document.getElementById('phoneError');
        
        if(!/^[0-9]{10}$/.test(phone)) {
            document.getElementById('phone').classList.add('is-invalid');
            errorDiv.textContent = 'Please enter a valid 10-digit mobile number';
            return;
        }

        document.getElementById('phone').classList.remove('is-invalid');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

        fetch('<?php echo e(route("customer.send-otp")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('sendOtpForm').style.display = 'none';
                document.getElementById('verifyOtpForm').style.display = 'block';
                document.getElementById('verifyPhone').value = phone;
                
                // Auto-focus OTP field
                setTimeout(() => {
                    document.getElementById('otp').focus();
                }, 100);

                // Auto-read OTP from SMS (WebOTP API)
                if ('OTPCredential' in window) {
                    const ac = new AbortController();
                    navigator.credentials.get({
                        otp: { transport: ['sms'] },
                        signal: ac.signal
                    }).then(otp => {
                        document.getElementById('otp').value = otp.code;
                        // Auto-submit form
                        document.getElementById('verifyOtpBtn').click();
                    }).catch(err => {
                        console.log('WebOTP API Error:', err);
                    });
                }
            } else {
                alert(data.message || 'Error sending OTP');
                btn.disabled = false;
                btn.innerHTML = 'Get OTP';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = 'Get OTP';
        });
    });

    document.getElementById('verifyOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phone = document.getElementById('verifyPhone').value;
        const otp = document.getElementById('otp').value;
        const btn = document.getElementById('verifyOtpBtn');
        const errorDiv = document.getElementById('otpError');
        
        if(!/^[0-9]{6}$/.test(otp)) {
            document.getElementById('otp').classList.add('is-invalid');
            errorDiv.textContent = 'Please enter a valid 6-digit OTP';
            return;
        }

        document.getElementById('otp').classList.remove('is-invalid');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Verifying...';

        fetch('<?php echo e(route("customer.verify-otp")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone: phone, otp: otp })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.href = data.redirect;
            } else {
                document.getElementById('otp').classList.add('is-invalid');
                errorDiv.textContent = data.message || 'Invalid OTP';
                btn.disabled = false;
                btn.innerHTML = 'Verify & Login';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = 'Verify & Login';
        });
    });

    document.getElementById('changeNumberBtn').addEventListener('click', function() {
        document.getElementById('verifyOtpForm').style.display = 'none';
        document.getElementById('sendOtpForm').style.display = 'block';
        document.getElementById('otp').value = '';
        const btn = document.getElementById('sendOtpBtn');
        btn.disabled = false;
        btn.innerHTML = 'Get OTP';
    });
</script>
<style>
    .letter-spacing-2 { letter-spacing: 0.5rem; }
    .text-accent { color: var(--accent); }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/login.blade.php ENDPATH**/ ?>