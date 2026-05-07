@extends('layouts.admin')

@section('title', 'Edit Customer - SAHI GADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-pencil-square me-2"></i>Edit Customer: {{ $customer->name }}</h4>
        <small class="text-muted">Update customer profile details. The customer will be notified via email.</small>
    </div>
    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Details
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h5 class="mb-3 border-bottom pb-2">Primary Information</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2">Business & Additional Details</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number', $customer->whatsapp_number) }}">
                    @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Company Name</label>
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $customer->company_name) }}">
                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">GST Number</label>
                    <input type="text" name="gst_number" class="form-control @error('gst_number') is-invalid @enderror" value="{{ old('gst_number', $customer->gst_number) }}">
                    @error('gst_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2">Address Information</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-medium">Full Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $customer->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">City</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $customer->city) }}">
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $customer->state) }}">
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Pincode</label>
                    <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $customer->pincode) }}">
                    @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Save Changes & Notify</button>
            </div>
        </form>
    </div>
</div>
@endsection
