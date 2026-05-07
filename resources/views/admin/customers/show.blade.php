@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person me-2"></i>{{ $customer->name }}</h4>
        <small class="text-muted">{{ $customer->email }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Customer
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Name</td>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">WhatsApp</td>
                            <td>{{ $customer->whatsapp_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Company</td>
                            <td>{{ $customer->company_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">City</td>
                            <td>{{ $customer->city ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">State</td>
                            <td>{{ $customer->state ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pincode</td>
                            <td>{{ $customer->pincode ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Full Address</td>
                            <td>{{ $customer->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">GST Number</td>
                            <td>{{ $customer->gst_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wallet Balance</td>
                            <td class="text-success fw-bold">₹{{ number_format($customer->wallet->balance ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Joined On</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Car Listings ({{ $customer->listings->count() }})</h5>
            </div>
            <div class="card-body">
                @if($customer->listings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Reg. Year</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->listings as $listing)
                            <tr>
                                <td>
                                    <strong>{{ $listing->brand->name ?? 'Unknown' }} {{ $listing->model_name }}</strong>
                                    <br><small class="text-muted">{{ $listing->variant }} ({{ $listing->fuel_type }})</small>
                                </td>
                                <td>{{ $listing->registration_year }}</td>
                                <td>₹{{ number_format($listing->selling_price ?? 0) }}</td>
                                <td>
                                    @if($listing->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($listing->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No cars listed.</p>
                @endif
            </div>
        </div>

        @if($customer->wallet && $customer->wallet->transactions->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Wallet Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->wallet->transactions as $txn)
                            <tr>
                                <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($txn->type === 'credit')
                                        <span class="badge bg-success">Credit</span>
                                    @else
                                        <span class="badge bg-danger">Debit</span>
                                    @endif
                                </td>
                                <td class="{{ $txn->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                                </td>
                                <td>{{ $txn->remark ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
