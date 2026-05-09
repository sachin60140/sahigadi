@extends('layouts.admin')

@section('title', 'Challan PDF Search Logs')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Search Logs</h6>
            <a href="{{ route('admin.challan-pdf.export-logs') }}" class="btn btn-sm btn-success">Export to CSV</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Challan/Vehicle Number</th>
                            <th>User Type</th>
                            <th>User Name</th>
                            <th>Status</th>
                            <th>Charge</th>
                            <th>Error Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $log->vehicle_number }}</td>
                            <td>
                                @if($log->customer_id)
                                    <span class="badge bg-info">Customer</span>
                                @elseif($log->dealer_id)
                                    <span class="badge bg-primary">Dealer</span>
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td>
                                @if($log->customer_id)
                                    {{ $log->customer->name ?? 'N/A' }}
                                @elseif($log->dealer_id)
                                    {{ $log->dealer->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($log->is_success)
                                    <span class="badge bg-success">Success</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($log->charge_amount, 2) }}</td>
                            <td>{{ $log->error_message ?? '-' }}</td>
                            <td>
                                @if($log->is_success && $log->pdf_url)
                                    <a href="{{ $log->pdf_url }}" target="_blank" class="btn btn-sm btn-primary">View PDF</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
