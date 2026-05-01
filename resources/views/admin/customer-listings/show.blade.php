@extends('layouts.admin')

@section('title', 'View Listing - ' . $listing->title . ' - SAHI GADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <a href="{{ route('admin.customer-listings.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <h4><i class="bi bi-car-front-fill me-2"></i>{{ Str::limit($listing->title, 40) }}</h4>
        <small class="text-muted">
            Listed on {{ $listing->created_at ? $listing->created_at->format('d M Y, h:i A') : 'N/A' }}
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.customer-listings.edit', $listing->slug) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        @if($listing->status === 'pending')
            <form action="{{ url('admin/customer-listings/' . $listing->slug . '/approve') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Approve
                </button>
            </form>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-2"></i>Reject
            </button>
        @elseif($listing->status === 'approved')
            <span class="badge bg-success p-2"><i class="bi bi-check-circle me-1"></i>Approved</span>
        @else
            <span class="badge bg-danger p-2"><i class="bi bi-x-circle me-1"></i>Rejected</span>
        @endif
        
        @if($listing->isFeatured())
            <form action="{{ route('admin.customer-listings.remove-featured', $listing->slug) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-warning">
                    <i class="bi bi-star-fill me-2"></i>Remove Featured
                </button>
            </form>
        @else
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-star me-2"></i>Make Featured
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="{{ route('admin.customer-listings.featured', $listing->slug) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="days" value="7">
                            <button type="submit" class="dropdown-item">7 Days</button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.customer-listings.featured', $listing->slug) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="days" value="14">
                            <button type="submit" class="dropdown-item">14 Days</button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.customer-listings.featured', $listing->slug) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="days" value="30">
                            <button type="submit" class="dropdown-item">30 Days</button>
                        </form>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-car-front me-2 text-danger"></i>Car Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Title</small>
                            <p class="mb-0 fw-semibold">{{ $listing->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Brand</small>
                            <p class="mb-0 fw-semibold">{{ $listing->brand->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Model</small>
                            <p class="mb-0 fw-semibold">{{ $listing->model ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Year</small>
                            <p class="mb-0 fw-semibold">{{ $listing->year ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Fuel Type</small>
                            <p class="mb-0 fw-semibold text-capitalize">{{ $listing->fuel_type ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Transmission</small>
                            <p class="mb-0 fw-semibold text-capitalize">{{ $listing->transmission ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">KM Driven</small>
                            <p class="mb-0 fw-semibold">{{ $listing->km_driven ? number_format($listing->km_driven) . ' km' : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Price</small>
                            <p class="mb-0 fw-semibold text-danger">₹{{ number_format($listing->price ?? 0) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">City</small>
                            <p class="mb-0 fw-semibold">{{ $listing->city ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($listing->latitude && $listing->longitude)
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Location</small>
                            <p class="mb-0 fw-semibold">
                                {{ $listing->latitude }}, {{ $listing->longitude }}
                                <a href="https://www.google.com/maps?q={{ $listing->latitude }},{{ $listing->longitude }}" target="_blank" class="btn btn-sm btn-outline-success ms-2">
                                    <i class="bi bi-geo-alt"></i> View on Map
                                </a>
                            </p>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Registration Number</small>
                            <p class="mb-0 fw-semibold">{{ $listing->registration_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Number of Owners</small>
                            <p class="mb-0 fw-semibold">{{ $listing->owners ?? 1 }}{{ $listing->owners == 1 ? 'st' : ($listing->owners == 2 ? 'nd' : 'th') }} Owner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($listing->images)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-images me-2 text-danger"></i>Car Images</h5>
            </div>
            <div class="card-body">
                @php
                    $images = json_decode($listing->images, true) ?? [];
                @endphp
                @if(count($images) > 0)
                    <div class="row g-2">
                        @foreach($images as $image)
                            <div class="col-4 col-md-3 col-lg-2">
                                <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail" style="height: 120px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted mt-2 d-block">{{ count($images) }} images uploaded</small>
                @else
                    <p class="text-muted mb-0">No images uploaded</p>
                @endif
            </div>
        </div>
        @endif

        @if($listing->status === 'rejected' && $listing->rejection_reason)
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Rejection Reason:</strong> {{ $listing->rejection_reason }}
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-person me-2 text-danger"></i>Owner Details</h5>
            </div>
            <div class="card-body">
                <div class="detail-item mb-3">
                    <small class="text-muted">Name</small>
                    <p class="mb-0 fw-semibold">{{ $listing->owner_name ?? 'N/A' }}</p>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Phone</small>
                    <p class="mb-0">
                        <a href="tel:{{ $listing->owner_phone }}" class="fw-semibold text-success">
                            <i class="bi bi-telephone me-1"></i>{{ $listing->owner_phone }}
                        </a>
                    </p>
                </div>
                @if($listing->whatsapp_number)
                <div class="detail-item">
                    <small class="text-muted">WhatsApp</small>
                    <p class="mb-0">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $listing->whatsapp_number) }}" target="_blank" class="fw-semibold text-success">
                            <i class="bi bi-whatsapp me-1"></i>{{ $listing->whatsapp_number }}
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-danger"></i>Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    @if($listing->status === 'approved')
                        <span class="badge bg-success w-100 py-2"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    @elseif($listing->status === 'pending')
                        <span class="badge bg-warning text-dark w-100 py-2"><i class="bi bi-clock me-1"></i>Pending Review</span>
                    @else
                        <span class="badge bg-danger w-100 py-2"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    @endif
                </div>
                <hr>
                <form action="{{ url('admin/customer-listings/' . $listing->slug) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-2"></i>Delete Listing
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('admin/customer-listings/' . $listing->slug . '/reject') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  rows="3" required placeholder="Please explain why this listing is being rejected...">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
