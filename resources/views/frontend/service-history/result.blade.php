@extends('layouts.app')

@section('title', 'Service History Result - SAHIGADI')

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

            @if($serviceHistory->is_success)
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Service History Found</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($cached) && $cached)
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5 class="fw-bold">Vehicle: {{ $serviceHistory->vehicle_number }}</h5>
                            @if($serviceHistory->customer_name)
                                <p class="text-muted mb-0">Requested by: {{ $serviceHistory->customer_name }}</p>
                            @endif
                        </div>

                        @if($serviceHistory->records->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Dealer</th>
                                            <th>Work Type</th>
                                            <th>Mileage</th>
                                            <th>Bill Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory->records as $record)
                                        <tr>
                                            <td>{{ $record->svc_date ? \Carbon\Carbon::parse($record->svc_date)->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $record->dealer_name ?? 'N/A' }}</td>
                                            <td>{{ $record->work_type ?? 'N/A' }}</td>
                                            <td>{{ $record->mileage ? number_format($record->mileage).' km' : 'N/A' }}</td>
                                            <td>₹{{ $record->net_bill_amt ? number_format($record->net_bill_amt) : 'N/A' }}</td>
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
                            <a href="{{ route('service-history.pdf', $serviceHistory->id) }}" class="btn btn-primary">
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
                        <p class="mb-0">{{ $serviceHistory->error_message ?? 'No service history records found for this vehicle.' }}</p>
                        <p class="text-muted mt-3">This could mean:</p>
                        <ul>
                            <li>Vehicle is not registered with authorized service centers</li>
                            <li>Vehicle has no service records</li>
                            <li>Vehicle brand/model is not supported</li>
                        </ul>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('service-history.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Search Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
@endsection