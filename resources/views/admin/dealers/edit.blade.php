@extends('layouts.admin')

@section('title', 'Edit Dealer - SAHIGADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-gear me-2"></i>Edit Dealer</h4>
        <small class="text-muted">Update dealer information and documents</small>
    </div>
    <a href="{{ route('admin.dealers.show', $dealer) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="stat-card">
            <form action="{{ route('admin.dealers.update', $dealer) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <h5 class="mb-3"><i class="bi bi-person me-2"></i>Basic Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $dealer->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $dealer->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $dealer->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $dealer->company_name) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Full Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ old('address', $dealer->address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $dealer->city) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $dealer->state) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $dealer->pincode) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="pending" {{ old('status', $dealer->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $dealer->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status', $dealer->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>KYC Documents</h5>
                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Aadhaar Card</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Aadhaar Number</label>
                        <input type="text" name="kyc_document_number" class="form-control" value="{{ old('kyc_document_number', $dealer->kyc_document_number) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Update Aadhaar Document (PDF, JPG, PNG - Max 5MB)</label>
                        @if($dealer->kyc_document_path)
                        <div class="mb-2">
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current document uploaded</span>
                            <a href="{{ asset('storage/'.$dealer->kyc_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye me-1"></i>View Current
                            </a>
                        </div>
                        @endif
                        <input type="file" name="kyc_document" class="form-control @error('kyc_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('kyc_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">PAN Card</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" style="text-transform:uppercase" class="form-control" value="{{ old('pan_number', $dealer->pan_number) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Update PAN Document (PDF, JPG, PNG - Max 5MB)</label>
                        @if($dealer->pan_document_path)
                        <div class="mb-2">
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current document uploaded</span>
                            <a href="{{ asset('storage/'.$dealer->pan_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye me-1"></i>View Current
                            </a>
                        </div>
                        @endif
                        <input type="file" name="pan_document" class="form-control @error('pan_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('pan_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>GST Document</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $dealer->gst_number) }}" maxlength="15">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload GST Document (PDF, JPG, PNG - Max 5MB)</label>
                        @if($dealer->gst_document_path)
                        <div class="mb-2">
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current document uploaded</span>
                            <a href="{{ asset('storage/'.$dealer->gst_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye me-1"></i>View Current
                            </a>
                        </div>
                        @endif
                        <input type="file" name="gst_document" class="form-control @error('gst_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('gst_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update Dealer
                    </button>
                    <a href="{{ route('admin.dealers.show', $dealer) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
