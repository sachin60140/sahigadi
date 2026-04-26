@extends('layouts.admin')

@section('title', 'Customer Car Listings - SAHIGADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-badge me-2"></i>Customer Car Listings</h4>
        <small class="text-muted">Manage individual seller listings</small>
        @if($pendingCount > 0)
            <span class="badge bg-warning text-dark ms-2">{{ $pendingCount }} pending</span>
        @endif
    </div>
</div>

<div class="stat-card mb-4">
    <form action="{{ route('admin.customer-listings.index') }}" method="GET" class="row g-3">
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by title, model, phone, email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="{{ route('admin.customer-listings.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="table-modern">
    <table class="table table-modern mb-0">
        <thead>
            <tr>
                <th><i class="bi bi-car me-1"></i>Car</th>
                <th><i class="bi bi-person me-1"></i>Owner</th>
                <th><i class="bi bi-currency-rupee me-1"></i>Price</th>
                <th><i class="bi bi-star me-1"></i>Featured</th>
                <th><i class="bi bi-geo-alt me-1"></i>Location</th>
                <th><i class="bi bi-info-circle me-1"></i>Status</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listings as $listing)
            <tr>
                <td>
                    <strong>{{ Str::limit($listing->title, 30) }}</strong>
                    <br>
                    <small class="text-muted">
                        @if($listing->brand)
                            {{ $listing->brand->name }} |
                        @endif
                        {{ $listing->year ?? 'N/A' }}
                    </small>
                </td>
                <td>
                    {{ $listing->owner_name ?? 'N/A' }}
                </td>
                <td class="fw-bold">₹{{ number_format($listing->price ?? 0) }}</td>
                <td>
                    @if($listing->isFeatured())
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-star-fill me-1"></i>Featured</span>
                        <form action="{{ route('admin.customer-listings.remove-featured', $listing->slug) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Remove Featured">
                                <i class="bi bi-star"></i>
                            </button>
                        </form>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-star"></i>
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
                </td>
                <td>
                    @if($listing->latitude && $listing->longitude)
                        <a href="https://www.google.com/maps?q={{ $listing->latitude }},{{ $listing->longitude }}" target="_blank" class="btn btn-sm btn-outline-success" title="View on Google Maps">
                            <i class="bi bi-geo-alt"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($listing->status === 'approved')
                        <span class="badge bg-success badge-modern"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    @elseif($listing->status === 'pending')
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-clock me-1"></i>Pending</span>
                    @else
                        <span class="badge bg-danger badge-modern" title="{{ $listing->rejection_reason }}"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    @endif
                </td>
                <td>
                    <a href="{{ url('admin/customer-listings/' . $listing->slug) }}" class="btn btn-sm btn-outline-primary me-1" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.customer-listings.edit', $listing->slug) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if($listing->status === 'pending')
                        <form action="{{ url('admin/customer-listings/' . $listing->slug . '/approve') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-car-front" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No customer listings found</h5>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($listings->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $listings->withQueryString()->links() }}
</div>
@endif
@endsection
