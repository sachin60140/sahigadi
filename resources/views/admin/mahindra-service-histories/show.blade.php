@extends('layouts.admin')

@section('title', 'Mahindra Customer Service History Details - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-truck-front me-2"></i>Mahindra Service History Details</h2>
    <div>
        <a href="{{ route('admin.mahindra-service-histories.downloadPdf', $mahindraServiceHistory) }}" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
        <a href="{{ route('admin.mahindra-service-histories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Vehicle Number: {{ $mahindraServiceHistory->vehicle_number }}</h5>
                <p>Customer: {{ $mahindraServiceHistory->customer_name ?? 'N/A' }}</p>
                <p>Phone: {{ $mahindraServiceHistory->customer_phone ?? 'N/A' }}</p>
                <p>Date: {{ $mahindraServiceHistory->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p>Services: {{ $mahindraServiceHistory->records->count() }}</p>
                <p>Charge Paid: Rs.{{ number_format($mahindraServiceHistory->paid_amount ?? 0) }}</p>
                @if($mahindraServiceHistory->is_success)
                    <span class="badge bg-success">Success</span>
                @else
                    <span class="badge bg-danger">Failed</span>
                @endif
            </div>
        </div>
    </div>
</div>

@if($mahindraServiceHistory->is_success && $mahindraServiceHistory->records->count() > 0)
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Work Type</th>
                        <th>Dealer Name</th>
                        <th>Location</th>
                        <th>Job Card No</th>
                        <th>RO No</th>
                        <th>Bill No</th>
                        <th>Bill Date</th>
                        <th>Assistant</th>
                        <th>Total Amt</th>
                        <th>Status</th>
                        <th>Mileage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahindraServiceHistory->records as $record)
                    <tr>
                        <td>{{ $record->svc_date ? $record->svc_date->format('d/m/Y') : 'N/A' }}</td>
                        <td><span class="badge bg-secondary">{{ $record->service_cate ?? 'N/A' }}</span></td>
                        <td>{{ $record->work_type ?? 'N/A' }}</td>
                        <td>{{ $record->dealer_name ?? 'N/A' }} <br><small class="text-muted">{{ $record->dealer_code }}</small></td>
                        <td>{{ $record->dealer_address ?? 'N/A' }}</td>
                        <td>{{ $record->register_no ?? 'N/A' }}</td>
                        <td>{{ $record->repair_order_no ?? 'N/A' }}</td>
                        <td>{{ $record->repair_order_bill_no ?? 'N/A' }}</td>
                        <td>{{ $record->repair_order_bill_date ? $record->repair_order_bill_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $record->service_assistant_name ?? 'N/A' }}</td>
                        <td class="text-end fw-bold text-success">Rs.{{ number_format($record->total_amount ?? 0) }}</td>
                        <td>{{ $record->status ?? 'N/A' }}</td>
                        <td>{{ $record->mileage ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-danger">
    {{ $mahindraServiceHistory->error_message ?? 'No service records found.' }}
</div>
@endif
@endsection
