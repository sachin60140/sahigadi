@extends('layouts.app')

@section('title', 'RC Search Result - SAHIGADI')

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

            @if($vehicleSearch->is_success)
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Vehicle Details Found</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($cached) && $cached)
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5 class="fw-bold">Registration Number: {{ $vehicleSearch->registration_number }}</h5>
                            @if($vehicleSearch->customer_name)
                                <p class="text-muted mb-0">Requested by: {{ $vehicleSearch->customer_name }}</p>
                            @endif
                        </div>

                        @if($vehicleSearch->vehicle_data)
                            <div class="row g-3">
                                @foreach($vehicleSearch->vehicle_data as $key => $value)
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">{{ ucwords(str_replace('_', ' ', $key)) }}</small>
                                        <strong>{{ $value ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>No detailed data available.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $vehicleSearch->error_message ?? 'No vehicle details found for this registration number.' }}</p>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('vehicle-search.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Search Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
@endsection