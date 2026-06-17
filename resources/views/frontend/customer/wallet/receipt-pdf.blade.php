@extends('pdf.layout')

@php
    $receiptNo = 'RCPT-'.date('Y').'-'.str_pad((string) $transaction->id, 5, '0', STR_PAD_LEFT);
@endphp

@section('doc-title', 'Wallet Recharge Receipt - '.$transaction->id.' - SAHI GADI')
@section('brand-subtitle', 'Awani Enterprises')
@section('report-kicker', 'Wallet recharge')
@section('report-title', 'Payment Receipt')
@section('report-meta', 'Receipt '.$receiptNo.' | '.$date)
@section('reference', $receiptNo)
@section('footer-note', 'Payment receipt')

@section('content')
    <div class="hero-panel">
        <table>
            <tr>
                <td>
                    <div class="hero-label">Amount received</div>
                    <div class="hero-value">Rs.{{ number_format($totalAmount, 2) }}</div>
                </td>
                <td class="hero-aside">
                    <span class="hero-badge">PAYMENT CONFIRMED</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Receipt parties</td><td class="section-caption">Receipt {{ $receiptNo }}</td></tr></table>
        <table class="profile" style="table-layout: fixed;">
            <tr>
                <td style="width: 50%;">
                    <div class="field-label">Billed by</div>
                    <div class="field-value">Awani Enterprises (SAHI GADI)</div>
                    <div style="margin-top: 4px; color: #5a6b82; font-size: 7.5px; line-height: 1.6;">
                        UGF-4-5, Parsvanath Majestic Arcade, Vaibhav Khand, Indirapuram, Ghaziabad, UP - 201014<br>
                        GST 09CEKPS2342H1Z8 &nbsp;|&nbsp; support@sahigadi.com &nbsp;|&nbsp; 9811588801
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="field-label">Billed to</div>
                    <div class="field-value">{{ $customer->name ?: 'Customer' }}</div>
                    <div style="margin-top: 4px; color: #5a6b82; font-size: 7.5px; line-height: 1.6;">
                        {{ $customer->customer_unique_id ? 'ID '.$customer->customer_unique_id : '' }}
                        @if($customer->company_name) <br>{{ $customer->company_name }} @endif
                        <br>{{ collect([$customer->address, $customer->city, $customer->state, $customer->pincode])->filter()->implode(', ') ?: 'Address not provided' }}
                        <br>{{ $customer->phone }}{{ $customer->email ? ' | '.$customer->email : '' }}
                        @if($customer->gst_number) <br>GST {{ $customer->gst_number }} @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Transaction details</td><td class="section-caption">Reference {{ $transaction->reference_id ?? 'N/A' }}</td></tr></table>
        <table class="data-table">
            <thead><tr><td>Reference no</td><td>Description</td><td class="num">Amount (Rs.)</td></tr></thead>
            <tbody>
                <tr>
                    <td>{{ $transaction->reference_id ?? 'N/A' }}</td>
                    <td>Customer wallet recharge</td>
                    <td class="num">{{ number_format($baseAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 55%; margin-left: 45%; margin-top: 10px; border: 1px solid #cfe0dd;">
            <tr><td style="padding: 7px 12px; color: #5a6b82; font-size: 8.5px; font-weight: bold;">Base amount</td><td style="padding: 7px 12px; text-align: right; font-weight: bold;">Rs. {{ number_format($baseAmount, 2) }}</td></tr>
            <tr><td style="padding: 7px 12px; color: #5a6b82; font-size: 8.5px; font-weight: bold;">GST (18%)</td><td style="padding: 7px 12px; text-align: right; font-weight: bold;">Rs. {{ number_format($gstAmount, 2) }}</td></tr>
            <tr style="background: #f3faf8;"><td style="padding: 9px 12px; color: #0f766e; font-size: 10px; font-weight: bold;">Total paid</td><td style="padding: 9px 12px; text-align: right; color: #0f766e; font-size: 13px; font-weight: bold;">Rs. {{ number_format($totalAmount, 2) }}</td></tr>
        </table>
    </div>

    <div class="notice">
        This is a computer-generated receipt and does not require a physical signature. Thank you for your business with SAHI GADI.
    </div>
@endsection
