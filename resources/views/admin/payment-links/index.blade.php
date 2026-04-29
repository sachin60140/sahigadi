@extends('layouts.admin')

@section('title', 'Payment Links')

@section('content')
<div class="top-bar flex-wrap gap-3">
    <div>
        <h4><i class="bi bi-link-45deg text-primary me-2"></i>Payment Links</h4>
        <p class="text-muted mb-0">Generate and manage custom payment links for dealers.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateLinkModal">
            <i class="bi bi-plus-lg me-1"></i> Generate New Link
        </button>
    </div>
</div>

<div class="card table-modern border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payee Info</th>
                        <th>Amount</th>
                        <th>Purpose & Gateway</th>
                        <th>Link</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentLinks as $link)
                        <tr>
                            <td>
                                <div class="fw-medium text-dark">{{ $link->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $link->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                @if($link->dealer)
                                    <div class="fw-bold text-primary">{{ $link->dealer->company_name ?? 'N/A' }}</div>
                                    <span class="fw-medium text-dark" style="font-size: 0.85rem;">{{ $link->dealer->name ?? 'Unknown' }}</span>
                                @else
                                    <div class="fw-bold text-primary">{{ $link->customer_name ?? 'Unknown Customer' }}</div>
                                    <span class="text-muted d-block" style="font-size: 0.8rem;">{{ $link->customer_mobile ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark">₹{{ number_format($link->amount, 2) }}</span>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $link->purpose }}</div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 mt-1">
                                    {{ ucfirst($link->gateway) }}
                                </span>
                            </td>
                            <td>
                                <div class="input-group input-group-sm" style="max-width: 250px;">
                                    <input type="text" class="form-control bg-light" value="{{ route('pay.link', $link->id) }}" readonly id="link-{{ $link->id }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('link-{{ $link->id }}')" title="Copy Link">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                @if($link->status === 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-check-circle-fill me-1"></i>Paid</span>
                                @elseif($link->status === 'expired')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-x-circle-fill me-1"></i>Expired</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($link->status === 'pending')
                                        <form action="{{ route('admin.payment-links.refresh', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Check gateway for payment status updates?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Refresh Status">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.payment-links.destroy', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment link?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Link">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-link-45deg fs-1 d-block mb-3"></i>
                                    <h5>No payment links found</h5>
                                    <p>Generate a new link to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($paymentLinks->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $paymentLinks->links() }}
        </div>
    @endif
</div>

<!-- Generate Link Modal -->
<div class="modal fade" id="generateLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.payment-links.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Generate Payment Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Select Payee Type</label>
                        <select class="form-select mb-2" id="payeeTypeToggle" onchange="togglePayeeFields()">
                            <option value="customer">Direct Customer</option>
                            <option value="dealer">Registered Dealer</option>
                        </select>
                    </div>

                    <div id="dealerField" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Select Dealer</label>
                            <select class="form-select" name="dealer_id" id="dealerSelect">
                                <option value="">Choose a dealer...</option>
                                @foreach($dealers as $dealer)
                                    <option value="{{ $dealer->id }}">{{ $dealer->company_name ?? $dealer->name }} ({{ $dealer->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="customerFields">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Customer Name</label>
                            <input type="text" class="form-control" name="customer_name" id="customerName" placeholder="e.g. John Doe">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Email (Optional)</label>
                                <input type="email" class="form-control" name="customer_email" placeholder="john@example.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Mobile</label>
                                <input type="text" class="form-control" name="customer_mobile" id="customerMobile" placeholder="9876543210">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Amount (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required placeholder="e.g. 5000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Purpose</label>
                        <input type="text" class="form-control" name="purpose" required placeholder="e.g. Wallet Recharge, Featured Listing">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Payment Gateway</label>
                        <select class="form-select" name="gateway" required>
                            <option value="any">Any Available Gateway</option>
                            <option value="razorpay">Razorpay</option>
                            <option value="phonepe">PhonePe</option>
                        </select>
                        <div class="form-text">Choose the gateway for this payment link or let the dealer choose if 'Any' is selected.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Link</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
function togglePayeeFields() {
    var type = document.getElementById('payeeTypeToggle').value;
    var dealerField = document.getElementById('dealerField');
    var customerFields = document.getElementById('customerFields');
    
    if(type === 'dealer') {
        dealerField.classList.remove('d-none');
        customerFields.classList.add('d-none');
        document.getElementById('dealerSelect').required = true;
        document.getElementById('customerName').required = false;
        document.getElementById('customerMobile').required = false;
    } else {
        dealerField.classList.add('d-none');
        customerFields.classList.remove('d-none');
        document.getElementById('dealerSelect').required = false;
        document.getElementById('dealerSelect').value = '';
        document.getElementById('customerName').required = true;
        document.getElementById('customerMobile').required = true;
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    togglePayeeFields();
});

function copyToClipboard(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    navigator.clipboard.writeText(copyText.value);
    alert("Copied the link: " + copyText.value);
}
</script>
@endpush
@endsection
