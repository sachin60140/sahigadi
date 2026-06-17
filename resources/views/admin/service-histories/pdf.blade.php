@extends('pdf.layout')

@section('doc-title', 'Service History Reports - SAHI GADI')
@section('brand-subtitle', 'Admin operations')
@section('report-kicker', 'Admin export')
@section('report-title', 'Service History Reports')
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
        <table class="section-heading"><tr><td class="section-title">Service history records</td><td class="section-caption">{{ $searches->count() }} entries</td></tr></table>
        <table class="data-table">
            <thead>
                <tr><td>Vehicle number</td><td>Dealer</td><td class="num">Services found</td><td class="num">Charge</td><td>Date</td></tr>
            </thead>
            <tbody>
                @forelse($searches as $search)
                    <tr>
                        <td><strong>{{ $search->vehicle_number }}</strong></td>
                        <td>{{ optional($search->dealer)->name ?? 'N/A' }}</td>
                        <td class="num">{{ $search->service_count ?? 0 }}</td>
                        <td class="num">Rs.{{ number_format($search->charge_amount ?? 0, 2) }}</td>
                        <td>{{ optional($search->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; color: #7a8799;">No service history searches matched the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
