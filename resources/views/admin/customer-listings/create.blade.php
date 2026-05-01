@extends('layouts.admin')

@section('title', 'Create Customer Listing - SAHI GADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-plus-circle me-2"></i>Create Customer Listing</h4>
        <small class="text-muted">Add a new customer car listing manually</small>
    </div>
    <a href="{{ route('admin.customer-listings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.customer-listings.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fuel Type</label>
                    <select name="fuel_type" class="form-select">
                        <option value="">Select</option>
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="cng">CNG</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transmission</label>
                    <select name="transmission" class="form-select">
                        <option value="">Select</option>
                        <option value="manual">Manual</option>
                        <option value="automatic">Automatic</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Phone <span class="text-danger">*</span></label>
                    <input type="text" name="owner_phone" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Create Listing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
