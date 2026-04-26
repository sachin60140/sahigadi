@extends('layouts.admin')

@section('title', 'Dealer Details')

@section('content')
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person me-2"></i>{{ $dealer->name }}</h4>
        <small class="text-muted">{{ $dealer->email }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.dealers.edit', $dealer) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Dealer
        </a>
        <a href="{{ route('admin.dealers.index') }}" class="btn btn-outline-secondary">
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
                            <td>{{ $dealer->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $dealer->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td>{{ $dealer->phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Company</td>
                            <td>{{ $dealer->company_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">City</td>
                            <td>{{ $dealer->city ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">State</td>
                            <td>{{ $dealer->state ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pincode</td>
                            <td>{{ $dealer->pincode ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Full Address</td>
                            <td>{{ $dealer->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($dealer->status === 'approved')
                                    <span class="badge bg-success badge-modern">Approved</span>
                                @elseif($dealer->status === 'pending')
                                    <span class="badge bg-warning badge-modern">Pending</span>
                                @else
                                    <span class="badge bg-danger badge-modern">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wallet Balance</td>
                            <td class="text-success fw-bold">₹{{ number_format($dealer->wallet->balance ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">KYC Documents</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Document Type</td>
                            <td><strong>Aadhaar Card</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Aadhaar Number</td>
                            <td><strong>{{ $dealer->kyc_document_number ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Aadhaar File</td>
                            <td>
                                @if($dealer->kyc_document_path)
                                    <a href="{{ asset('storage/'.$dealer->kyc_document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-text me-1"></i>View Document
                                    </a>
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr class="my-2"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">PAN Number</td>
                            <td><strong>{{ $dealer->pan_number ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">PAN Document File</td>
                            <td>
                                @if($dealer->pan_document_path)
                                    <a href="{{ asset('storage/'.$dealer->pan_document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-text me-1"></i>View Document
                                    </a>
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        @if($dealer->gst_number)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">GST Documents</h5>
                @if($dealer->gst_document_path && !$dealer->gst_verified)
                    <form action="{{ route('admin.dealers.verify-gst', $dealer) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i>Verify GST
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">GST Number</td>
                            <td>
                                <strong>{{ $dealer->gst_number }}</strong>
                                @if($dealer->gst_verified)
                                    <span class="badge bg-success ms-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Verification Date</td>
                            <td>{{ $dealer->gst_verified_at ? $dealer->gst_verified_at->format('d M Y') : 'Not verified' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Document File</td>
                            <td>
                                @if($dealer->gst_document_path)
                                    <a href="{{ asset('storage/'.$dealer->gst_document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-text me-1"></i>View GST
                                    </a>
                                    @if($dealer->gst_verified)
                                        <form action="{{ route('admin.dealers.unverify-gst', $dealer) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove GST verification?')">
                                                <i class="bi bi-x-circle me-1"></i>Unverify
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($dealer->status === 'pending')
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('admin.dealers.approve', $dealer) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Approve Dealer</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Reject Dealer
                        </button>
                    </div>
                </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Add Money to Wallet</h6>
                        <form action="{{ route('admin.dealers.add-money', $dealer) }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-8">
                                <input type="number" name="amount" class="form-control" placeholder="Amount" min="1" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="remark" class="form-control" placeholder="Remark (optional)">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100 mt-2"><i class="bi bi-plus-circle"></i> Add Money</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h6>Debit Money from Wallet</h6>
                        <form action="{{ route('admin.dealers.debit-money', $dealer) }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-8">
                                <input type="number" name="amount" class="form-control" placeholder="Amount" min="1" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="remark" class="form-control" placeholder="Remark (optional)">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger w-100 mt-2"><i class="bi bi-dash-circle"></i> Debit Money</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cars ({{ $dealer->cars->count() }})</h5>
            </div>
            <div class="card-body">
                @if($dealer->cars->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Car</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dealer->cars as $car)
                            <tr>
                                <td>{{ Str::limit($car->title, 30) }}</td>
                                <td>₹{{ number_format($car->price ?? 0) }}</td>
                                <td>
                                    @if($car->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($car->status === 'pending')
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

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Subscriptions</h5>
            </div>
            <div class="card-body">
                @if($dealer->subscriptions->count() > 0)
                    @foreach($dealer->subscriptions as $sub)
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>{{ $sub->plan->name }}</strong> - {{ $sub->getActiveListingsCount() }}/{{ $sub->plan->listing_limit }} listings ({{ $sub->expires_at->format('d M Y') }})</span>
                        <span class="badge bg-{{ $sub->isActive() ? 'success' : 'secondary' }} badge-modern">
                            {{ $sub->isActive() ? 'Active' : 'Expired' }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No active subscription.</p>
                @endif
                <hr>
                <h6 class="mb-3">Assign New Plan</h6>
                <form action="{{ route('admin.dealers.assign-plan', $dealer) }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-8">
                        <select name="plan_id" class="form-select @error('plan_id') is-invalid @enderror" required>
                            <option value="">Select Plan</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }} - ₹{{ number_format($plan->price) }} ({{ $plan->listing_limit }} listings)
                            </option>
                            @endforeach
                        </select>
                        @error('plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Assign Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($walletTransactions->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Wallet Transactions</h5>
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
                            @foreach($walletTransactions as $txn)
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

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.dealers.reject', $dealer) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for rejection *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Dealer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
