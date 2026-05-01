@extends('layouts.admin')

@section('title', 'Maruti Service History - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>Maruti Service History Searches</h2>
    <a href="{{ route('admin.maruti-service-histories.settings') }}" class="btn btn-outline-primary">
        <i class="bi bi-gear me-2"></i>Settings
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ $searches->total() }}</h3>
                <p class="text-muted mb-0">Total Searches</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success">Rs.{{ number_format($totalRevenue) }}</h3>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info">Rs.{{ number_format($charge) }}</h3>
                <p class="text-muted mb-0">Charge Per Search</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.maruti-service-histories.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Vehicle Number" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
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
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Search</button>
                <a href="{{ route('admin.maruti-service-histories.index') }}" class="btn btn-outline-secondary">Clear</a>
                <div class="float-end">
                    <a href="{{ route('admin.maruti-service-histories.exportExcel', request()->query()) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>Excel
                    </a>
                    <a href="{{ route('admin.maruti-service-histories.exportPdf', request()->query()) }}" class="btn btn-danger">
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
                        <th>Dealer</th>
                        <th>Vehicle Number</th>
                        <th>Services</th>
                        <th>Charge</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($searches as $search)
                    <tr>
                        <td>{{ $search->id }}</td>
                        <td>{{ $search->dealer->name ?? 'N/A' }}</td>
                        <td>{{ $search->vehicle_number }}</td>
                        <td>{{ $search->service_count }}</td>
                        <td>Rs.{{ number_format($search->charge_amount ?? 0) }}</td>
                        <td>
                            @if($search->is_success)
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>{{ $search->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.maruti-service-histories.show', $search) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.maruti-service-histories.downloadPdf', $search) }}" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">No searches found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $searches->links() }}
    </div>
</div>
@endsection