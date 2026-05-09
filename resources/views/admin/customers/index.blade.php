@extends('layouts.admin')

@section('title', 'Manage Customers - SAHI GADI Admin')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-hearts me-2"></i>Manage Customers</h4>
        <small class="text-muted">View and manage all registered customers on the platform</small>
    </div>
</div>

<div class="stat-card mb-4">
    <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-3">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control" placeholder="Search by ID, name, email, phone..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary w-100 text-center">Clear</a>
        </div>
    </form>
</div>

<div class="table-modern">
    <table class="table mb-0">
        <thead>
            <tr>
                <th><i class="bi bi-hash me-1"></i>ID</th>
                <th><i class="bi bi-person me-1"></i>Name</th>
                <th><i class="bi bi-envelope me-1"></i>Email</th>
                <th><i class="bi bi-telephone me-1"></i>Phone</th>
                <th><i class="bi bi-geo-alt me-1"></i>City</th>
                <th><i class="bi bi-wallet2 me-1"></i>Wallet Balance</th>
                <th><i class="bi bi-calendar3 me-1"></i>Joined</th>
                <th><i class="bi bi-person-badge me-1"></i>Profile</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td><span class="badge bg-secondary">{{ $customer->customer_unique_id }}</span></td>
                <td>
                    <strong>{{ $customer->name }}</strong>
                    @if($customer->company_name)
                    <br><small class="text-muted">{{ Str::limit($customer->company_name, 20) }}</small>
                    @endif
                </td>
                <td>{{ Str::limit($customer->email, 25) }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->city ?? 'N/A' }}</td>
                <td>
                    @if($customer->wallet)
                        <strong class="text-success">₹{{ number_format($customer->wallet->balance, 2) }}</strong>
                    @else
                        <span class="text-muted">₹0.00</span>
                    @endif
                </td>
                <td>{{ $customer->created_at->format('d M Y') }}</td>
                <td>
                    <div class="progress mb-1" style="height: 6px;">
                        <div class="progress-bar {{ $customer->profile_completion_percentage >= 75 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $customer->profile_completion_percentage }}%;" aria-valuenow="{{ $customer->profile_completion_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted">{{ $customer->profile_completion_percentage }}% Completed</small>
                </td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                        <i class="bi bi-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No customers found</h5>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($customers->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $customers->withQueryString()->links() }}
</div>
@endif
@endsection
