@extends('layouts.admin')

@section('title', 'Wallet Recharges')

@section('content')
<div class="top-bar flex-wrap gap-3">
    <div>
        <h4><i class="bi bi-cash-stack text-primary me-2"></i>Customer Wallet Recharges</h4>
        <p class="text-muted mb-0">Trace all customer wallet recharge transactions across the platform.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-warning btn-modern" data-bs-toggle="modal" data-bs-target="#deductModal">
            <i class="bi bi-dash-circle me-1"></i> Deduct Balance
        </button>
        <a href="{{ route('admin.customer-wallet-recharges.exportExcel', request()->all()) }}" class="btn btn-success btn-modern">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
        <a href="{{ route('admin.customer-wallet-recharges.exportPdf', request()->all()) }}" class="btn btn-danger btn-modern">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
    </div>
</div>

<!-- Deduct Balance Modal -->
<div class="modal fade" id="deductModal" tabindex="-1" aria-labelledby="deductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.customer-wallet-recharges.deduct') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title text-primary fw-bold" id="deductModalLabel"><i class="bi bi-dash-circle me-2"></i>Deduct Customer Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-4">Select a customer and enter the amount to deduct from their wallet.</p>
                    <div class="mb-3">
                        <label for="customer_id" class="form-label fw-medium">Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $customer)
                                @php
                                    $bal = $customer->wallet ? $customer->wallet->balance : 0;
                                @endphp
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }}) - Bal: ₹{{ number_format($bal, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-medium">Amount to Deduct (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" min="1" required placeholder="e.g. 500">
                    </div>
                    <div class="mb-3">
                        <label for="remark" class="form-label fw-medium">Remark / Reason</label>
                        <input type="text" class="form-control" id="remark" name="remark" required placeholder="Reason for deduction">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4">Confirm Deduction</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.customer-wallet-recharges.index') }}" method="GET" class="row g-3 align-items-end">
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
                <a href="{{ route('admin.customer-wallet-recharges.index') }}" class="btn btn-light ms-2">Clear</a>
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
                        <th>Customer Info</th>
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
                                <div class="fw-bold text-primary mb-1">{{ $txn->wallet->customer->company_name ?? 'N/A' }}</div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-medium text-dark" style="font-size: 0.85rem;">{{ $txn->wallet->customer->name ?? 'Unknown Customer' }}</span>
                                    <small class="text-muted"><i class="bi bi-telephone-fill" style="font-size: 0.7rem;"></i> {{ $txn->wallet->customer->phone ?? '' }}</small>
                                </div>
                                @if(!empty($txn->wallet->customer->gst_number))
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.7rem;">GST: {{ $txn->wallet->customer->gst_number }}</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size: 0.7rem;">Unregistered</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($txn->type === 'debit')
                                        <div class="d-flex justify-content-between pt-1 mt-1 border-top" style="font-size: 0.9rem;">
                                            <span class="fw-bold text-dark">Deducted:</span>
                                            <span class="fw-bold text-danger">-₹{{ number_format($txn->amount, 2) }}</span>
                                        </div>
                                    @else
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
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    @if($txn->type === 'debit')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 mb-1"><i class="bi bi-dash-circle me-1"></i>Admin Deduction</span>
                                        <small class="user-select-all text-secondary d-block mt-1 text-break" style="font-size:0.75rem;" title="Remark"><strong class="text-dark">Rmk:</strong> {{ $txn->remark ?? 'N/A' }}</small>
                                    @elseif($txn->reference_type === 'admin_credit')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1"><i class="bi bi-bank me-1"></i>Direct Deposit</span>
                                        <small class="user-select-all text-secondary d-block mt-1 text-break" style="font-size:0.75rem;" title="Reference ID"><strong class="text-dark">Ref:</strong> {{ $txn->reference_id ?? 'N/A' }}</small>
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
                                            
                                            if (str_starts_with($txn->reference_id, 'PP')) {
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
                                                <small class="user-select-all text-secondary d-block text-break" style="font-size:0.75rem;"><strong class="text-dark">{{ str_starts_with($txn->reference_id, 'PP') ? 'UTR/Bank' : 'Ord' }}:</strong> {{ $orderId }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                                    @if($txn->type === 'credit')
                                        <a href="{{ route('admin.customer-wallet-recharges.receipt', $txn->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.75rem;" title="Download Receipt">
                                            <i class="bi bi-download me-1"></i> Receipt
                                        </a>
                                    @endif
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
