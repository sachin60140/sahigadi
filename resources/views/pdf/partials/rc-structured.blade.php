@php
    $val = static fn ($v) => ($v === null || $v === '') ? 'Not available' : $v;
@endphp

<div class="hero-panel">
    <table>
        <tr>
            <td>
                <div class="hero-label">Registration number</div>
                <div class="hero-value">{{ strtoupper($reg) }}</div>
            </td>
            <td class="hero-aside">
                @if($isSuccess && $detail)
                    <span class="hero-badge">RC DETAILS VERIFIED</span>
                @else
                    <span class="hero-badge is-danger">LOOKUP UNSUCCESSFUL</span>
                @endif
            </td>
        </tr>
    </table>
</div>

@if($isSuccess && $detail)
    <table class="summary-grid">
        <tr>
            <td><div class="summary-card"><div class="summary-label">Make &amp; model</div><div class="summary-value">{{ $val(trim(($detail->make ?? '').' '.($detail->model ?? ''))) }}</div><div class="summary-note">Vehicle identity</div></div></td>
            <td><div class="summary-card"><div class="summary-label">Fuel type</div><div class="summary-value">{{ $val($detail->fuel_type ?? null) }}</div><div class="summary-note">Reported fuel</div></div></td>
            <td><div class="summary-card"><div class="summary-label">RC status</div><div class="summary-value">{{ $val($detail->rc_status ?? null) }}</div><div class="summary-note">Registration state</div></div></td>
            <td><div class="summary-card"><div class="summary-label">Owner</div><div class="summary-value" style="font-size: 11px;">{{ $val($detail->owner_name ?? null) }}</div><div class="summary-note">Registered owner</div></div></td>
        </tr>
    </table>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Owner details</td><td class="section-caption">Registered keeper information</td></tr></table>
        <table class="profile" style="table-layout: fixed;">
            <tr>
                <td style="width: 25%;"><div class="field-label">Owner name</div><div class="field-value">{{ $val($detail->owner_name ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Father's name</div><div class="field-value">{{ $val($detail->father_name ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Mobile number</div><div class="field-value">{{ $val($detail->mobile_number ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">RTO location</div><div class="field-value">{{ $val($detail->rto_location ?? null) }}</div></td>
            </tr>
            <tr>
                <td colspan="4"><div class="field-label">Address</div><div class="field-value">{{ $val($detail->address ?? null) }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Vehicle details</td><td class="section-caption">Specification on record</td></tr></table>
        <table class="profile" style="table-layout: fixed;">
            <tr>
                <td style="width: 25%;"><div class="field-label">Category</div><div class="field-value">{{ $val($detail->vehicle_category ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Make</div><div class="field-value">{{ $val($detail->make ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Model</div><div class="field-value">{{ $val($detail->model ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Colour</div><div class="field-value">{{ $val($detail->color ?? null) }}</div></td>
            </tr>
            <tr>
                <td><div class="field-label">Fuel type</div><div class="field-value">{{ $val($detail->fuel_type ?? null) }}</div></td>
                <td><div class="field-label">Seating capacity</div><div class="field-value">{{ $val($detail->seats ?? null) }}</div></td>
                <td><div class="field-label">RC status</div><div class="field-value">{{ $val($detail->rc_status ?? null) }}</div></td>
                <td><div class="field-label">Engine number</div><div class="field-value">{{ $val($detail->engine_number ?? null) }}</div></td>
            </tr>
            <tr>
                <td colspan="2"><div class="field-label">Chassis number</div><div class="field-value">{{ $val($detail->chassis_number ?? null) }}</div></td>
                <td colspan="2"><div class="field-label">Document reference</div><div class="field-value">{{ $reference }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Documents &amp; status</td><td class="section-caption">Insurance, fitness and compliance</td></tr></table>
        <table class="profile" style="table-layout: fixed;">
            <tr>
                <td style="width: 25%;"><div class="field-label">Insurance provider</div><div class="field-value">{{ $val($detail->insurance_provider ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Insurance valid till</div><div class="field-value">{{ $val($detail->insurance_date ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">Fitness valid till</div><div class="field-value">{{ $val($detail->fitness_date ?? null) }}</div></td>
                <td style="width: 25%;"><div class="field-label">PUC valid till</div><div class="field-value">{{ $val($detail->puc_validity ?? null) }}</div></td>
            </tr>
            <tr>
                <td><div class="field-label">Blacklist status</div><div class="field-value">{{ $detail->blacklist_status ?? 'Clean' }}</div></td>
                <td colspan="3"><div class="field-label">Financed</div><div class="field-value">{{ ($detail->financed ?? false) ? 'Yes - '.($detail->lender_name ?? '') : 'No' }}</div></td>
            </tr>
        </table>
    </div>

    @if(! empty($summaryRows))
        <div class="section">
            <table class="section-heading"><tr><td class="section-title">Search summary</td><td class="section-caption">Request context</td></tr></table>
            <table class="kv-table">
                @foreach($summaryRows as $label => $value)
                    <tr><td class="kv-key">{{ $label }}</td><td class="kv-val">{{ $value }}</td></tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="notice">
        This report presents registration data returned by the connected RTO/Vahan provider for the stated number.
        Verify against the physical RC, ownership documents and a vehicle inspection before any purchase decision.
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-title">RC details could not be prepared</div>
        <div class="empty-state-copy">{{ $errorMessage ?: 'No registration data was returned for this number.' }}</div>
    </div>
@endif
