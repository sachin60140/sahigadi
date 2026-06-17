@extends('pdf.layout')

@section('doc-title', 'Customer RC Searches - SAHI GADI')
@section('brand-subtitle', 'Admin operations')
@section('report-kicker', 'Admin export')
@section('report-title', 'Customer RC Searches')
@section('report-meta', 'Generated '.now('Asia/Kolkata')->format('d M Y, h:i A'))
@section('footer-note', 'Admin export')

@section('content')
    <table class="summary-grid">
        <tr>
            <td style="width: 50%;"><div class="summary-card"><div class="summary-label">Total searches</div><div class="summary-value">{{ $searches->count() }}</div><div class="summary-note">Records in export</div></div></td>
            <td style="width: 50%;"><div class="summary-card"><div class="summary-label">Total revenue</div><div class="summary-value">Rs.{{ number_format($totalRevenue, 2) }}</div><div class="summary-note">Charges collected</div></div></td>
        </tr>
    </table>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Customer RC lookups</td><td class="section-caption">{{ $searches->count() }} entries</td></tr></table>
        <table class="data-table">
            <thead>
                <tr><td>ID</td><td>Customer</td><td>Phone</td><td>Registration number</td><td class="num">Charge paid</td><td>Status</td><td>Date</td></tr>
            </thead>
            <tbody>
                @forelse($searches as $search)
                    <tr>
                        <td>{{ $search->id }}</td>
                        <td>{{ $search->customer_name ?? 'N/A' }}</td>
                        <td>{{ $search->customer_phone ?? 'N/A' }}</td>
                        <td><strong>{{ $search->registration_number }}</strong></td>
                        <td class="num">Rs.{{ number_format($search->paid_amount ?? 0, 2) }}</td>
                        <td>@if($search->is_success)<span class="status">Success</span>@else<span class="status is-danger">Failed</span>@endif</td>
                        <td>{{ optional($search->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; color: #7a8799;">No records found for the specified period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
