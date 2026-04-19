@extends('layouts.dealer')

@section('title', 'Add Car')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add New Car</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('dealer.cars.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}">
                        @error('model')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year') }}" min="1900" max="{{ date('Y') }}">
                        @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror">
                            <option value="">Select</option>
                            @foreach($fuelTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('fuel_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('fuel_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Transmission</label>
                        <select name="transmission" class="form-select @error('transmission') is-invalid @enderror">
                            <option value="">Select</option>
                            @foreach($transmissions as $key => $label)
                            <option value="{{ $key }}" {{ old('transmission') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('transmission')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Kilometers Driven</label>
                        <input type="number" name="km_driven" class="form-control @error('km_driven') is-invalid @enderror" value="{{ old('km_driven') }}">
                        @error('km_driven')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Price (₹) *</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" min="0" required>
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                        @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function(position) {
                                            document.getElementById('latitude').value = position.coords.latitude;
                                            document.getElementById('longitude').value = position.coords.longitude;
                                        }, function(error) {
                                            console.log('Location error:', error.message);
                                        });
                                    }
                                });
                                </script>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Registration Number</label>
                        <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number') }}">
                        @error('registration_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">No. of Owners</label>
                        <select name="owners" class="form-select @error('owners') is-invalid @enderror">
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('owners', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('owners')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Images (Max 10)</label>
                        <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" multiple accept="image/*">
                        @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">First image will be used as primary image.</small>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Submit for Review</button>
            <a href="{{ route('dealer.cars.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
