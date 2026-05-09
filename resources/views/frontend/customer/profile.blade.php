@extends('layouts.app')

@section('title', 'Edit Profile - SAHI GADI')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('frontend.customer.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex align-items-center mb-4">
                <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h2 class="fw-bold mb-0">Edit Profile</h2>
            </div>

            @php
                if($customer->profile_completion_percentage === 0) {
                    $customer->calculateProfileCompletion();
                }
                $completion = $customer->profile_completion_percentage;
                $missingFields = $customer->getMissingProfileFields();
            @endphp

            @if($completion < 75)
            <div class="alert alert-warning shadow-sm border-warning border-opacity-50 mb-4 rounded-4">
                <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Profile Incomplete</h5>
                <p class="mb-2">Your profile is currently at <strong>{{ $completion }}%</strong>. You need to complete at least <strong>75%</strong> of your profile to access all features like the Dashboard, Wallet, and Service History.</p>
                <hr class="border-warning opacity-25 my-2">
                <p class="mb-1 fw-medium">Missing Required Fields:</p>
                <ul class="mb-0 small">
                    @foreach($missingFields as $field)
                        <li>{{ $field }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Profile Completion</h6>
                            <span class="badge {{ $completion >= 75 ? 'bg-success' : 'bg-warning text-dark' }} fs-6">{{ $completion }}%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar {{ $completion >= 75 ? 'bg-success' : 'bg-warning progress-bar-striped progress-bar-animated' }}" role="progressbar" style="width: {{ $completion }}%;" aria-valuenow="{{ $completion }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4 d-none d-lg-block text-center">
                            @if($customer->profile_image)
                                <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="Profile" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-person text-secondary" style="font-size: 2.5rem;"></i>
                                </div>
                            @endif
                            <h5 class="fw-bold mb-1">+91 {{ $customer->phone }}</h5>
                            <p class="text-muted small mb-1"><i class="bi bi-check-circle-fill text-success me-1"></i>Verified Mobile Number</p>
                            <p class="badge bg-secondary mb-0">ID: {{ $customer->customer_unique_id }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Profile Photo <span class="text-danger">*</span></label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                            <div class="form-text">Upload a square image for best results (Max 2MB).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" placeholder="Enter your email address">
                            <div class="form-text">We'll use this to send you important updates about your listings.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $customer->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $customer->dob ? $customer->dob->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $customer->whatsapp_number) }}" placeholder="For easier communication">
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Identity Information <span class="text-danger">*</span></h5>
                        <p class="text-muted small mb-3">Please provide either Aadhaar or PAN to verify your identity.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Aadhaar Number</label>
                                <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number', $customer->aadhaar_number) }}" placeholder="12 Digit Aadhaar Number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">PAN Number</label>
                                <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number', $customer->pan_number) }}" placeholder="10 Digit PAN Number">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Address Information</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $customer->company_name) }}" placeholder="If you're representing a business">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">GST Number</label>
                            <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $customer->gst_number) }}" placeholder="GSTIN">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Enter your complete address">{{ old('address', $customer->address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}" placeholder="City">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" value="{{ old('state', $customer->state) }}" placeholder="State">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $customer->pincode) }}" placeholder="Pincode">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
