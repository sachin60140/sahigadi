@extends('layouts.admin')

@section('title', 'Customer RC Search Details - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>RC Search Details</h2>
    <div>
        <a href="{{ route('admin.customer-vehicle-searches.downloadPdf', $vehicleSearch) }}" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
        <a href="{{ route('admin.customer-vehicle-searches.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold">Registration Number: {{ $vehicleSearch->registration_number }}</h5>
                <p class="mb-1">Customer Name: {{ $vehicleSearch->customer_name ?? 'N/A' }}</p>
                <p class="mb-1">Phone: {{ $vehicleSearch->customer_phone ?? 'N/A' }}</p>
                <p class="mb-1">Date: {{ $vehicleSearch->created_at->format('d M Y H:i') }}</p>
                <p class="mb-0">Amount Paid: Rs.{{ number_format($vehicleSearch->paid_amount ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold">Status Information</h5>
                <div class="mb-2">
                    @if($vehicleSearch->is_success)
                        <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i> Success</span>
                    @else
                        <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i> Failed</span>
                    @endif
                </div>
                @if(!$vehicleSearch->is_success)
                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>Error Message:</strong><br>
                        {{ $vehicleSearch->error_message ?? 'No specific error message recorded.' }}
                    </div>
                @endif
                @if($vehicleSearch->razorpay_payment_id)
                    <div class="mt-3">
                        <small class="text-muted d-block">Razorpay Payment ID:</small>
                        <code>{{ $vehicleSearch->razorpay_payment_id }}</code>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($vehicleSearch->is_success && is_array($vehicleSearch->vehicle_data))
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Vehicle Information</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                @foreach($vehicleSearch->vehicle_data as $key => $value)
                    <tr>
                        <td class="fw-bold bg-light" width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                        <td>{{ is_array($value) ? json_encode($value) : ($value ?? 'N/A') }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endif

@endsection
