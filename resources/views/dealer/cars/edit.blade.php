@extends('layouts.dealer')

@section('title', 'Edit Car')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Car</h2>
    <span class="badge bg-{{ $car->status === 'approved' ? 'success' : ($car->status === 'pending' ? 'warning' : 'danger') }}">
        {{ ucfirst($car->status) }}
    </span>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('dealer.cars.update', $car) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $car->title) }}" required>
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
                            <option value="{{ $brand->id }}" {{ old('brand_id', $car->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $car->model) }}">
                        @error('model')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $car->year) }}" min="1900" max="{{ date('Y') }}">
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
                            <option value="{{ $key }}" {{ old('fuel_type', $car->fuel_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                            <option value="{{ $key }}" {{ old('transmission', $car->transmission) == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                        <input type="number" name="km_driven" class="form-control @error('km_driven') is-invalid @enderror" value="{{ old('km_driven', $car->km_driven) }}">
                        @error('km_driven')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Price (₹) *</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $car->price) }}" min="0" required>
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $car->city) }}">
                        @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $car->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $car->longitude) }}">
                                @if(!$car->latitude)
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
                                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Registration Number</label>
                        <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number', $car->registration_number) }}">
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
                            <option value="{{ $i }}" {{ old('owners', $car->owners) == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $car->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Add More Images</label>
                        <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" multiple accept="image/*">
                        @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Update Car Details</button>
            <a href="{{ route('dealer.cars.index') }}" class="btn btn-secondary">Cancel</a>
        </form>

        @if($car->images->count() > 0)
        <div class="mt-5">
            <h5 class="form-label border-bottom pb-2">Manage Current Images</h5>
            <div class="d-flex flex-wrap gap-3 mt-3">
                @foreach($car->images as $image)
                <div class="position-relative border p-2 rounded bg-light">
                    <img src="{{ $image->url }}" alt="" class="rounded" style="width: 120px; height: 90px; object-fit: cover;">
                    @if($image->is_primary)
                    <span class="position-absolute top-0 start-0 badge bg-success m-2 shadow-sm" style="font-size: 0.65rem;">Primary</span>
                    @endif
                    <div class="mt-2 d-flex gap-1 justify-content-center">
                        <form action="{{ route('dealer.cars.image.primary', [$car, $image]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" {{ $image->is_primary ? 'disabled' : '' }}>Primary</button>
                        </form>
                        <form action="{{ route('dealer.cars.image.delete', [$car, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this image?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
