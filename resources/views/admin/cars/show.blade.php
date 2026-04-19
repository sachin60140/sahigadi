@extends('layouts.admin')

@section('title', 'Car Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Car Details</h2>
    <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $car->title }}</h5>
            </div>
            <div class="card-body">
                @if($car->images->count() > 0)
                <div class="row">
                    @foreach($car->images as $image)
                    <div class="col-md-3 mb-2">
                        <img src="{{ $image->url }}" class="img-thumbnail" alt="Car Image">
                    </div>
                    @endforeach
                </div>
                @endif

                <hr>

                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" style="width: 150px;">Price</td>
                        <td><strong>₹{{ number_format($car->price ?? 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Year</td>
                        <td>{{ $car->year ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fuel Type</td>
                        <td>{{ ucfirst($car->fuel_type ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Transmission</td>
                        <td>{{ ucfirst($car->transmission ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">KM Driven</td>
                        <td>{{ $car->km_driven ? number_format($car->km_driven) . ' km' : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">City</td>
                        <td>{{ $car->city ?? 'N/A' }}</td>
                    </tr>
                    @if($car->latitude && $car->longitude)
                    <tr>
                        <td class="text-muted">Location</td>
                        <td>
                            <span class="me-2">{{ $car->latitude }}, {{ $car->longitude }}</span>
                            <a href="https://www.google.com/maps?q={{ $car->latitude }},{{ $car->longitude }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-geo-alt"></i> View on Map
                            </a>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Owners</td>
                        <td>{{ $car->owners ?? 1 }}</td>
                    </tr>
                </table>

                @if($car->description)
                <hr>
                <h6>Description</h6>
                <p>{{ $car->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dealer</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $car->dealer->name ?? 'N/A' }}</strong></p>
                <p class="text-muted mb-1">{{ $car->dealer->email ?? '' }}</p>
                <p class="text-muted mb-0">{{ $car->dealer->phone ?? '' }}</p>
                <a href="{{ route('admin.dealers.show', $car->dealer_id) }}" class="btn btn-sm btn-outline-primary mt-2">View Dealer</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($car->status === 'pending')
                <form action="{{ route('admin.cars.approve', $car) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 mb-2"><i class="bi bi-check-circle"></i> Approve</button>
                </form>
                <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle"></i> Reject
                </button>
                @endif

                @if(!$car->isFeatured())
                <form action="{{ route('admin.cars.featured', $car) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100 mb-2"><i class="bi bi-star"></i> Make Featured</button>
                </form>
                @else
                <form action="{{ route('admin.cars.remove-featured', $car) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-100 mb-2"><i class="bi bi-star-fill"></i> Remove Featured</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.cars.reject', $car) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for rejection *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
