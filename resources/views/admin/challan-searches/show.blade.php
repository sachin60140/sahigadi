@extends('layouts.admin')

@section('title', 'E-Challan Search Details - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt me-2"></i>E-Challan Search Details</h2>
    <div>
        <a href="{{ route('admin.challan-searches.download-pdf', $challanSearch->id) }}" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
        <a href="{{ route('admin.challan-searches.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Vehicle Number: {{ $challanSearch->vehicle_number }}</h5>
                <p>Dealer: {{ $challanSearch->dealer->name ?? 'N/A' }}</p>
                <p>Date: {{ $challanSearch->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                @if($challanSearch->is_success)
                    <span class="badge bg-success">Success</span>
                @else
                    <span class="badge bg-danger">Failed</span>
                @endif
                <p class="mt-2">Challans: {{ $challanSearch->challan_count }} | Amount: ₹{{ number_format($challanSearch->total_amount ?? 0) }}</p>
            </div>
        </div>

        @if($challanSearch->is_success && $challanSearch->challan_data)
            <div class="table-responsive">
                <table class="table table-bordered">
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
                            <td>{{ $challan['dateChallan'] ?? 'N/A' }}</td>
                            <td>{{ $challan['locationChallan'] ?? 'N/A' }}</td>
                            <td>{{ $challan['detailsViolation'][0]['offence'] ?? 'N/A' }}</td>
                            <td>₹{{ number_format($challan['amountChallan'] ?? 0) }}</td>
                            <td>{{ $challan['status'] ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-danger">
                {{ $challanSearch->error_message ?? 'No data available' }}
            </div>
        @endif
    </div>
</div>
@endsection