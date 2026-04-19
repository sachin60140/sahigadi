@extends('layouts.admin')

@section('title', 'Service History - ' . $serviceHistory->vehicle_number . ' - SAHIGADI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.service-histories.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h2><i class="bi bi-wrench me-2"></i>{{ $serviceHistory->vehicle_number }}</h2>
    </div>
    <div class="d-flex gap-2">
        @if($serviceHistory->is_success)
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>{{ $serviceHistory->service_count ?? 0 }} Services</span>
        @else
            <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
        @endif
        <a href="{{ route('admin.service-histories.downloadPdf', $serviceHistory) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
        </a>
    </div>
</div>

@if(!$serviceHistory->is_success)
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Search Failed:</strong> {{ $serviceHistory->error_message }}
</div>
@endif

@if($serviceHistory->is_success && $dealerSearch)
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-wrench me-2"></i>Service Records ({{ $dealerSearch->records->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Dealer</th>
                                <th>Work Type</th>
                                <th>Bill Amount</th>
                                <th>Mileage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dealerSearch->records as $record)
                            <tr>
                                <td>{{ $record->svc_date ?? 'N/A' }}</td>
                                <td>
                                    <small>{{ $record->dealer_name ?? 'N/A' }}</small><br>
                                    <small class="text-muted">{{ $record->location_name ?? '' }}</small>
                                </td>
                                <td>{{ $record->work_type ?? 'N/A' }}</td>
                                <td>₹{{ $record->net_bill_amt ?? '0' }}</td>
                                <td>{{ $record->mileage ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Search Summary</h5>
            </div>
            <div class="card-body">
                <div class="detail-item mb-3">
                    <small class="text-muted">Dealer</small>
                    <h6 class="mb-0">{{ $serviceHistory->dealer->name ?? 'N/A' }}</h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Search Date</small>
                    <h6 class="mb-0">{{ $serviceHistory->created_at->format('d M Y, h:i A') }}</h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Amount Charged</small>
                    <h5 class="mb-0 text-success">₹{{ number_format($serviceHistory->charge_amount, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.detail-item {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}
.detail-item small { display: block; margin-bottom: 4px; }
</style>
@endpush
