@extends('layouts.dealer')

@section('title', 'Maruti Service History - SAHIGADI')

@php
$dealer = auth('dealer')->user();
$walletBalance = $dealer->walletBalance();
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>Maruti Service History</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-wallet2 text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">Wallet Balance</h6>
                <h3 class="mb-0 text-success">₹{{ number_format($walletBalance, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-currency-rupee text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">Charge Per Search</h6>
                <h3 class="mb-0">₹{{ number_format($charge, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-receipt text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">Total Searches</h6>
                <h3 class="mb-0">{{ $searches->total() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Search Service History</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('dealer.maruti-service-history.search') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="vehicle_number" class="form-control form-control-lg text-uppercase @error('vehicle_number') is-invalid @enderror" 
                           placeholder="e.g. MH12AB1234" maxlength="20" required>
                    @error('vehicle_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Enter vehicle registration number</small>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100" {{ $walletBalance < $charge ? 'disabled' : '' }}>
                        <i class="bi bi-search me-2"></i>Search Now
                    </button>
                </div>
            </div>
        </form>
        @if($walletBalance < $charge)
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Insufficient wallet balance. Please <a href="{{ route('dealer.wallet.add') }}">add money</a> to search.
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Search History</h5>
            <form action="{{ route('dealer.maruti-service-history.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search by reg number..." value="{{ request('search') }}">
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Registration No.</th>
                        <th>Services Found</th>
                        <th>Status</th>
                        <th>Charges</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($searches as $search)
                    <tr>
                        <td class="fw-bold">{{ $search->vehicle_number }}</td>
                        <td>{{ $search->records->count() }}</td>
                        <td>
                            @if($search->is_success)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Success</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>
                            @endif
                        </td>
                        <td>₹{{ number_format($search->debit_amount ?? 0, 2) }}</td>
                        <td>{{ $search->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('dealer.maruti-service-history.show', $search) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($search->is_success)
                                <a href="{{ route('dealer.maruti-service-history.pdf', $search) }}" class="btn btn-outline-danger">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-car-front text-secondary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-secondary">No searches yet</h5>
                            <p class="text-muted">Search for a vehicle using the form above</p>
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