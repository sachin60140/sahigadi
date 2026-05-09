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
                                    @if(isset($log->customer->customer_unique_id))
                                        <br><small class="text-muted">{{ $log->customer->customer_unique_id }}</small>
                                    @endif
                                @elseif($log->dealer_id)
                                    {{ $log->dealer->name ?? 'N/A' }}
                                    @if(isset($log->dealer->dealer_unique_id))
                                        <br><small class="text-muted">{{ $log->dealer->dealer_unique_id }}</small>
                                    @endif
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
                                    <a href="{{ $log->pdf_url }}" target="_blank" class="btn btn-sm btn-primary mb-1">View PDF</a>
                                @endif
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                    View Details
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog modal-lg">
                                    <div class="modal-content text-start">
                                      <div class="modal-header">
                                        <h5 class="modal-title">API Details for {{ $log->vehicle_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        <h6>API Request:</h6>
                                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 200px; overflow-y: auto;"><code>{{ json_encode($log->api_request, JSON_PRETTY_PRINT) }}</code></pre>
                                        
                                        <h6 class="mt-3">API Response:</h6>
                                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->api_response, JSON_PRETTY_PRINT) }}</code></pre>
                                        
                                        @if($log->error_message)
                                            <h6 class="mt-3 text-danger">Error Message:</h6>
                                            <p class="text-danger">{{ $log->error_message }}</p>
                                        @endif
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
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
