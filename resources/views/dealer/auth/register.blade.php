@extends('layouts.app')

@section('title', 'Dealer Registration - SAHIGADI')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2">Dealer Registration</h1>
            <p>Join SAHIGADI - Your trusted car marketplace</p>
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
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="+91 XXXXXXXXXX">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <button type="submit" class="btn btn-accent btn-lg py-3">
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
