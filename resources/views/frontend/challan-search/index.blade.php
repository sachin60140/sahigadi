@extends('layouts.app')

@section('title', 'E-Challan Check - SAHIGADI')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-receipt" style="font-size: 4rem; color: var(--accent);"></i>
                        <h2 class="fw-bold mt-3">E-Challan Check</h2>
                        <p class="text-muted">Check your vehicle for any pending traffic challans</p>
                    </div>

                    <form action="{{ route('challan-search.search') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                            <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. BR01AB1234" required>
                            <small class="text-muted">Enter your vehicle registration number</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email Address <span class="text-muted">(Optional)</span></label>
                            <input type="email" name="customer_email" class="form-control" placeholder="Enter email address">
                        </div>

                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Price: ₹{{ number_format($charge, 0) }}</strong> per report
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 py-3">
                            <i class="bi bi-search me-2"></i>Check Challan Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection