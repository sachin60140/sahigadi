@extends('layouts.app')

@section('title', 'Challan PDF History')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            @include('frontend.customer.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Challan PDF Search History</h5>
                    <a href="{{ route('customer.challan-pdf.index') }}" class="btn btn-sm btn-light text-primary">New Search</a>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
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
                                            <a href="{{ $record->pdf_url }}" target="_blank" class="btn btn-sm btn-info text-white">Download PDF</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No search history found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($history->hasPages())
                <div class="card-footer bg-white">
                    {{ $history->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
