@extends('layouts.dealer')

@section('title', 'Wallet')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Wallet</h2>
    <a href="{{ route('dealer.payments.checkout', ['type' => 'wallet_recharge', 'amount' => 1000]) }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add Money
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Available Balance</h5>
                <h2>₹{{ number_format($balance, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Transaction History</h5>
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
                        <td><small>{{ $transaction->reference_id ?? '-' }}</small></td>
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
@endsection
