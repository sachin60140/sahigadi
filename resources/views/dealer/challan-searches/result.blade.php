@extends('layouts.dealer')

@push('styles')
<style>
.summary-card { transition: transform 0.2s; }
.summary-card:hover { transform: translateY(-3px); }
</style>
@endpush

@section('title', 'Challan Result - Dealer Panel')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            @if(isset($success) && !$success)
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Search Failed</h5>
                    <p class="mb-0">{{ $message }}</p>
                </div>
            @endif

            @if($challan->is_success)
                @php
                $challans = $challan->challan_data ?? [];
                $paidAmount = 0;
                $unpaidAmount = 0;
                $pendingAmount = 0;
                $physicalCourt = 0;
                $virtualCourt = 0;
                $noCourt = 0;
                
                usort($challans, function($a, $b) {
                    $dateA = isset($a['dateChallan']) ? strtotime($a['dateChallan']) : 0;
                    $dateB = isset($b['dateChallan']) ? strtotime($b['dateChallan']) : 0;
                    return $dateB - $dateA;
                });
                
                foreach ($challans as $c) {
                    $amount = floatval($c['amountChallan'] ?? 0);
                    $status = strtolower($c['status'] ?? '');
                    
                    if ($status == 'paid') {
                        $paidAmount += $amount;
                    } elseif ($status == 'pending') {
                        $pendingAmount += $amount;
                    } else {
                        $unpaidAmount += $amount;
                    }
                    
                    $courtStatus = strtolower($c['court_status_desc'] ?? '');
                    if ($courtStatus == 'physical court') {
                        $physicalCourt++;
                    } elseif ($courtStatus == 'virtual court') {
                        $virtualCourt++;
                    } else {
                        $noCourt++;
                    }
                }
                @endphp

                <div class="card mb-4">
                    <div class="card-header {{ ($unpaidAmount + $pendingAmount) > 0 ? 'bg-danger' : 'bg-success' }} text-white">
                        <h4 class="mb-0">
                            <i class="bi {{ ($unpaidAmount + $pendingAmount) > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle' }} me-2"></i>
                            {{ ($unpaidAmount + $pendingAmount) > 0 ? 'Challans Found' : 'No Pending Challans' }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($cached) && $cached)
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5>Vehicle: {{ $challan->vehicle_number }}</h5>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card summary-card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">₹{{ number_format($unpaidAmount + $pendingAmount) }}</h3>
                                        <small>Pending Amount</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">₹{{ number_format($paidAmount) }}</h3>
                                        <small>Paid Amount</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card bg-warning">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">₹{{ number_format($unpaidAmount) }}</h3>
                                        <small>Unpaid Amount</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $challan->challan_count }}</h3>
                                        <small>Total Challans</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($physicalCourt > 0 || $virtualCourt > 0 || $noCourt > 0)
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card summary-card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $physicalCourt }}</h3>
                                        <small>Physical Court</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card summary-card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $virtualCourt }}</h3>
                                        <small>Virtual Court</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card summary-card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $noCourt }}</h3>
                                        <small>No Court Data</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($challan->challan_data && count($challan->challan_data) > 0)
                            <h5 class="mb-3">Challan Details <small class="text-muted">(Sorted by Date - Newest First)</small></h5>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Challan No</th>
                                            <th>Date</th>
                                            <th>State</th>
                                            <th>Location</th>
                                            <th>Offence</th>
                                            <th>RTO</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Court</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($challans as $c)
                                        @php
                                            $status = strtolower($c['status'] ?? '');
                                            $isPaid = ($status == 'paid');
                                            $isPending = ($status == 'pending');
                                        @endphp
                                        <tr>
                                            <td>{{ $c['challanNo'] ?? 'N/A' }}</td>
                                            <td>{{ $c['dateChallan'] ? \Carbon\Carbon::parse($c['dateChallan'])->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $c['State'] ?? 'N/A' }}</td>
                                            <td>{{ $c['locationChallan'] ?? 'N/A' }}</td>
                                            <td>{{ $c['detailsViolation'][0]['offence'] ?? 'N/A' }}</td>
                                            <td>{{ $c['nameRTO'] ?? 'N/A' }}</td>
                                            <td class="{{ $isPaid ? 'text-success' : ($isPending ? 'text-warning' : 'text-danger') }} fw-bold">₹{{ number_format($c['amountChallan'] ?? 0) }}</td>
                                            <td>
                                                @if($isPaid)
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($isPending)
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Unpaid</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $courtStatus = $c['court_status_desc'] ?? ''; @endphp
                                                @if($courtStatus)
                                                    @if(strtolower($courtStatus) == 'physical court')
                                                        <span class="badge bg-secondary">{{ $courtStatus }}</span>
                                                    @elseif(strtolower($courtStatus) == 'virtual court')
                                                        <span class="badge bg-info">{{ $courtStatus }}</span>
                                                    @else
                                                        <span class="badge bg-outline-dark">{{ $courtStatus }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>No pending challans found.
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Search Date: {{ $challan->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('dealer.challan-search.pdf', $challan) }}" class="btn btn-primary">
                                        <i class="bi bi-download me-2"></i>Download PDF
                                    </a>
                                    <a href="{{ route('dealer.challan-search.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $challan->error_message ?? 'No challan records found.' }}</p>
                        <a href="{{ route('dealer.challan-search.index') }}" class="btn btn-outline-secondary mt-3">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection