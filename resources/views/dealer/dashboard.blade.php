@extends('layouts.dealer')

@push('scripts')
<script>
function copyCatalogUrl() {
    var copyText = document.getElementById("catalogUrl");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        var btn = document.querySelector('#copyBtn');
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 2000);
    });
}
</script>
<style>
.hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
.icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
.spacing-3 { letter-spacing: 2px; }
</style>
@endpush

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Welcome back, {{ strtok(auth('dealer')->user()->name, ' ') }} 👋</h2>
        <p class="text-muted mb-0">Here's what's happening with your dealership inventory today.</p>
    </div>
    <div>
        <a href="{{ route('dealer.cars.create') }}" class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm"><i class="bi bi-plus-lg me-2"></i>Add New Car</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stat 1 -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-semibold mb-0 text-uppercase tracking-wide" style="font-size: 0.8rem;">Total Cars</h6>
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-car-front fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-bolder mb-0 text-dark display-6">{{ $stats['total_cars'] }}</h2>
            </div>
        </div>
    </div>
    <!-- Stat 2 -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-semibold mb-0 text-uppercase tracking-wide" style="font-size: 0.8rem;">Approved</h6>
                    <div class="icon-shape bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-bolder mb-0 text-dark display-6">{{ $stats['approved_cars'] }}</h2>
            </div>
        </div>
    </div>
    <!-- Stat 3 -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-semibold mb-0 text-uppercase tracking-wide" style="font-size: 0.8rem;">Pending</h6>
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-bolder mb-0 text-dark display-6">{{ $stats['pending_cars'] }}</h2>
            </div>
        </div>
    </div>
    <!-- Stat 4 -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-semibold mb-0 text-uppercase tracking-wide" style="font-size: 0.8rem;">Enquiries</h6>
                    <div class="icon-shape bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-chat-dots fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-bolder mb-0 text-dark display-6">{{ $stats['new_enquiries'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Wallet -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
            <div class="position-absolute end-0 bottom-0 opacity-10 p-3">
                <i class="bi bi-wallet2" style="font-size: 8rem; margin-right: -2rem; margin-bottom: -3rem;"></i>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-white-50 fw-semibold mb-0 text-uppercase">Wallet Balance</h6>
                    </div>
                    <h1 class="fw-bold mb-0 display-5">₹{{ number_format($stats['wallet_balance'], 2) }}</h1>
                </div>
                <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                    <a href="{{ route('dealer.wallet.index') }}" class="text-white text-decoration-none fw-semibold d-flex align-items-center justify-content-between">
                        <span>Manage Wallet</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Info / Limits -->
    <div class="col-md-8">
        <div class="row g-4 h-100">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                        <div class="mb-3">
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 64px; height: 64px;">
                                <i class="bi bi-tags fs-2"></i>
                            </div>
                            <h6 class="text-muted mb-1 fw-semibold text-uppercase" style="font-size: 0.8rem;">Remaining Listings</h6>
                            <h2 class="fw-bold text-dark mb-0">{{ $stats['remaining_listings'] }}</h2>
                        </div>
                        <div class="mt-2"><a href="{{ route('dealer.plans.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold">Upgrade Plan</a></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                        <div class="mb-3">
                            @php $status = auth('dealer')->user()->status; @endphp
                            <div class="icon-shape mx-auto mb-3 {{ $status === 'approved' ? 'bg-success bg-opacity-10 text-success' : ($status === 'pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-danger bg-opacity-10 text-danger') }}" style="width: 64px; height: 64px;">
                                <i class="bi {{ $status === 'approved' ? 'bi-shield-check' : ($status === 'pending' ? 'bi-shield-exclamation' : 'bi-shield-x') }} fs-2"></i>
                            </div>
                            <h6 class="text-muted mb-1 fw-semibold text-uppercase" style="font-size: 0.8rem;">Showroom Status</h6>
                            <h3 class="fw-bold text-dark mb-0 text-capitalize">{{ $status }}</h3>
                        </div>
                        <div class="mt-2">
                            @if($status !== 'approved')
                                <span class="text-muted small">Update profile for faster approval.</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>Fully Verified Account</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Share Catalog -->
    <div class="col-md-7">
        <div class="card shadow-sm rounded-4 border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-share-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Share Catalog Link</h5>
                        <p class="text-muted small mb-0">Blast this link on WhatsApp & social media for direct leads.</p>
                    </div>
                </div>
                
                <div class="mt-4 bg-light p-2 rounded-pill border">
                    <div class="input-group">
                        <input type="text" class="form-control bg-transparent border-0 ps-4 shadow-none fw-medium text-dark" id="catalogUrl" value="{{ url('/catalog/' . auth('dealer')->user()->slug) }}" readonly>
                        <button class="btn btn-primary rounded-pill px-4 fw-semibold" type="button" id="copyBtn" onclick="copyCatalogUrl()">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-md-5">
        <div class="card shadow-sm rounded-4 border-0 h-100 bg-dark text-white text-center position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 opacity-10 p-3">
                <i class="bi bi-graph-up-arrow" style="font-size: 8rem; margin-top: -1rem; margin-left: -2rem;"></i>
            </div>
            <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-center">
                <h6 class="fw-semibold text-white-50 text-uppercase mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Live Inventory Pulse</h6>
                <div class="d-flex justify-content-center align-items-center gap-4 mt-2">
                    <div class="px-3">
                        <h1 class="display-4 fw-bold mb-0">{{ auth('dealer')->user()->cars()->where('is_active', true)->where('status', 'approved')->count() }}</h1>
                        <span class="text-white-50 text-uppercase small fw-semibold spacing-3">Listed</span>
                    </div>
                    <div class="border-start border-secondary opacity-50" style="height: 60px;"></div>
                    <div class="px-3">
                        <h1 class="display-4 fw-bold mb-0 text-warning">{{ auth('dealer')->user()->cars()->where('is_active', true)->where('status', 'pending')->count() }}</h1>
                        <span class="text-white-50 text-uppercase small fw-semibold spacing-3">Review</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-inbox-fill text-primary me-2"></i>Recent Enquiries</h5>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $recentEnquiries->count() }} Leads</span>
    </div>
    <div class="card-body p-0">
        @if($recentEnquiries->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-white">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Customer Info</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Interested Model</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Phone / Contact</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Status</th>
                        <th class="py-3 pe-4 text-end text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Received On</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($recentEnquiries as $enquiry)
                    <tr style="transition: all 0.2s;">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($enquiry->customer_name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold text-dark">{{ $enquiry->customer_name }}</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <a href="{{ route('dealer.cars.edit', $enquiry->car_id) }}" class="text-decoration-none fw-semibold text-primary d-flex align-items-center">
                                <i class="bi bi-car-front me-2 text-muted"></i>
                                {{ Str::limit($enquiry->car->title ?? 'Deleted Car', 30) }}
                            </a>
                        </td>
                        <td class="py-3 text-muted fw-medium"><i class="bi bi-telephone-fill small me-2 opacity-50"></i>{{ $enquiry->customer_phone }}</td>
                        <td class="py-3">
                            @if($enquiry->status === 'new')
                                <span class="badge bg-danger bg-opacity-10 border border-danger border-opacity-25 text-danger rounded-pill px-3 py-2"><i class="bi bi-circle-fill" style="font-size: 0.4rem; vertical-align: middle; margin-right: 4px;"></i> New</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 border border-secondary border-opacity-25 text-secondary rounded-pill px-3 py-2"><i class="bi bi-check" style="font-size: 1rem; line-height: 0;"></i> Contacted</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4 text-end text-muted small fw-medium">{{ $enquiry->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 text-center border-top bg-light rounded-bottom-4">
            <a href="#" class="text-decoration-none fw-semibold small text-muted">View all enquiries <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-inbox fs-1 text-muted"></i>
            </div>
            <h5 class="fw-bold text-dark">No new enquiries yet</h5>
            <p>Make sure you list your cars and share your catalog to attract buyers.</p>
            <a href="{{ route('dealer.cars.create') }}" class="btn btn-outline-primary rounded-pill mt-2">List a New Car</a>
        </div>
        @endif
    </div>
</div>
@endsection
