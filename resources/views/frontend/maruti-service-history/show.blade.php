@extends('layouts.app')

@section('title', 'Maruti Service History - ' . $marutiServiceHistory->vehicle_number . ' - SAHIGADI')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('maruti-service-history.index') }}">Maruti Service History</a></li>
                            <li class="breadcrumb-item active">{{ $marutiServiceHistory->vehicle_number }}</li>
                        </ol>
                    </nav>
                    <h2><i class="bi bi-car-front-fill me-2"></i>{{ $marutiServiceHistory->vehicle_number }}</h2>
                </div>
                <div class="d-flex gap-2">
                    @if($marutiServiceHistory->is_success)
                        <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
                        <a href="{{ route('maruti-service-history.pdf', $marutiServiceHistory) }}" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
                        </a>
                    @else
                        <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
                    @endif
                </div>
            </div>

            @if(!$marutiServiceHistory->is_success)
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Search Failed:</strong> {{ $marutiServiceHistory->error_message }}
                </div>
            @endif

            @if($marutiServiceHistory->is_success)
                @if($marutiServiceHistory->records->count() > 0)
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Service Records ({{ $marutiServiceHistory->records->count() }})</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Service Type</th>
                                            <th>Dealer</th>
                                            <th>Job Card</th>
                                            <th>RO No</th>
                                            <th class="text-end">Total Amt</th>
                                            <th>Mileage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($marutiServiceHistory->records as $record)
                                        <tr>
                                            <td>{{ $record->svc_date ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info">{{ $record->service_cate ?? 'N/A' }}</span></td>
                                            <td>{{ $record->dealer_name ?? 'N/A' }}</td>
                                            <td>{{ $record->register_no ?? 'N/A' }}</td>
                                            <td>{{ $record->repair_order_no ?? 'N/A' }}</td>
                                            <td class="text-end text-success">₹{{ number_format($record->total_amount ?? 0, 2) }}</td>
                                            <td>{{ $record->mileage ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-lg mt-4">
                        <div class="card-header" style="background: var(--accent); color: white;">
                            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Bill Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Service Type</th>
                                            <th class="text-end">Part Amt</th>
                                            <th class="text-end">Labour Amt</th>
                                            <th class="text-end">Total</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $grouped = $marutiServiceHistory->records->groupBy('service_cate');
                                        @endphp
                                        @foreach($grouped as $type => $records)
                                        <tr>
                                            <td>{{ $type ?? 'Unknown' }}</td>
                                            <td class="text-end">₹{{ number_format($records->sum('part_amount'), 2) }}</td>
                                            <td class="text-end">₹{{ number_format($records->sum('labour_amount'), 2) }}</td>
                                            <td class="text-end">₹{{ number_format($records->sum('total_amount'), 2) }}</td>
                                            <td>{{ $records->count() }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="fw-bold bg-light">
                                            <td>Total</td>
                                            <td class="text-end">₹{{ number_format($marutiServiceHistory->records->sum('part_amount'), 2) }}</td>
                                            <td class="text-end">₹{{ number_format($marutiServiceHistory->records->sum('labour_amount'), 2) }}</td>
                                            <td class="text-end">₹{{ number_format($marutiServiceHistory->records->sum('total_amount'), 2) }}</td>
                                            <td>{{ $marutiServiceHistory->records->count() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>No service records found for this vehicle.
                    </div>
                @endif
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('maruti-service-history.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Search Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
@endsection