@extends('layouts.app')

@section('title', 'E-Challan Result - SAHI GADI')

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

            @if($challanSearch->is_success)
                <div class="card border-0 shadow-lg">
                    <div class="card-header {{ $challanSearch->total_amount > 0 ? 'bg-danger' : 'bg-success' }} text-white py-3">
                        <h4 class="mb-0">
                            <i class="bi {{ $challanSearch->total_amount > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle' }} me-2"></i>
                            {{ $challanSearch->total_amount > 0 ? 'Challans Found' : 'No Challans' }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($cached) && $cached)
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5 class="fw-bold">Vehicle: {{ $challanSearch->vehicle_number }}</h5>
                            <div class="d-flex gap-3">
                                <span class="badge bg-primary">Total Challans: {{ $challanSearch->challan_count }}</span>
                                <span class="badge bg-danger">Total Amount: ₹{{ number_format($challanSearch->total_amount) }}</span>
                            </div>
                        </div>

                        @if($challanSearch->challan_data && count($challanSearch->challan_data) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Challan No</th>
                                            <th>Date</th>
                                            <th>Location</th>
                                            <th>Offence</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($challanSearch->challan_data as $challan)
                                        <tr>
                                            <td>{{ $challan['challanNo'] ?? 'N/A' }}</td>
                                            <td>{{ $challan['dateChallan'] ? \Carbon\Carbon::parse($challan['dateChallan'])->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $challan['locationChallan'] ?? 'N/A' }}</td>
                                            <td>{{ $challan['detailsViolation'][0]['offence'] ?? 'N/A' }}</td>
                                            <td>₹{{ number_format($challan['amountChallan'] ?? 0) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $challan['status'] == 'Paid' ? 'success' : 'danger' }}">
                                                    {{ $challan['status'] ?? 'Unknown' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>No pending challans found for this vehicle.
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
                        <p class="mb-0">{{ $challanSearch->error_message ?? 'No challan records found for this vehicle.' }}</p>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('challan-search.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Check Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
@endsection