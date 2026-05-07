@extends('layouts.app')

@section('title', 'My Dashboard - SAHI GADI')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h2 class="fw-bold mb-1">My Dashboard</h2>
                <p class="text-muted mb-0">Manage your listed cars and profile</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('frontend.customer.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">My Car Listings</h4>
                <a href="{{ route('sell-car.index') }}" class="btn btn-accent btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Sell a Car
                </a>
            </div>

            @if($listings->count() > 0)
                <div class="row">
                    @foreach($listings as $listing)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="position-relative">
                                @php
                                    $images = json_decode($listing->images, true) ?? [];
                                @endphp
                                @if(count($images) > 0)
                                    <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top" alt="{{ $listing->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                                    </div>
                                @endif

                                @if($listing->status == 'approved')
                                    <span class="position-absolute top-0 start-0 badge bg-success m-2">Approved</span>
                                @elseif($listing->status == 'pending')
                                    <span class="position-absolute top-0 start-0 badge bg-warning text-dark m-2">Pending Review</span>
                                @else
                                    <span class="position-absolute top-0 start-0 badge bg-danger m-2">Rejected</span>
                                @endif

                                @if($listing->isFeatured())
                                    <span class="position-absolute top-0 start-0 badge badge-featured m-2" style="{{ $listing->status ? 'margin-left: 80px !important;' : '' }}">
                                        <i class="bi bi-star-fill me-1"></i>Featured
                                    </span>
                                @endif
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
                                <h5 class="text-accent fw-bold mb-0">₹{{ number_format($listing->price ?? 0) }}</h5>
                                
                                @if($listing->status == 'rejected' && $listing->rejection_reason)
                                    <div class="alert alert-danger mt-3 mb-0 py-2 small">
                                        <strong>Reason:</strong> {{ $listing->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3 d-flex gap-2">
                                @if($listing->status == 'approved')
                                    <a href="{{ route('car.detail', $listing->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1" target="_blank">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                @else
                                    <a href="{{ route('customer.listing.edit', $listing->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-danger btn-sm flex-grow-1" onclick="initiateDelete({{ $listing->id }})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
                    <i class="bi bi-car-front text-secondary" style="font-size: 5rem;"></i>
                    <h4 class="mt-3 fw-bold">No cars listed yet</h4>
                    <p class="text-muted mb-4">You haven't listed any cars for sale.</p>
                    <a href="{{ route('sell-car.index') }}" class="btn btn-accent px-4 py-2">
                        Sell Your First Car
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- OTP Verification Modal -->
<div class="modal fade" id="deleteOtpModal" tabindex="-1" aria-labelledby="deleteOtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-sm">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteOtpModalLabel"><i class="bi bi-shield-lock text-accent me-2"></i>Security Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-4">To protect your account, please verify your identity to permanently delete this car listing.</p>
                
                <div id="otpStep1">
                    <p class="small fw-semibold mb-3">
                        An OTP will be sent to your registered mobile number{{ $customer->email ? ' and email address' : '' }}:
                        <br>+91 {{ $customer->phone }}
                        @if($customer->email)
                            <br>{{ $customer->email }}
                        @endif
                    </p>
                    <button type="button" class="btn btn-primary w-100 py-2 fw-bold" id="btnSendDeleteOtp">
                        Send OTP
                    </button>
                    <div id="sendOtpMessage" class="small mt-2 text-danger"></div>
                </div>

                <div id="otpStep2" class="d-none">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Enter 6-digit OTP</label>
                        <input type="text" id="delete_otp_input" class="form-control text-center fs-4 letter-spacing-2" placeholder="------" maxlength="6">
                    </div>
                    <button type="button" class="btn btn-danger w-100 py-2 fw-bold" id="btnVerifyDeleteOtp">
                        Verify & Delete
                    </button>
                    <div id="verifyOtpMessage" class="small mt-2 text-danger"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-link btn-sm text-muted text-decoration-none" id="btnResendDeleteOtp">Resend OTP</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentDeleteId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteOtpModal'));

        window.initiateDelete = function(listingId) {
            currentDeleteId = listingId;
            document.getElementById('otpStep1').classList.remove('d-none');
            document.getElementById('otpStep2').classList.add('d-none');
            document.getElementById('delete_otp_input').value = '';
            document.getElementById('sendOtpMessage').textContent = '';
            document.getElementById('verifyOtpMessage').textContent = '';
            deleteModal.show();
        };

        document.getElementById('btnSendDeleteOtp').addEventListener('click', sendDeleteOtp);
        document.getElementById('btnResendDeleteOtp').addEventListener('click', sendDeleteOtp);

        function sendDeleteOtp() {
            const btn = document.getElementById('btnSendDeleteOtp');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
            document.getElementById('sendOtpMessage').textContent = '';

            fetch("{{ route('customer.listing.delete.otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Send OTP';
                if (data.success) {
                    document.getElementById('otpStep1').classList.add('d-none');
                    document.getElementById('otpStep2').classList.remove('d-none');
                    document.getElementById('delete_otp_input').focus();
                } else {
                    document.getElementById('sendOtpMessage').textContent = data.message || 'Failed to send OTP.';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Send OTP';
                document.getElementById('sendOtpMessage').textContent = 'An error occurred. Please try again.';
            });
        }

        document.getElementById('btnVerifyDeleteOtp').addEventListener('click', function() {
            const otp = document.getElementById('delete_otp_input').value.trim();
            const msg = document.getElementById('verifyOtpMessage');
            
            if(!/^[0-9]{6}$/.test(otp)) {
                msg.textContent = 'Please enter a valid 6-digit OTP.';
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verifying...';
            msg.textContent = '';

            fetch(`/customer/listing/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp: otp })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    deleteModal.hide();
                    window.location.reload();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Verify & Delete';
                    msg.textContent = data.message || 'Invalid OTP';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Verify & Delete';
                msg.textContent = 'An error occurred. Please try again.';
            });
        });
    });
</script>
@endsection
