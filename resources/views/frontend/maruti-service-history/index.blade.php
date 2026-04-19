@extends('layouts.app')

@section('title', 'Maruti Service History - SAHIGADI')
@section('meta_description', 'Search Maruti vehicle service history online. Get complete service records from authorized Maruti service centers.')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2"><i class="bi bi-car-front-fill me-2"></i>Maruti Service History</h1>
            <p class="mb-0">Get complete service records from authorized Maruti service centers</p>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Search Vehicle Service History</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maruti-service-history.search') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Vehicle Registration Number</label>
                            <input type="text" name="vehicle_number" class="form-control form-control-lg text-uppercase @error('vehicle_number') is-invalid @enderror" 
                                   placeholder="e.g. DL01AB1234" maxlength="20" required>
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter your vehicle's registration number</small>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Your Name</label>
                                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                                           placeholder="Enter your name" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mobile Number</label>
                                    <input type="tel" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           placeholder="10-digit mobile number" required maxlength="20">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email (Optional)</label>
                                    <input type="email" name="customer_email" class="form-control" 
                                           placeholder="your@email.com">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted">Charge:</span>
                                <span class="fw-bold text-primary fs-5">₹{{ number_format($charge) }}</span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search me-2"></i>Search Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-lg mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                                <i class="bi bi-car-front text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Enter Vehicle Number</h6>
                            <small class="text-muted">Input your Maruti car's registration number</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                                <i class="bi bi-receipt text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Get Service Records</h6>
                            <small class="text-muted">View all service history from authorized centers</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                                <i class="bi bi-file-earmark-pdf text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Download Report</h6>
                            <small class="text-muted">Get PDF report for reference</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-lg mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>What You'll Get</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i>Complete service history from Maruti authorized service centers</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i>Service dates, types, and descriptions</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i>Bill amounts (Parts & Labour)</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i>Dealer information and location</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i>Vehicle mileage at each service</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection