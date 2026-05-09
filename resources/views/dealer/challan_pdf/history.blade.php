@extends('layouts.dealer')

@section('title', 'Challan PDF History')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Challan PDF Search History</h6>
            <a href="{{ route('dealer.challan-pdf.index') }}" class="btn btn-sm btn-primary">New Search</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Challan Number</th>
                            <th>Status</th>
                            <th>Amount Deducted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $record)
                        <tr>
                            <td>{{ $record->created_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $record->vehicle_number }}</td>
                            <td>
                                @if($record->is_success)
                                    <span class="badge bg-success">Success</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($record->charge_amount, 2) }}</td>
                            <td>
                                @if($record->is_success && $record->pdf_url)
                                    <a href="{{ $record->pdf_url }}" target="_blank" class="btn btn-sm btn-info">Download PDF</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No search history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $history->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
