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
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Your city">
                                @error('city')
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

                        <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>KYC Document * (Mandatory)</h5>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Required:</strong> KYC document is mandatory for dealer registration. Please upload any one of the following: Aadhaar Card, PAN Card, Voter ID, or Driving License.
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Document Type *</label>
                                <select name="kyc_document_type" class="form-select @error('kyc_document_type') is-invalid @enderror" required>
                                    <option value="">Select Document Type</option>
                                    <option value="aadhar" {{ old('kyc_document_type') == 'aadhar' ? 'selected' : '' }}>Aadhaar Card</option>
                                    <option value="pan" {{ old('kyc_document_type') == 'pan' ? 'selected' : '' }}>PAN Card</option>
                                    <option value="voter_id" {{ old('kyc_document_type') == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                                    <option value="driving_license" {{ old('kyc_document_type') == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                                </select>
                                @error('kyc_document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Document Number *</label>
                                <input type="text" name="kyc_document_number" class="form-control @error('kyc_document_number') is-invalid @enderror" value="{{ old('kyc_document_number') }}" required placeholder="Enter document number">
                                @error('kyc_document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Upload KYC Document * (PDF, JPG, PNG - Max 5MB)</label>
                                <input type="file" name="kyc_document" class="form-control @error('kyc_document') is-invalid @enderror" required accept=".pdf,.jpg,.jpeg,.png">
                                @error('kyc_document')
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
