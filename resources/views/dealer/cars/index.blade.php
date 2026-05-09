@extends('layouts.dealer')

@section('title', 'My Cars')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Cars</h2>
    <a href="{{ route('dealer.cars.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Car
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('dealer.cars.index') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('dealer.cars.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cars as $car)
                    <tr>
                        <td>
                            @if($car->image_url)
                                <img src="{{ $car->image_url }}" alt="" style="width: 80px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                    <i class="bi bi-car-front text-white"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dealer.cars.edit', $car) }}">{{ Str::limit($car->title, 30) }}</a>
                            <br><small class="text-muted">#{{ $car->unique_id }}</small>
                            @if($car->rejection_reason)
                                <br><small class="text-danger">{{ $car->rejection_reason }}</small>
                            @endif
                        </td>
                        <td>₹{{ number_format($car->price ?? 0) }}</td>
                        <td>
                            @if($car->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($car->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($car->isFeatured())
                                <span class="badge bg-warning"><i class="bi bi-star-fill"></i> Featured</span>
                                @if($car->featured_expires_at)
                                    <br><small class="text-muted">Till: {{ \Carbon\Carbon::parse($car->featured_expires_at)->format('d M Y') }}</small>
                                @endif
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </td>
                        <td>
                            @if($car->status === 'approved')
                                @if(!$car->isFeatured())
                                    <a href="{{ route('dealer.cars.featured-plans', $car) }}" class="btn btn-sm btn-warning text-dark fw-bold" title="Make Featured">
                                        <i class="bi bi-star"></i> Feature
                                    </a>
                                @else
                                    <a href="{{ route('dealer.cars.featured-plans', $car) }}" class="btn btn-sm btn-outline-warning" title="Extend Featured Plan">
                                        <i class="bi bi-star-fill"></i> Extend
                                    </a>
                                @endif
                            @endif
                            <a href="{{ route('dealer.cars.edit', $car) }}" class="btn btn-sm btn-outline-primary ms-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('dealer.cars.destroy', $car) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No cars found. <a href="{{ route('dealer.cars.create') }}">Add your first car</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $cars->withQueryString()->links() }}
    </div>
</div>
@endsection
