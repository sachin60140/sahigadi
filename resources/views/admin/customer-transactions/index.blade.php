@extends('layouts.admin')

@section('title', 'Customer Payments & Refunds - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wallet2 me-2"></i>Customer Payments & Refunds</h2>
</div>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
        <ul class="nav nav-tabs card-header-tabs fs-6">
            <li class="nav-item">
                <a class="nav-link {{ $type === 'vahan' ? 'active fw-bold' : '' }}" href="{{ route('admin.customer-transactions.index', ['type' => 'vahan']) }}">Vahan Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type === 'challan' ? 'active fw-bold' : '' }}" href="{{ route('admin.customer-transactions.index', ['type' => 'challan']) }}">E-Challan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type === 'maruti' ? 'active fw-bold' : '' }}" href="{{ route('admin.customer-transactions.index', ['type' => 'maruti']) }}">Maruti SVC</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type === 'mahindra' ? 'active fw-bold' : '' }}" href="{{ route('admin.customer-transactions.index', ['type' => 'mahindra']) }}">Mahindra SVC</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        @if($type === 'vahan')
                            <th>Registration No</th>
                        @else
                            <th>Vehicle Number</th>
                        @endif
                        <th>Paid Amount</th>
                        <th>Status</th>
                        <th>Refund Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>
                            <strong>{{ $transaction->customer_name }}</strong><br>
                            <small class="text-muted">{{ $transaction->customer_phone }}</small>
                        </td>
                        <td>{{ strtoupper($type === 'vahan' ? $transaction->registration_number : $transaction->vehicle_number) }}</td>
                        <td>Rs. {{ number_format($transaction->paid_amount ?? 0, 2) }}</td>
                        <td>
                            @if($transaction->is_success)
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Failed API</span>
                            @endif
                        </td>
                        <td>
                            @if(!$transaction->razorpay_payment_id)
                                <span class="badge bg-secondary">Unpaid / No ID</span>
                            @elseif($transaction->is_refunded)
                                <span class="badge bg-info">Refunded</span><br>
                                <small class="text-muted">{{ $transaction->razorpay_refund_id }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Not Refunded</span>
                            @endif
                        </td>
                        <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.customer-transactions.show', ['id' => $transaction->id, 'type' => $type]) }}" class="btn btn-sm btn-outline-primary mb-1">
                                <i class="bi bi-eye"></i> Details
                            </a>
                            
                            @if(!$transaction->is_success && !$transaction->is_refunded && $transaction->razorpay_payment_id)
                                <form action="{{ route('admin.customer-transactions.refund', ['id' => $transaction->id, 'type' => $type]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to issue a full refund to this customer?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger mb-1">
                                        <i class="bi bi-arrow-counterclockwise"></i> Refund
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No customer transactions found for this service.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
