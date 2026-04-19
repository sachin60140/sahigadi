@extends('layouts.admin')

@section('title', 'Transaction Details - SAHIGADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Transaction Details</h2>
    <div>
        <a href="{{ route('admin.customer-transactions.index', ['type' => $type]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Vehicle Number: <span class="text-primary">{{ strtoupper($type === 'vahan' ? $transaction->registration_number : $transaction->vehicle_number) }}</span></h5>
                <p class="mb-1"><strong>Customer:</strong> {{ $transaction->customer_name ?? 'N/A' }} | <i class="bi bi-telephone text-muted"></i> {{ $transaction->customer_phone ?? 'N/A' }}</p>
                @if($transaction->customer_email)
                    <p class="mb-1"><strong>Email:</strong> {{ $transaction->customer_email }}</p>
                @endif
                <p class="mb-1"><strong>Date:</strong> {{ $transaction->created_at->format('d M Y H:i') }}</p>
                <div class="mt-3 text-muted small">
                    <p class="mb-1"><strong>Order ID:</strong> {{ $transaction->razorpay_order_id ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Payment ID:</strong> <span class="font-monospace">{{ $transaction->razorpay_payment_id ?? 'N/A' }}</span></p>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                @if($transaction->is_success)
                    <span class="badge bg-success fs-6">Success</span>
                @else
                    <span class="badge bg-danger fs-6">Failed</span>
                @endif
                
                <h4 class="mt-3 text-success">Paid: ₹{{ number_format($transaction->paid_amount ?? 0, 2) }}</h4>
                
                @if($type === 'challan' && $transaction->is_success)
                    <p class="mt-2 fw-bold text-secondary">Total Challans: {{ $transaction->challan_count ?? 0 }} | Fine: ₹{{ number_format($transaction->total_amount ?? 0) }}</p>
                @endif

                <div class="mt-3 p-3 bg-light rounded text-start d-inline-block border">
                    <p class="mb-2">
                        <strong>Refund Status:</strong> 
                        @if($transaction->is_refunded)
                            <span class="badge bg-info">Refunded</span>
                        @else
                            <span class="badge bg-warning">Not Refunded</span>
                        @endif
                    </p>
                    @if($transaction->is_refunded)
                        <p class="mb-0 text-muted small"><i class="bi bi-arrow-return-left"></i> Ref ID: {{ $transaction->razorpay_refund_id }}</p>
                    @elseif(!$transaction->is_success && $transaction->razorpay_payment_id)
                        <form action="{{ route('admin.customer-transactions.refund', ['id' => $transaction->id, 'type' => $type]) }}" method="POST" onsubmit="return confirm('Issue full refund?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger mt-1 w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Issue Refund
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <hr class="my-4">

        @if($transaction->error_message)
            <div class="alert alert-danger">
                {{ $transaction->error_message }}
            </div>
        @endif
        
        @if($transaction->is_success)
            @if($type === 'challan')
                @if($transaction->challan_data && count($transaction->challan_data) > 0)
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
                                @foreach($transaction->challan_data as $challan)
                                <tr>
                                    <td>{{ $challan['challanNo'] ?? 'N/A' }}</td>
                                    <td>{{ !empty($challan['dateChallan']) ? \Carbon\Carbon::parse($challan['dateChallan'])->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $challan['locationChallan'] ?? 'N/A' }}</td>
                                    <td>{{ $challan['detailsViolation'][0]['offence'] ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($challan['amountChallan'] ?? 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ ($challan['status'] ?? '') == 'Paid' ? 'success' : 'danger' }}">
                                            {{ $challan['status'] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle me-2"></i>No pending challans found for this vehicle.
                    </div>
                @endif

            @elseif($type === 'vahan')
                @if($transaction->vehicle_data)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tbody>
                                    @foreach($transaction->vehicle_data as $key => $value)
                                    <tr>
                                        <td class="bg-light" style="width: 30%;"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                                        <td>{{ $value ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>No detailed data available.
                    </div>
                @endif

            @elseif($type === 'maruti')
                @if($transaction->records && $transaction->records->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Service Type</th>
                                    <th>Dealer</th>
                                    <th>Job Card / RO No</th>
                                    <th>Mileage</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->records as $record)
                                <tr>
                                    <td>{{ $record->svc_date ?? 'N/A' }}</td>
                                    <td>{{ $record->service_cate ?? 'N/A' }}</td>
                                    <td>{{ $record->dealer_name ?? 'N/A' }}</td>
                                    <td>{{ $record->register_no ?? 'N/A' }} / {{ $record->repair_order_no ?? 'N/A' }}</td>
                                    <td>{{ $record->mileage ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($record->total_amount ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info border-0">
                        <i class="bi bi-info-circle me-2"></i>No service records found.
                    </div>
                @endif
            
            @elseif($type === 'mahindra')
                @if($transaction->records && $transaction->records->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
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
                                @foreach($transaction->records as $record)
                                <tr>
                                    <td>{{ $record->svc_date ? \Carbon\Carbon::parse($record->svc_date)->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $record->dealer_name ?? 'N/A' }}</td>
                                    <td>{{ $record->work_type ?? 'N/A' }}</td>
                                    <td>{{ $record->mileage ? number_format($record->mileage).' km' : 'N/A' }}</td>
                                    <td>₹{{ $record->net_bill_amt ? number_format($record->net_bill_amt) : '0' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info border-0">
                        <i class="bi bi-info-circle me-2"></i>No service records found.
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
@endsection
