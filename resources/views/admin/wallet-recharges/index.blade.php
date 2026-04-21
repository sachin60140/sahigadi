@extends('layouts.admin')

@section('title', 'Wallet Recharges')

@section('content')
<div class="top-bar flex-wrap gap-3">
    <div>
        <h4><i class="bi bi-cash-stack text-primary me-2"></i>Dealer Wallet Recharges</h4>
        <p class="text-muted mb-0">Trace all wallet recharge transactions across the platform.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.wallet-recharges.exportExcel', request()->all()) }}" class="btn btn-success btn-modern">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
        <a href="{{ route('admin.wallet-recharges.exportPdf', request()->all()) }}" class="btn btn-danger btn-modern">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.wallet-recharges.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="from_date" class="form-label text-muted small fw-medium">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-4">
                <label for="to_date" class="form-label text-muted small fw-medium">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.wallet-recharges.index') }}" class="btn btn-light ms-2">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-modern border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Receipt No</th>
                        <th>Company Name</th>
                        <th>Dealer Details</th>
                        <th>GST Number</th>
                        <th>Recharge (Base)</th>
                        <th>GST (18%)</th>
                        <th>Total Paid</th>
                        <th>Razorpay / Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                        @php
                            $base = $txn->amount;
                            $gst = $base * 0.18;
                            $total = $base + $gst;
                            $receipt = 'RCPT-' . $txn->created_at->format('Y') . '-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $txn->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $txn->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><i class="bi bi-file-earmark-text"></i> {{ $receipt }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $txn->wallet->dealer->company_name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $txn->wallet->dealer->name ?? 'Unknown Dealer' }}</div>
                                <small class="text-muted d-block">{{ $txn->wallet->dealer->phone ?? '' }}</small>
                            </td>
                            <td>
                                @if(!empty($txn->wallet->dealer->gst_number))
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $txn->wallet->dealer->gst_number }}</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Unregistered</span>
                                @endif
                            </td>
                            <td class="text-success fw-medium">₹{{ number_format($base, 2) }}</td>
                            <td class="text-danger">₹{{ number_format($gst, 2) }}</td>
                            <td class="fw-bold">₹{{ number_format($total, 2) }}</td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <small class="user-select-all text-secondary"><i class="bi bi-hash"></i> {{ $txn->reference_id ?? 'N/A' }}</small>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                                        <a href="{{ route('admin.wallet-recharges.receipt', $txn->id) }}" class="btn btn-sm btn-outline-primary" style="padding: 1px 6px; font-size: 0.75rem;" title="Download Receipt">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <h5>No wallet recharges found</h5>
                                    <p>Try adjusting your date filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
