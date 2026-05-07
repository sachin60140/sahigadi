@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('frontend.customer.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <h2 class="fw-bold mb-0">My Wallet</h2>
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rechargeModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Money
                </button>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-success text-white border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="card-title opacity-75 mb-1">Available Balance</h5>
                            <h2 class="fw-bold mb-0">₹{{ number_format($balance, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                    <h5 class="mb-0 fw-bold">Transaction History</h5>
                </div>
    <div class="card-body">
        @if($transactions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Remark</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            @if($transaction->type === 'credit')
                                <span class="badge bg-success">Credit</span>
                            @else
                                <span class="badge bg-danger">Debit</span>
                            @endif
                        </td>
                        <td class="{{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                        </td>
                        <td>{{ $transaction->remark ?? '-' }}</td>
                        <td>
                            <small>{{ $transaction->reference_id ?? '-' }}</small>
                            @if((str_contains(strtolower($transaction->remark), 'recharge via razorpay') || str_contains(strtolower($transaction->remark), 'recharge via phonepe') || str_contains(strtolower($transaction->remark), 'recharge via')) && $transaction->type === 'credit')
                                <br>
                                <a href="{{ route('customer.wallet.receipt', $transaction->id) }}" class="btn btn-sm btn-outline-primary mt-1" style="font-size: 0.75rem; padding: 2px 6px;">
                                    <i class="bi bi-download"></i> Receipt
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $transactions->links() }}
        @else
        <p class="text-muted text-center mb-0">No transactions yet.</p>
        @endif
    </div>
</div>
</div>

<!-- Recharge Modal -->
<div class="modal fade" id="rechargeModal" tabindex="-1" aria-labelledby="rechargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.payments.checkout') }}" method="GET" id="rechargeForm">
                <input type="hidden" name="type" value="wallet_recharge">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeModalLabel">Recharge Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recharge_amount" class="form-label">Recharge Amount (₹)</label>
                        <input type="number" class="form-control" id="recharge_amount" name="recharge_amount" min="{{ $minRechargeAmount }}" value="{{ $minRechargeAmount }}" required>
                        <div class="form-text">Minimum recharge amount is ₹{{ $minRechargeAmount }}.</div>
                    </div>
                    <div class="card bg-light border-0">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">Base Amount:</span>
                                <span id="displayBase" class="fw-medium">₹{{ number_format($minRechargeAmount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">GST (18%):</span>
                                <span id="displayGst" class="fw-medium text-danger">+ ₹{{ number_format($minRechargeAmount * 0.18, 2) }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Payable:</span>
                                <span id="displayTotal" class="fw-bold text-success fs-5">₹{{ number_format($minRechargeAmount * 1.18, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="proceedBtn">Proceed to Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.getElementById('recharge_amount');
        const displayBase = document.getElementById('displayBase');
        const displayGst = document.getElementById('displayGst');
        const displayTotal = document.getElementById('displayTotal');
        const proceedBtn = document.getElementById('proceedBtn');

        function updateCalculation() {
            let amount = parseFloat(amountInput.value) || 0;
            let minRechargeAmount = {{ $minRechargeAmount }};
            if (amount < minRechargeAmount) {
                proceedBtn.disabled = true;
            } else {
                proceedBtn.disabled = false;
            }

            let gst = amount * 0.18;
            let total = amount + gst;

            displayBase.textContent = '₹' + amount.toFixed(2);
            displayGst.textContent = '+ ₹' + gst.toFixed(2);
            displayTotal.textContent = '₹' + total.toFixed(2);
        }

        amountInput.addEventListener('input', updateCalculation);
        // Initial calc
        updateCalculation();

        @if(session('open_recharge_modal'))
            var rechargeModal = new bootstrap.Modal(document.getElementById('rechargeModal'));
            rechargeModal.show();
        @endif
    });
</script>
@endpush
