@extends('layouts.admin')

@section('title', 'Customer Maruti Service History - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>Customer Maruti Service History Searches</h2>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ $searches->total() }}</h3>
                <p class="text-muted mb-0">Total Searches</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success">Rs.{{ number_format($totalRevenue) }}</h3>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.customer-maruti-service-histories.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Vehicle No, Name, Phone" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Search</button>
                <a href="{{ route('admin.customer-maruti-service-histories.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
            <div class="col-12 mt-3">
                <div class="float-end">
                    <a href="{{ route('admin.customer-maruti-service-histories.exportExcel', request()->query()) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>Excel
                    </a>
                    <a href="{{ route('admin.customer-maruti-service-histories.exportPdf', request()->query()) }}" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Vehicle Number</th>
                        <th>Charge Paid</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($searches as $search)
                    <tr>
                        <td>{{ $search->id }}</td>
                        <td>
                            {{ $search->customer_name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $search->customer_phone ?? 'N/A' }}</small>
                        </td>
                        <td class="text-uppercase fw-bold">{{ $search->vehicle_number }}</td>
                        <td class="fw-bold text-success">Rs.{{ number_format($search->paid_amount ?? 0) }}</td>
                        <td>
                            @if($search->is_success)
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>{{ $search->created_at->format('d M Y h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.customer-maruti-service-histories.show', $search) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($search->is_success)
                            <a href="{{ route('admin.customer-maruti-service-histories.downloadPdf', $search) }}" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No searches found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $searches->links() }}
    </div>
</div>
@endsection
