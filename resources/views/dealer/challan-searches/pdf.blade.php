@extends('pdf.layout')

@php
    $reference = 'SG-CHL-'.str_pad((string) ($challanSearch->id ?? 0), 6, '0', STR_PAD_LEFT);
    $reportDate = optional($challanSearch->created_at)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') ?? now('Asia/Kolkata')->format('d M Y, h:i A');

    $challans = $challanSearch->challan_data ?? [];
    $paidAmount = 0; $unpaidAmount = 0; $pendingAmount = 0;
    $physicalCourt = 0; $virtualCourt = 0; $noCourt = 0;

    usort($challans, function ($a, $b) {
        return (isset($b['dateChallan']) ? strtotime($b['dateChallan']) : 0)
             - (isset($a['dateChallan']) ? strtotime($a['dateChallan']) : 0);
    });

    foreach ($challans as $c) {
        $amount = floatval($c['amountChallan'] ?? 0);
        $status = strtolower($c['status'] ?? '');
        if ($status == 'paid') { $paidAmount += $amount; }
        elseif ($status == 'pending') { $pendingAmount += $amount; }
        else { $unpaidAmount += $amount; }

        $courtStatus = strtolower($c['court_status_desc'] ?? '');
        if ($courtStatus == 'physical court') { $physicalCourt++; }
        elseif ($courtStatus == 'virtual court') { $virtualCourt++; }
        else { $noCourt++; }
    }
@endphp

@section('doc-title', 'E-Challan Report - '.$challanSearch->vehicle_number.' - SAHI GADI')
@section('brand-subtitle', 'Verified vehicle intelligence')
@section('report-kicker', 'E-challan dossier')
@section('report-title', 'E-Challan Report')
@section('report-meta', 'Reference '.$reference.' | Report date '.$reportDate)
@section('reference', $reference)
@section('footer-note', 'E-challan report')

@section('content')
    <div class="hero-panel">
        <table>
            <tr>
                <td>
                    <div class="hero-label">Vehicle number</div>
                    <div class="hero-value">{{ strtoupper($challanSearch->vehicle_number) }}</div>
                </td>
                <td class="hero-aside">
                    @if($challanSearch->is_success)
                        <span class="hero-badge">{{ count($challans) }} CHALLAN RECORDS</span>
                    @else
                        <span class="hero-badge is-danger">LOOKUP UNSUCCESSFUL</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($challanSearch->is_success && $challanSearch->challan_data)
        <table class="summary-grid">
            <tr>
                <td><div class="summary-card"><div class="summary-label">Pending dues</div><div class="summary-value amount">Rs.{{ number_format($unpaidAmount + $pendingAmount) }}</div><div class="summary-note">Unpaid + pending</div></div></td>
                <td><div class="summary-card"><div class="summary-label">Paid amount</div><div class="summary-value">Rs.{{ number_format($paidAmount) }}</div><div class="summary-note">Settled challans</div></div></td>
                <td><div class="summary-card"><div class="summary-label">Total challans</div><div class="summary-value">{{ count($challans) }}</div><div class="summary-note">Records found</div></div></td>
                <td><div class="summary-card"><div class="summary-label">Total amount</div><div class="summary-value">Rs.{{ number_format($challanSearch->total_amount ?? 0) }}</div><div class="summary-note">All challans</div></div></td>
            </tr>
        </table>

        <div class="section">
            <table class="section-heading"><tr><td class="section-title">Challan details</td><td class="section-caption">Newest challan shown first</td></tr></table>
            <table class="data-table">
                <thead>
                    <tr>
                        <td>Challan no</td><td>Date</td><td>State</td><td>Offence</td><td>RTO</td>
                        <td class="num">Amount</td><td>Status</td><td>Court</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($challans as $c)
                        @php $status = strtolower($c['status'] ?? ''); $isPaid = $status == 'paid'; $isPending = $status == 'pending'; @endphp
                        <tr>
                            <td>{{ $c['challanNo'] ?? 'N/A' }}</td>
                            <td>{{ isset($c['dateChallan']) ? date('d M Y', strtotime($c['dateChallan'])) : 'N/A' }}</td>
                            <td>{{ $c['State'] ?? 'N/A' }}</td>
                            <td>{{ $c['detailsViolation'][0]['offence'] ?? 'N/A' }}</td>
                            <td>{{ $c['nameRTO'] ?? 'N/A' }}</td>
                            <td class="num">Rs.{{ number_format($c['amountChallan'] ?? 0) }}</td>
                            <td>
                                @if($isPaid)<span class="status">Paid</span>
                                @elseif($isPending)<span class="status is-warning">Pending</span>
                                @else<span class="status is-danger">Unpaid</span>@endif
                            </td>
                            <td>{{ $c['court_status_desc'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <table class="section-heading"><tr><td class="section-title">Summary</td><td class="section-caption">Court split and charge</td></tr></table>
            <table class="profile" style="table-layout: fixed;">
                <tr>
                    <td style="width: 25%;"><div class="field-label">Physical court</div><div class="field-value">{{ $physicalCourt }}</div></td>
                    <td style="width: 25%;"><div class="field-label">Virtual court</div><div class="field-value">{{ $virtualCourt }}</div></td>
                    <td style="width: 25%;"><div class="field-label">No court data</div><div class="field-value">{{ $noCourt }}</div></td>
                    <td style="width: 25%;"><div class="field-label">Service charge</div><div class="field-value">Rs.{{ number_format($challanSearch->charge_amount ?? 0, 2) }}</div></td>
                </tr>
            </table>
        </div>

        <div class="notice">
            Challan data is sourced from the connected provider for the stated registration number. Confirm dues on the official
            state transport / Parivahan portal before settlement.
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-title">No challans found</div>
            <div class="empty-state-copy">{{ $challanSearch->error_message ?: 'No challan records were returned for this vehicle.' }}</div>
        </div>
    @endif
@endsection
