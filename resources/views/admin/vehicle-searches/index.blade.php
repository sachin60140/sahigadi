@extends('layouts.admin')

@section('title', 'RC Search Reports - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-search me-2"></i>RC Search Reports</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vehicle-searches.settings') }}" class="btn btn-outline-primary">
            <i class="bi bi-gear me-2"></i>Settings
        </a>
    </div>
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
                    <h3 class="mb-0">₹{{ number_format($charge, 2) }}</h3>
                    <small class="text-muted">Per Search</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-12">
                <form action="{{ route('admin.vehicle-searches.index') }}" method="GET" class="row g-2">
                    <div class="col-md-2">
                        <input type="text" name="search" class="form-control" placeholder="Reg Number..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="dealer_id" class="form-select">
                            <option value="">All Dealers</option>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}" {{ request('dealer_id') == $dealer->id ? 'selected' : '' }}>{{ $dealer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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
                        <button type="submit" class="btn btn-secondary"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="{{ route('admin.vehicle-searches.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="btn-group">
                    <a href="{{ route('admin.vehicle-searches.exportExcel', request()->query()) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                    </a>
                    <a href="{{ route('admin.vehicle-searches.exportPdf', request()->query()) }}" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Reg Number</th>
                        <th>Owner</th>
                        <th>Dealer</th>
                        <th>Vehicle</th>
                        <th>RC Status</th>
                        <th>Charge</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($searches as $search)
                    <tr>
                        <td class="fw-bold">{{ $search->registration_number }}</td>
                        <td>{{ Str::limit($search->owner_name ?? 'N/A', 20) }}</td>
                        <td>{{ $search->dealer->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit(($search->make ?? '') . ' ' . ($search->model ?? ''), 25) }}</td>
                        <td>
                            @if($search->is_success)
                                <span class="badge bg-success">{{ $search->rc_status ?? 'Active' }}</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>₹{{ number_format($search->charge_amount, 2) }}</td>
                        <td>{{ $search->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.vehicle-searches.show', $search) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.vehicle-searches.downloadPdf', $search) }}" class="btn btn-outline-danger">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-search text-secondary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-secondary">No searches found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($searches->hasPages())
        <div class="card-footer bg-white">
            {{ $searches->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
