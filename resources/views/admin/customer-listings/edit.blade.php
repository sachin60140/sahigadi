@extends('layouts.admin')

@section('title', 'Edit Listing - ' . $listing->title . ' - SAHIGADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-pencil me-2"></i>Edit Listing</h4>
        <small class="text-muted">Update customer car listing</small>
    </div>
    <a href="{{ route('admin.customer-listings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.customer-listings.update', $listing) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ $listing->title }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $listing->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="form-control" value="{{ $listing->model }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" value="{{ $listing->year }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fuel Type</label>
                    <select name="fuel_type" class="form-select">
                        <option value="">Select</option>
                        <option value="petrol" {{ $listing->fuel_type == 'petrol' ? 'selected' : '' }}>Petrol</option>
                        <option value="diesel" {{ $listing->fuel_type == 'diesel' ? 'selected' : '' }}>Diesel</option>
                        <option value="electric" {{ $listing->fuel_type == 'electric' ? 'selected' : '' }}>Electric</option>
                        <option value="hybrid" {{ $listing->fuel_type == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="cng" {{ $listing->fuel_type == 'cng' ? 'selected' : '' }}>CNG</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transmission</label>
                    <select name="transmission" class="form-select">
                        <option value="">Select</option>
                        <option value="manual" {{ $listing->transmission == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="automatic" {{ $listing->transmission == 'automatic' ? 'selected' : '' }}>Automatic</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" value="{{ $listing->price }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ $listing->city }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">KM Driven</label>
                    <input type="number" name="km_driven" class="form-control" value="{{ $listing->km_driven }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" value="{{ $listing->registration_number }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Number of Owners</label>
                    <input type="number" name="owners" class="form-control" value="{{ $listing->owners }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Phone <span class="text-danger">*</span></label>
                    <input type="text" name="owner_phone" class="form-control" value="{{ $listing->owner_phone }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" value="{{ $listing->owner_name }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="{{ $listing->whatsapp_number }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $listing->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $listing->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $listing->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>

            @php
                $images = json_decode($listing->images, true) ?? [];
            @endphp
            @if(count($images) > 0)
            <div class="mt-4 border-top pt-4">
                <h6 class="mb-3">Car Images (Select Featured Image)</h6>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($images as $index => $image)
                    <div class="position-relative border p-2 rounded {{ $index === 0 ? 'bg-primary-subtle border-primary' : 'bg-light' }}">
                        <a href="{{ asset('storage/' . $image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $image) }}" class="rounded" style="width: 120px; height: 90px; object-fit: cover;">
                        </a>
                        <div class="mt-2 text-center">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="radio" name="primary_image" id="img{{ $index }}" value="{{ $image }}" {{ $index === 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="img{{ $index }}">Featured</label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <small class="text-muted d-block mt-2">The selected image will be shown as the main thumbnail.</small>
            </div>
            @endif

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Listing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
