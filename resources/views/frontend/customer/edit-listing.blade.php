@extends('layouts.app')

@section('title', 'Edit Car Listing | SAHI GADI')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-3">Edit Your Car Listing</h1>
            <p class="lead text-white-50">Update your car details</p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5">
                        <h4 class="fw-bold mb-4"><i class="bi bi-car-front me-2 text-danger"></i>Car Details</h4>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('customer.listing.update', $listing->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Listing Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                           placeholder="e.g., 2020 Maruti Swift VXi" value="{{ old('title', $listing->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Brand</label>
                                    <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $listing->brand_id) == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Model</label>
                                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                                           placeholder="e.g., Swift VXi" value="{{ old('model', $listing->model) }}">
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Year</label>
                                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" 
                                           placeholder="e.g., 2020" value="{{ old('year', $listing->year) }}" min="1900" max="{{ date('Y') }}">
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Fuel Type</label>
                                    <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @foreach($fuelTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('fuel_type', $listing->fuel_type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fuel_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transmission</label>
                                    <select name="transmission" class="form-select @error('transmission') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @foreach($transmissions as $key => $label)
                                            <option value="{{ $key }}" {{ old('transmission', $listing->transmission) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('transmission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kilometers Driven</label>
                                    <input type="number" name="km_driven" class="form-control @error('km_driven') is-invalid @enderror" 
                                           placeholder="e.g., 45000" value="{{ old('km_driven', $listing->km_driven) }}" min="0">
                                    @error('km_driven')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expected Price (₹)</label>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                           placeholder="e.g., 500000" value="{{ old('price', $listing->price) }}" min="0">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City</label>
                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                           placeholder="e.g., Patna" value="{{ old('city', $listing->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                @error('latitude')
                                    <div class="col-12 mt-1">
                                        <div class="text-danger small">{{ $message }}</div>
                                    </div>
                                @enderror

                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const submitBtn = document.getElementById('submitBtn');
                                    
                                    function handleLocationError(error) {
                                        submitBtn.disabled = true;
                                        let errorMsg = 'Location access is strictly required to list your vehicle.';
                                        switch(error.code) {
                                            case error.PERMISSION_DENIED:
                                                errorMsg = 'You denied the request for Geolocation. Location access is strictly required to sell your car. Please allow permissions and refresh the page.';
                                                break;
                                        }
                                        alert(errorMsg);
                                    }

                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function(position) {
                                            document.getElementById('latitude').value = position.coords.latitude;
                                            document.getElementById('longitude').value = position.coords.longitude;
                                            
                                            submitBtn.disabled = false;
                                        }, handleLocationError, {
                                            enableHighAccuracy: true,
                                            timeout: 10000,
                                            maximumAge: 0
                                        });
                                    } else {
                                        alert('Geolocation is not supported by your browser.');
                                        submitBtn.disabled = true;
                                    }
                                });
                                </script>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" 
                                           placeholder="e.g., BR01AB1234" value="{{ old('registration_number', $listing->registration_number) }}">
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Number of Owners</label>
                                    <select name="owners" class="form-select @error('owners') is-invalid @enderror">
                                        <option value="1" {{ old('owners', $listing->owners) == 1 ? 'selected' : '' }}>1st Owner</option>
                                        <option value="2" {{ old('owners', $listing->owners) == 2 ? 'selected' : '' }}>2nd Owner</option>
                                        <option value="3" {{ old('owners', $listing->owners) == 3 ? 'selected' : '' }}>3rd Owner</option>
                                        <option value="4" {{ old('owners', $listing->owners) == 4 ? 'selected' : '' }}>4th Owner</option>
                                    </select>
                                    @error('owners')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Car Images <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal">(Minimum 5, Maximum 10 images)</small>
                                    </label>
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="row g-2" id="imagePreviewContainer">
                                            @if($listing->images)
                                                @php $images = json_decode($listing->images, true) ?? []; @endphp
                                                @foreach($images as $index => $img)
                                                    <div class="col-4 col-md-3 col-lg-2 mb-2 preview-item-col">
                                                        <div class="position-relative border p-1 rounded {{ $index === 0 ? 'bg-primary-subtle border-primary' : '' }}">
                                                            <img src="{{ asset('storage/' . $img) }}" class="img-thumbnail border-0 bg-transparent p-0" style="height: 100px; width: 100%; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="mt-3">
                                            <label for="car_images" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus-circle me-1"></i>Select Images
                                            </label>
                                            <input type="file" name="images[]" id="car_images" class="d-none" multiple accept="image/*" onchange="previewImages(this)">
                                            <input type="hidden" name="primary_image_index" id="primaryImageIndex" value="0">
                                            <small class="text-danger fw-bold d-block mt-2">Note: Uploading new images will replace all your current images.</small>
                                            <small class="text-muted d-block">Supported formats: JPG, PNG, JPEG, GIF (Max 2MB each)</small>
                                            <small class="text-muted d-block">Select a thumbnail above to set it as the featured primary image.</small>
                                            @error('images')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            @foreach($errors->keys() as $key)
                                                @if(\Illuminate\Support\Str::startsWith($key, 'images.'))
                                                    <div class="text-danger small mt-1">{{ $errors->first($key) }}</div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted" id="imageCount">0 / 10 images selected</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h4 class="fw-bold mb-4"><i class="bi bi-person me-2 text-danger"></i>Owner Details</h4>
                                <input type="hidden" name="owner_phone" value="{{ auth('customer')->user()->phone }}">
                                <input type="hidden" name="owner_name" value="{{ auth('customer')->user()->name }}">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Address (Optional)</label>
                                        <input type="email" name="owner_email" class="form-control @error('owner_email') is-invalid @enderror" 
                                               placeholder="For notifications" value="{{ old('owner_email', auth('customer')->user()->email) }}">
                                        @error('owner_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">WhatsApp Number</label>
                                        <input type="text" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                               placeholder="Enter your WhatsApp number" value="{{ old('whatsapp_number', auth('customer')->user()->whatsapp_number ?? auth('customer')->user()->phone) }}">
                                        @error('whatsapp_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                </div>

                            <div class="mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-accent btn-lg w-100" disabled>
                                    <i class="bi bi-send me-2"></i>Update Listing
                                </button>
                            </div>

                            <p class="text-muted small mt-3 text-center">
                                <i class="bi bi-info-circle me-1"></i>
                                Your listing will be reviewed by our team before it goes live. We will contact you for verification.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
async function compressImage(file, maxSizeMB = 2) {
    if (file.size <= maxSizeMB * 1024 * 1024) {
        return file;
    }

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = event => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                
                const MAX_DIM = 1920;
                if (width > height && width > MAX_DIM) {
                    height *= MAX_DIM / width;
                    width = MAX_DIM;
                } else if (height > MAX_DIM) {
                    width *= MAX_DIM / height;
                    height = MAX_DIM;
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                let quality = 0.8;
                let compressNext = () => {
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            reject(new Error('Canvas to Blob failed'));
                            return;
                        }
                        if (blob.size <= maxSizeMB * 1024 * 1024 || quality <= 0.1) {
                            const extension = file.name.split('.').pop().toLowerCase();
                            let newFileName = file.name;
                            if (!['jpg', 'jpeg'].includes(extension)) {
                                newFileName = file.name.substring(0, file.name.lastIndexOf('.')) + '.jpg';
                            }
                            
                            const compressedFile = new File([blob], newFileName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(compressedFile);
                        } else {
                            quality -= 0.1;
                            compressNext();
                        }
                    }, 'image/jpeg', quality);
                };
                compressNext();
            };
            img.onerror = error => reject(error);
        };
        reader.onerror = error => reject(error);
    });
}

async function previewImages(input) {
    const container = document.getElementById('imagePreviewContainer');
    const countLabel = document.getElementById('imageCount');
    const submitBtn = document.getElementById('submitBtn');
    
    if (input.files && input.files.length > 0) {
        const totalFiles = input.files.length;
        
        if (totalFiles > 10) {
            alert('Maximum 10 images allowed');
            input.value = '';
            container.innerHTML = '';
            countLabel.textContent = '0 / 10 images selected';
            countLabel.className = 'text-muted';
            return;
        }

        countLabel.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Compressing images...';
        countLabel.className = 'text-warning';
        
        // Save the original disabled state to restore it later
        let originalSubmitState = submitBtn.disabled;
        submitBtn.disabled = true;
        
        const dataTransfer = new DataTransfer();
        container.innerHTML = '';
        
        try {
            for (let i = 0; i < totalFiles; i++) {
                let file = input.files[i];
                if (file.type.startsWith('image/')) {
                    const compressedFile = await compressImage(file, 2);
                    dataTransfer.items.add(compressedFile);
                }
            }
            
            input.files = dataTransfer.files;
            
            const newTotal = input.files.length;
            countLabel.textContent = newTotal + ' / 10 images selected';
            
            if (newTotal < 5) {
                countLabel.className = 'text-danger';
            } else {
                countLabel.className = 'text-success';
            }
            
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 col-lg-2 mb-2 preview-item-col';
                    col.innerHTML = `
                        <div class="position-relative border p-1 rounded ${index === 0 ? 'bg-primary-subtle border-primary' : ''}" id="preview_col_${index}">
                            <img src="${e.target.result}" class="img-thumbnail border-0 bg-transparent p-0" style="height: 100px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeImage(this)" style="padding: 0.1rem 0.3rem;">
                                <i class="bi bi-x"></i>
                            </button>
                            <div class="mt-1 text-center">
                                <div class="form-check d-inline-block" style="min-height: auto;">
                                    <input class="form-check-input" type="radio" name="preview_primary" id="feature_${index}" value="${index}" ${index === 0 ? 'checked' : ''} onchange="setFeatured(${index})">
                                    <label class="form-check-label small" style="font-size: 0.75rem;" for="feature_${index}">Featured</label>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
            
            submitBtn.disabled = originalSubmitState;
            
        } catch (error) {
            console.error('Error compressing images:', error);
            alert('An error occurred while processing images. Please try again.');
            input.value = '';
            container.innerHTML = '';
            countLabel.textContent = '0 / 10 images selected';
            countLabel.className = 'text-muted';
            submitBtn.disabled = originalSubmitState;
        }
    } else {
        container.innerHTML = '';
        countLabel.textContent = '0 / 10 images selected';
        countLabel.className = 'text-muted';
    }
}

function removeImage(btn) {
    const input = document.getElementById('car_images');
    const container = document.getElementById('imagePreviewContainer');
    const col = btn.closest('.preview-item-col');
    const index = Array.from(container.children).indexOf(col);
    
    if (index > -1) {
        const dt = new DataTransfer();
        const files = input.files;
        
        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                dt.items.add(files[i]);
            }
        }
        
        input.files = dt.files;
        col.remove();
        
        const countLabel = document.getElementById('imageCount');
        const totalFiles = input.files.length;
        countLabel.textContent = totalFiles + ' / 10 images selected';
        
        if (totalFiles < 5) {
            countLabel.className = 'text-danger';
        } else {
            countLabel.className = 'text-success';
        }
        
        // Re-index remaining elements to prevent desync
        Array.from(container.children).forEach((child, newIndex) => {
            child.querySelector('div.position-relative').id = `preview_col_${newIndex}`;
            const radio = child.querySelector('input[type="radio"]');
            radio.id = `feature_${newIndex}`;
            radio.value = newIndex;
            radio.setAttribute('onchange', `setFeatured(${newIndex})`);
            child.querySelector('label').setAttribute('for', `feature_${newIndex}`);
            
            if (newIndex === 0 && !container.querySelector('input[type="radio"]:checked')) {
                radio.checked = true;
                setFeatured(0);
            }
        });
    }
}

function setFeatured(index) {
    document.getElementById('primaryImageIndex').value = index;
    document.querySelectorAll('.preview-item-col > div').forEach(el => {
        el.classList.remove('border-primary', 'bg-primary-subtle');
    });
    document.getElementById(`preview_col_${index}`).classList.add('border-primary', 'bg-primary-subtle');
}
</script>


@endsection
