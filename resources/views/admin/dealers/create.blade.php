@extends('layouts.admin')

@section('title', 'Add Dealer - SAHIGADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-plus me-2"></i>Add New Dealer</h4>
        <small class="text-muted">Create a new dealer account and assign plan</small>
    </div>
    <a href="{{ route('admin.dealers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="stat-card">
            <form action="{{ route('admin.dealers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="mb-3"><i class="bi bi-person me-2"></i>Basic Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>KYC Document</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Document Type</label>
                        <select name="kyc_document_type" class="form-select">
                            <option value="">Select Type</option>
                            <option value="aadhar" {{ old('kyc_document_type') == 'aadhar' ? 'selected' : '' }}>Aadhaar Card</option>
                            <option value="pan" {{ old('kyc_document_type') == 'pan' ? 'selected' : '' }}>PAN Card</option>
                            <option value="voter_id" {{ old('kyc_document_type') == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                            <option value="driving_license" {{ old('kyc_document_type') == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Number</label>
                        <input type="text" name="kyc_document_number" class="form-control" value="{{ old('kyc_document_number') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload KYC Document (PDF, JPG, PNG - Max 5MB)</label>
                        <input type="file" name="kyc_document" class="form-control @error('kyc_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('kyc_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>GST Document</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number') }}" maxlength="15">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload GST Document (PDF, JPG, PNG - Max 5MB)</label>
                        <input type="file" name="gst_document" class="form-control @error('gst_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('gst_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-3"><i class="bi bi-box-seam me-2"></i>Assign Plan (Optional)</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Select Plan</label>
                        <select name="assign_plan" class="form-select">
                            <option value="">No Plan</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('assign_plan') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - ₹{{ number_format($plan->price) }} ({{ $plan->listing_limit }} listings)
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">The selected plan will be assigned to this dealer immediately.</small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Create Dealer
                    </button>
                    <a href="{{ route('admin.dealers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
