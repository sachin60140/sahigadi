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
            <div class="col-md-3">
                <label for="from_date" class="form-label text-muted small fw-medium">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label text-muted small fw-medium">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <label for="payment_gateway" class="form-label text-muted small fw-medium">Gateway</label>
                <select class="form-select" id="payment_gateway" name="payment_gateway">
                    <option value="">All</option>
                    <option value="razorpay" {{ request('payment_gateway') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                    <option value="phonepe" {{ request('payment_gateway') == 'phonepe' ? 'selected' : '' }}>PhonePe</option>
                    <option value="direct_deposit" {{ request('payment_gateway') == 'direct_deposit' ? 'selected' : '' }}>Direct Deposit</option>
                </select>
            </div>
            <div class="col-md-3">
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
                        <th>Transaction Details</th>
                        <th>Dealer Info</th>
                        <th style="min-width: 150px;">Amount Details</th>
                        <th>Payment & Gateway</th>
                        <th class="text-end">Status & Action</th>
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
                                <div class="fw-medium text-dark">{{ $txn->created_at->format('d M Y') }}</div>
                                <small class="text-muted d-block mb-1">{{ $txn->created_at->format('h:i A') }}</small>
                                <span class="badge bg-light text-dark border mt-1"><i class="bi bi-file-earmark-text"></i> {{ $receipt }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary mb-1">{{ $txn->wallet->dealer->company_name ?? 'N/A' }}</div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-medium text-dark" style="font-size: 0.85rem;">{{ $txn->wallet->dealer->name ?? 'Unknown Dealer' }}</span>
                                    <small class="text-muted"><i class="bi bi-telephone-fill" style="font-size: 0.7rem;"></i> {{ $txn->wallet->dealer->phone ?? '' }}</small>
                                </div>
                                @if(!empty($txn->wallet->dealer->gst_number))
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.7rem;">GST: {{ $txn->wallet->dealer->gst_number }}</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size: 0.7rem;">Unregistered</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                                        <span class="text-muted">Base:</span>
                                        <span class="text-success fw-medium">₹{{ number_format($base, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                                        <span class="text-muted">GST (18%):</span>
                                        <span class="text-danger">₹{{ number_format($gst, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1 mt-1 border-top" style="font-size: 0.9rem;">
                                        <span class="fw-bold text-dark">Total:</span>
                                        <span class="fw-bold text-dark">₹{{ number_format($total, 2) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    @if($txn->reference_type === 'admin_credit')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1"><i class="bi bi-bank me-1"></i>Direct Deposit</span>
                                        <small class="user-select-all text-secondary d-block mt-1" style="font-size:0.75rem;" title="Reference ID"><strong class="text-dark">Ref:</strong> {{ $txn->reference_id ?? 'N/A' }}</small>
                                    @else
                                        @php
                                            $paymentMode = 'Razorpay';
                                            $icon = 'bi-credit-card';
                                            $colorClass = 'info';
                                            
                                            $paymentRecord = \App\Models\Payment::where('razorpay_payment_id', $txn->reference_id)
                                                ->orWhere('phonepe_transaction_id', $txn->reference_id)
                                                ->first();
                                                
                                            $orderId = $paymentRecord && $paymentRecord->razorpay_order_id ? $paymentRecord->razorpay_order_id : null;
                                            $txnId = $txn->reference_id ?? 'N/A';
                                            
                                            if (str_starts_with($txn->reference_id, 'PP_')) {
                                                $paymentMode = 'PhonePe';
                                                $icon = 'bi-phone';
                                                $colorClass = 'success';
                                                $txnId = $txn->reference_id; // PhonePe calls this Transaction ID
                                                $orderId = $paymentRecord && $paymentRecord->reference_id ? $paymentRecord->reference_id : 'Processing / Bank Ref Pending'; // UTR / Provider Ref
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $colorClass }} bg-opacity-10 text-{{ $colorClass }} border border-{{ $colorClass }} border-opacity-25 mb-1"><i class="bi {{ $icon }} me-1"></i>{{ $paymentMode }}</span>
                                        
                                        <div class="mt-1 w-100">
                                            <small class="user-select-all text-secondary d-block mb-1 text-break" style="font-size:0.75rem;"><strong class="text-dark">Txn:</strong> {{ $txnId }}</small>
                                            @if($orderId)
                                                <small class="user-select-all text-secondary d-block text-break" style="font-size:0.75rem;"><strong class="text-dark">{{ str_starts_with($txn->reference_id, 'PP_') ? 'UTR/Bank' : 'Ord' }}:</strong> {{ $orderId }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                                    <a href="{{ route('admin.wallet-recharges.receipt', $txn->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.75rem;" title="Download Receipt">
                                        <i class="bi bi-download me-1"></i> Receipt
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
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
