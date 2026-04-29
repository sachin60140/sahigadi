@extends('layouts.dealer')

@section('title', 'My Profile - SAHIGADI')

@push('styles')
<style>
    .profile-header {
        position: relative;
        background: linear-gradient(135deg, var(--bs-primary) 0%, #1e3a8a 100%);
        height: 200px;
        border-radius: 1rem 1rem 0 0;
        overflow: hidden;
    }
    .profile-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40%;
        background: linear-gradient(to top, rgba(0,0,0,0.4) 0%, transparent 100%);
    }
    .profile-avatar-wrapper {
        position: relative;
        margin-top: -80px;
        padding-left: 30px;
        z-index: 10;
        display: flex;
        align-items: flex-end;
    }
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid #fff;
        background-color: #fff;
        object-fit: cover;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .profile-avatar:hover {
        transform: scale(1.02);
    }
    .camera-btn {
        position: absolute;
        bottom: 10px;
        left: 130px;
        background: var(--bs-primary);
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid #fff;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .camera-btn:hover {
        background: #1e3a8a;
        transform: translateY(-2px);
    }
    .nav-pills-custom .nav-link {
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }
    .nav-pills-custom .nav-link:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .nav-pills-custom .nav-link.active {
        background: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
    }
    .nav-pills-custom .nav-link i {
        margin-right: 8px;
    }
    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
    }
    .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .section-title {
        font-weight: 700;
        color: #1e293b;
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    .section-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 3px;
        background: var(--bs-primary);
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 border-0 border-start border-4 border-success" role="alert">
            <i class="bi bi-check-circle-fill text-success me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 border-0 border-start border-4 border-danger" role="alert">
            <i class="bi bi-exclamation-circle-fill text-danger me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('dealer.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('PUT')
    </form>

    <div class="card mb-4 overflow-hidden">
        <div class="profile-header"></div>
            
            <div class="profile-avatar-wrapper mb-4">
                <div class="position-relative">
                    <img src="{{ $dealer->profile_image ? Storage::url($dealer->profile_image) : asset('images/default-avatar.png') }}" class="profile-avatar" id="profilePreview" alt="Profile Image">
                    <label for="profile_image" class="camera-btn" title="Change Profile Picture">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                    <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*" onchange="previewProfileImage(this)" form="profileForm">
                </div>
                <div class="ms-4 pb-2">
                    <h3 class="fw-bold mb-1 text-dark">{{ $dealer->name }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-buildings me-1"></i> {{ $dealer->company_name ?? 'Independent Dealer' }}</p>
                </div>
            </div>

            <div class="card-body px-4 px-md-5 pb-5">
                <div class="row">
                    <!-- Navigation Pills -->
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="nav flex-column nav-pills nav-pills-custom" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active text-start" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                                <i class="bi bi-person"></i> Personal Info
                            </button>
                            <button class="nav-link text-start" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab" aria-controls="v-pills-security" aria-selected="false">
                                <i class="bi bi-shield-lock"></i> Security & Phone
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="col-lg-9 col-md-8">
                        <div class="tab-content" id="v-pills-tabContent">
                            
                            <!-- Personal Info Tab -->
                            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                <h4 class="section-title">Personal Information</h4>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold text-secondary">Full Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                                <input type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name', $dealer->name) }}" required form="profileForm">
                                            </div>
                                            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold text-secondary">Company Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-building text-muted"></i></span>
                                                <input type="text" class="form-control border-start-0 ps-0 @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name', $dealer->company_name) }}" form="profileForm">
                                            </div>
                                            @error('company_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold text-secondary">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                                <input type="email" class="form-control border-start-0 ps-0 bg-light" value="{{ $dealer->email }}" disabled>
                                            </div>
                                            <div class="form-text"><i class="bi bi-info-circle"></i> Email cannot be changed.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" form="profileForm">
                                        <i class="bi bi-check2-circle me-1"></i> Save Changes
                                    </button>
                                </div>
                            </div>

                            <!-- Security Tab -->
                            <div class="tab-pane fade" id="v-pills-security" role="tabpanel" aria-labelledby="v-pills-security-tab">
                                
                                <!-- Update Phone Card -->
                                <div class="card border border-light bg-light shadow-none mb-4">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-phone me-2 text-primary"></i>Update Mobile Number</h5>
                                        
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold text-secondary">Current Phone Number</label>
                                            <div class="input-group w-50">
                                                <span class="input-group-text bg-white"><i class="bi bi-telephone text-muted"></i></span>
                                                <input type="text" class="form-control bg-white" value="{{ $dealer->phone }}" disabled>
                                            </div>
                                        </div>

                                        <form action="{{ route('dealer.profile.verify-phone') }}" method="POST" id="updatePhoneForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-secondary">New Phone Number</label>
                                                <div class="input-group w-75">
                                                    <span class="input-group-text bg-white">+91</span>
                                                    <input type="text" id="new_phone" class="form-control" placeholder="10-digit number" required pattern="[0-9]{10}">
                                                    <button type="button" class="btn btn-dark px-4" id="btnSendOtp">Request OTP</button>
                                                </div>
                                                <div class="form-text mt-2"><i class="bi bi-shield-check text-success"></i> Two-factor authentication: OTPs will be sent to both your current and new numbers for maximum security.</div>
                                            </div>

                                            <div id="otpVerificationSection" class="d-none mt-4 p-4 bg-white rounded-3 border">
                                                <h6 class="fw-bold mb-3">Verify Both Numbers</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold text-danger">OTP Sent to Current Phone ({{ substr($dealer->phone, 0, 2) . '******' . substr($dealer->phone, -2) }})</label>
                                                        <input type="text" name="old_otp" class="form-control border-danger form-control-lg text-center letter-spacing-2" placeholder="------" maxlength="6" required>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold text-success">OTP Sent to New Phone</label>
                                                        <input type="text" name="new_otp" class="form-control border-success form-control-lg text-center letter-spacing-2" placeholder="------" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                                        <i class="bi bi-check-circle me-1"></i> Verify & Update Phone
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Change Password Card -->
                                <div class="card border border-light bg-light shadow-none">
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-key me-2 text-primary"></i>Change Password</h5>
                                        
                                        <form action="{{ route('dealer.profile.update-password') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-secondary">Current Password</label>
                                                    <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-secondary">New Password</label>
                                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                                                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-secondary">Confirm New Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter new password" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <button type="submit" class="btn btn-dark px-4 py-2">
                                                    <i class="bi bi-lock me-1"></i> Update Password
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

@push('scripts')
<style>
    .letter-spacing-2 { letter-spacing: 5px; font-weight: 600; }
</style>
<script>
    function previewProfileImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('btnSendOtp').addEventListener('click', function() {
        let newPhone = document.getElementById('new_phone').value;
        if (!/^[0-9]{10}$/.test(newPhone)) {
            alert('Please enter a valid 10-digit new phone number.');
            return;
        }

        let btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending...';

        fetch("{{ route('dealer.profile.phone-otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ new_phone: newPhone })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = 'Request OTP';
            if (data.success) {
                document.getElementById('otpVerificationSection').classList.remove('d-none');
                document.getElementById('new_phone').readOnly = true;
                btn.classList.add('d-none');
                
                // Smooth scroll to OTP section
                document.getElementById('otpVerificationSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                btn.disabled = false;
                alert(data.message);
            }
        })
        .catch(err => {
            btn.innerHTML = 'Request OTP';
            btn.disabled = false;
            alert('An error occurred. Please try again.');
        });
    });
</script>
@endpush
@endsection
