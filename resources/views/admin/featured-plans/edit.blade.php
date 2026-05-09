@extends('layouts.admin')

@section('title', 'Edit Featured Plan')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.featured-plans.index') }}" class="btn btn-light btn-sm me-3 border shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="mb-0 text-gray-800">Edit Featured Plan</h4>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.featured-plans.update', $featuredPlan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-medium">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $featuredPlan->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Duration (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', $featuredPlan->duration_days) }}" min="1" required>
                                @error('duration_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $featuredPlan->price) }}" min="0" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="is_active" value="1" {{ old('is_active', $featuredPlan->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium ms-2" for="isActive">Plan is Active (Visible to users)</label>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.featured-plans.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
