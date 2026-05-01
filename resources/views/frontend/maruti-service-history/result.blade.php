@extends('layouts.app')

@section('title', 'Maruti Service History Result - SAHI GADI')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if(isset($success) && !$success)
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Search Failed</h5>
                    <p class="mb-0">{{ $message }}</p>
                </div>
            @endif

            @if($marutiServiceHistory->is_success)
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Service History Found</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($cached) && $cached)
                            <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div><i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)</div>
                                <form action="{{ route('maruti-service-history.search') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="vehicle_number" value="{{ $marutiServiceHistory->vehicle_number }}">
                                    <input type="hidden" name="customer_name" value="{{ $marutiServiceHistory->customer_name ?? '' }}">
                                    <input type="hidden" name="customer_phone" value="{{ $marutiServiceHistory->customer_phone ?? '' }}">
                                    <input type="hidden" name="customer_email" value="{{ $marutiServiceHistory->customer_email ?? '' }}">
                                    <input type="hidden" name="force_fresh" value="1">
                                    <button type="submit" class="btn btn-sm btn-warning border border-dark fw-bold">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Fresh Search
                                    </button>
                                </form>
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5 class="fw-bold">Vehicle: {{ $marutiServiceHistory->vehicle_number }}</h5>
                            @if($marutiServiceHistory->customer_name)
                                <p class="text-muted mb-0">Requested by: {{ $marutiServiceHistory->customer_name }}</p>
                            @endif
                        </div>

                        @if($marutiServiceHistory->records->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Service Type</th>
                                            <th>Dealer</th>
                                            <th>Job Card</th>
                                            <th>RO No</th>
                                            <th>Total Amount</th>
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
                                            <td class="text-success">₹{{ number_format($record->total_amount ?? 0, 2) }}</td>
                                            <td>{{ $record->mileage ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>No service records found for this vehicle.
                            </div>
                        @endif

                        <div class="mt-4 text-center">
                            <a href="{{ route('maruti-service-history.pdf', $marutiServiceHistory->id) }}" class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $marutiServiceHistory->error_message ?? 'No service history records found for this vehicle.' }}</p>
                        <p class="text-muted mt-3">This could mean:</p>
                        <ul>
                            <li>Vehicle is not registered with Maruti authorized service centers</li>
                            <li>Vehicle has no service records</li>
                            <li>Vehicle brand/model is not supported</li>
                        </ul>
                    </div>
                </div>
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