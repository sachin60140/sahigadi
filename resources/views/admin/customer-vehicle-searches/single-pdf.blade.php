@extends('pdf.layout')

@php
    $reference = 'SG-RC-'.str_pad((string) ($vehicleSearch->id ?? 0), 6, '0', STR_PAD_LEFT);
    $reportDate = optional($vehicleSearch->created_at)->format('d M Y, h:i A') ?? now('Asia/Kolkata')->format('d M Y, h:i A');
    $data = collect($vehicleSearch->is_success && $vehicleSearch->vehicle_data ? $vehicleSearch->vehicle_data : []);
@endphp

@section('doc-title', 'RC Details - '.$vehicleSearch->registration_number.' - SAHI GADI')
@section('brand-subtitle', 'Verified vehicle intelligence')
@section('report-kicker', 'Vehicle RC dossier')
@section('report-title', 'RC Verification Report')
@section('report-meta', 'Reference '.$reference.' | Report date '.$reportDate)
@section('reference', $reference)
@section('footer-note', 'Vehicle RC report')

@section('content')
    <div class="hero-panel">
        <table>
            <tr>
                <td>
                    <div class="hero-label">Registration number</div>
                    <div class="hero-value">{{ strtoupper($vehicleSearch->registration_number) }}</div>
                </td>
                <td class="hero-aside">
                    @if($vehicleSearch->is_success)
                        <span class="hero-badge">RC DETAILS VERIFIED</span>
                    @else
                        <span class="hero-badge is-danger">LOOKUP UNSUCCESSFUL</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($vehicleSearch->is_success && $data->isNotEmpty())
        <table class="summary-grid">
            <tr>
                <td><div class="summary-card"><div class="summary-label">Status</div><div class="summary-value">Verified</div><div class="summary-note">Provider confirmed</div></div></td>
                <td><div class="summary-card"><div class="summary-label">Search date</div><div class="summary-value">{{ optional($vehicleSearch->created_at)->format('d M Y') ?? 'N/A' }}</div><div class="summary-note">Report generated</div></div></td>
                <td><div class="summary-card"><div class="summary-label">Data points</div><div class="summary-value">{{ $data->count() }}</div><div class="summary-note">Fields returned</div></div></td>
                <td><div class="summary-card"><div class="summary-label">Amount paid</div><div class="summary-value">Rs.{{ number_format((float) ($vehicleSearch->paid_amount ?? 0), 2) }}</div><div class="summary-note">Lookup charge</div></div></td>
            </tr>
        </table>

        <div class="section">
            <table class="section-heading"><tr><td class="section-title">Requested by</td><td class="section-caption">Search request context</td></tr></table>
            <table class="profile" style="table-layout: fixed;">
                <tr>
                    <td style="width: 25%;"><div class="field-label">Customer</div><div class="field-value">{{ $vehicleSearch->customer_name ?: 'Not provided' }}</div></td>
                    <td style="width: 25%;"><div class="field-label">Phone</div><div class="field-value">{{ $vehicleSearch->customer_phone ?: 'Not provided' }}</div></td>
                    <td style="width: 25%;"><div class="field-label">Search date</div><div class="field-value">{{ $reportDate }}</div></td>
                    <td style="width: 25%;"><div class="field-label">Document reference</div><div class="field-value">{{ $reference }}</div></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table class="section-heading"><tr><td class="section-title">Vehicle details</td><td class="section-caption">As returned by the verification provider</td></tr></table>
            <table class="kv-table">
                @foreach($data as $key => $value)
                    <tr>
                        <td class="kv-key">{{ ucwords(str_replace(['_', '-'], ' ', $key)) }}</td>
                        <td class="kv-val">{{ is_array($value) ? collect($value)->filter()->implode(', ') : (($value === null || $value === '') ? 'Not available' : $value) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="notice">
            This report presents registration data returned by the connected RTO/Vahan provider for the stated number.
            Verify against the physical RC, ownership documents and a vehicle inspection before any purchase decision.
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-title">RC details could not be prepared</div>
            <div class="empty-state-copy">{{ $vehicleSearch->error_message ?: 'No registration data was returned for this number.' }}</div>
        </div>
    @endif
@endsection
