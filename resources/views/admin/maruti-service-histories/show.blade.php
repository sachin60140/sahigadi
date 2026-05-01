@extends('layouts.admin')

@section('title', 'Maruti Service History Details - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>Maruti Service History Details</h2>
    <div>
        <a href="{{ route('admin.maruti-service-histories.downloadPdf', $marutiServiceHistory) }}" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
        <a href="{{ route('admin.maruti-service-histories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Vehicle Number: {{ $marutiServiceHistory->vehicle_number }}</h5>
                <p>Dealer: {{ $marutiServiceHistory->dealer->name ?? 'N/A' }}</p>
                <p>Date: {{ $marutiServiceHistory->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p>Services: {{ $marutiServiceHistory->service_count }}</p>
                <p>Charge: Rs.{{ number_format($marutiServiceHistory->charge_amount ?? 0) }}</p>
                @if($marutiServiceHistory->is_success)
                    <span class="badge bg-success">Success</span>
                @else
                    <span class="badge bg-danger">Failed</span>
                @endif
            </div>
        </div>
    </div>
</div>

@if($marutiServiceHistory->is_success && $marutiServiceHistory->raw_response)
@php
$records = $marutiServiceHistory->raw_response['result']['serviceHistoryDetails'] ?? [];
@endphp
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Service Type</th>
                        <th>Dealer</th>
                        <th>Job Card No</th>
                        <th>RO No</th>
                        <th>Part Amt</th>
                        <th>Labour Amt</th>
                        <th>Total Amt</th>
                        <th>Mileage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td>{{ $record['dateOfSVC'] ?? 'N/A' }}</td>
                        <td>{{ $record['serviceType'] ?? 'N/A' }}</td>
                        <td>{{ $record['dealerName'] ?? 'N/A' }}</td>
                        <td>{{ $record['noOfJobCard'] ?? 'N/A' }}</td>
                        <td>{{ $record['noOfRO'] ?? 'N/A' }}</td>
                        <td>Rs.{{ number_format($record['partAmmount'] ?? 0) }}</td>
                        <td>Rs.{{ number_format($record['labourAmmount'] ?? 0) }}</td>
                        <td>Rs.{{ number_format($record['totalAmount'] ?? 0) }}</td>
                        <td>{{ $record['mileage'] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-danger">
    {{ $marutiServiceHistory->error_message ?? 'No data available' }}
</div>
@endif
@endsection