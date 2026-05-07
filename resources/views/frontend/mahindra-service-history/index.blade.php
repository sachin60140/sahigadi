@extends('layouts.app')

@section('title', 'Mahindra Service History - SAHI GADI')
@section('meta_description', 'Search mahindra vehicle service history online. Get complete service records from authorized mahindra service centers.')

@section('content')

@if(!auth('customer')->check())
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2"><i class="bi bi-car-front-fill me-2"></i>Mahindra Service History</h1>
            <p class="mb-0">Get complete service records from authorized mahindra service centers</p>
        </div>
    </div>
</section>
@endif

<div class="container py-5">
    @if(auth('customer')->check())
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h2 class="fw-bold mb-1">Mahindra Service History</h2>
                <p class="text-muted mb-0">Get complete service records from authorized mahindra service centers</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row {{ auth('customer')->check() ? '' : 'justify-content-center' }}">
        @if(auth('customer')->check())
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('frontend.customer.partials.sidebar')
        </div>
        @endif

        <!-- Main Content -->
        <div class="{{ auth('customer')->check() ? 'col-lg-9' : 'col-lg-8' }}">
            <div class="card border-0 shadow-lg {{ auth('customer')->check() ? 'rounded-4' : '' }}">
                <div class="card-header bg-primary text-white {{ auth('customer')->check() ? 'rounded-top-4' : '' }}">
                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Search Vehicle Service History</h5>
                </div>
                <div class="card-body {{ auth('customer')->check() ? 'p-4' : '' }}">
                    <form action="{{ route('mahindra-service-history.search') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Vehicle Registration Number</label>
                            <input type="text" name="vehicle_number" class="form-control form-control-lg text-uppercase @error('vehicle_number') is-invalid @enderror" 
                                   placeholder="e.g. DL01AB1234" maxlength="20" required>
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter your vehicle's registration number</small>
                        </div>
                        
                        @if(!auth('customer')->check())
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Your Name</label>
                                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                                           placeholder="Enter your name" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Mobile Number</label>
                                    <input type="tel" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           placeholder="10-digit mobile number" required maxlength="20">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Email (Optional)</label>
                                    <input type="email" name="customer_email" class="form-control" 
                                           placeholder="your@email.com">
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <div class="bg-light px-3 py-2 rounded">
                                <span class="text-muted mb-0 me-2">Charge:</span>
                                <span class="fw-bold text-primary fs-5">₹{{ number_format($charge) }}</span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search me-2"></i>Search Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(auth('customer')->check() && isset($history))
            <div class="card border-0 shadow-lg mt-4 rounded-4">
                <div class="card-header bg-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Your Search History</h5>
                </div>
                <div class="card-body p-4">
                    @if($history->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Vehicle Number</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $record)
                                        <tr>
                                            <td>{{ $record->created_at->format('d M Y, h:i A') }}</td>
                                            <td class="text-uppercase fw-bold">{{ $record->vehicle_number }}</td>
                                            <td>
                                                @if($record->is_success)
                                                    <span class="badge bg-success">Success</span>
                                                @else
                                                    <span class="badge bg-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->is_success)
                                                    <a href="{{ route('mahindra-service-history.show', $record->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">No search history found.</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-lg mt-4 {{ auth('customer')->check() ? 'rounded-4' : '' }}">
                <div class="card-header bg-white {{ auth('customer')->check() ? 'rounded-top-4' : '' }}">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                                <i class="bi bi-car-front text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Enter Vehicle Number</h6>
                            <small class="text-muted">Input your mahindra car's registration number</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                                <i class="bi bi-receipt text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Get Service Records</h6>
                            <small class="text-muted">View all service history from authorized centers</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                                <i class="bi bi-file-earmark-pdf text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Download Report</h6>
                            <small class="text-muted">Get PDF report for reference</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-lg mt-4 {{ auth('customer')->check() ? 'rounded-4' : '' }}">
                <div class="card-header bg-white {{ auth('customer')->check() ? 'rounded-top-4' : '' }}">
                    <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>What You'll Get</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Complete service history from mahindra authorized service centers</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Service dates, types, and descriptions</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Bill amounts (Parts & Labour)</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Dealer information and location</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Vehicle mileage at each service</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
