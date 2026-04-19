@extends('layouts.admin')

@section('title', 'Manage Dealers - SAHIGADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-people-fill me-2"></i>Manage Dealers</h4>
        <small class="text-muted">View and manage all registered dealers</small>
    </div>
    <a href="{{ route('admin.dealers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Add Dealer
    </a>
</div>

<div class="stat-card mb-4">
    <form action="{{ route('admin.dealers.index') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="{{ route('admin.dealers.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="table-modern">
    <table class="table table-modern mb-0">
        <thead>
            <tr>
                <th><i class="bi bi-person me-1"></i>Name</th>
                <th><i class="bi bi-envelope me-1"></i>Email</th>
                <th><i class="bi bi-telephone me-1"></i>Phone</th>
                <th><i class="bi bi-geo-alt me-1"></i>City</th>
                <th><i class="bi bi-info-circle me-1"></i>Status</th>
                <th><i class="bi bi-calendar3 me-1"></i>Joined</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dealers as $dealer)
            <tr>
                <td>
                    <strong>{{ $dealer->name }}</strong>
                    @if($dealer->company_name)
                    <br><small class="text-muted">{{ Str::limit($dealer->company_name, 20) }}</small>
                    @endif
                </td>
                <td>{{ Str::limit($dealer->email, 25) }}</td>
                <td>{{ $dealer->phone }}</td>
                <td>{{ $dealer->city ?? 'N/A' }}</td>
                <td>
                    @if($dealer->status === 'approved')
                        <span class="badge bg-success badge-modern"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    @elseif($dealer->status === 'pending')
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-clock me-1"></i>Pending</span>
                    @else
                        <span class="badge bg-danger badge-modern"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    @endif
                </td>
                <td>{{ $dealer->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.dealers.show', $dealer) }}" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if($dealer->status === 'pending')
                    <form action="{{ route('admin.dealers.approve', $dealer) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No dealers found</h5>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($dealers->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $dealers->withQueryString()->links() }}
</div>
@endif
@endsection
