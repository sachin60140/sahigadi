@extends('layouts.admin')

@section('title', 'Service Tracking - E-Challan - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt me-2"></i>E-Challan Search Tracking</h2>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon primary">
                    <i class="bi bi-search"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $totalSearches }}</h3>
                    <small class="text-muted">Total Searches</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $successfulSearches }}</h3>
                    <small class="text-muted">Successful</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon warning">
                    <i class="bi bi-currency-rupee"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">₹{{ number_format($totalRevenue, 2) }}</h3>
                    <small class="text-muted">Total Revenue</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon info">
                    <i class="bi bi-tag"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">C: ₹{{ number_format($charge) }} | D: ₹{{ number_format($dealerCharge) }}</h3>
                    <small class="text-muted">Charges</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="{{ route('admin.service-tracking.challan-search') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search vehicle..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('admin.service-tracking.challan-search') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>User Name</th>
                        <th>Phone</th>
                        <th>Vehicle Number</th>
                        <th>Status</th>
                        <th>Charge</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allSearches as $search)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $search->type == 'dealer' ? 'primary' : 'info' }}">
                                {{ ucfirst($search->type) }}
                            </span>
                        </td>
                        <td>{{ $search->user_name }}</td>
                        <td>{{ $search->customer_phone ?? $search->dealer->phone ?? 'N/A' }}</td>
                        <td>{{ $search->vehicle_number }}</td>
                        <td>
                            @if($search->is_success)
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>₹{{ number_format($search->charge_amount ?? $search->paid_amount ?? 0) }}</td>
                        <td>{{ $search->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.service-tracking.challan-search.show', ['id' => $search->id, 'type' => $search->type]) }}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="bi bi-eye"></i></a>
                            @if($search->is_success)
                                <a href="{{ route('admin.service-tracking.challan-search.pdf', ['id' => $search->id, 'type' => $search->type]) }}" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-pdf"></i></a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No searches found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection