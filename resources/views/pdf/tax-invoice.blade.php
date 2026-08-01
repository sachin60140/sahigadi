<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice {{ $invoice->invoice_number }} - {{ $invoice->supplier_name }}</title>
    <style>
        @page { margin: 22px 24px 34px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "DejaVu Sans", Arial, sans-serif; color: #172033; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }

        .title-bar { margin-bottom: 10px; border: 1px solid #dce6e8; background: #f7fbfb; }
        .title-bar td { padding: 10px 13px; vertical-align: middle; }
        .brand-mark { width: 28px; height: 28px; border-radius: 5px; background: #0f766e; color: #fff; font-size: 11px; font-weight: bold; text-align: center; vertical-align: middle; }
        .brand-name { color: #101828; font-size: 15px; font-weight: bold; line-height: 1; }
        .brand-name span { color: #f26422; }
        .doc-title { text-align: right; color: #101828; font-size: 15px; font-weight: bold; letter-spacing: .5px; }
        .doc-sub { text-align: right; color: #667085; font-size: 7px; font-weight: bold; letter-spacing: .8px; text-transform: uppercase; margin-top: 3px; }

        .meta { margin-bottom: 10px; border: 1px solid #dfe7ea; }
        .meta td { width: 33.33%; padding: 8px 11px; border-right: 1px solid #e5ecee; vertical-align: top; }
        .meta td:last-child { border-right: 0; }
        .label { color: #7a8799; font-size: 6.8px; font-weight: bold; letter-spacing: .45px; text-transform: uppercase; }
        .value { margin-top: 2px; color: #25324a; font-size: 9.5px; font-weight: bold; }

        .parties { margin-bottom: 10px; border: 1px solid #dfe7ea; }
        .parties td { width: 50%; padding: 10px 12px; border-right: 1px solid #e5ecee; vertical-align: top; }
        .parties td:last-child { border-right: 0; }
        .party-head { color: #0f766e; font-size: 7px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .party-name { margin-top: 4px; color: #101828; font-size: 11px; font-weight: bold; }
        .party-line { margin-top: 3px; color: #5a6b82; font-size: 8px; line-height: 1.55; }
        .party-gst { margin-top: 4px; color: #25324a; font-size: 8.5px; font-weight: bold; }

        .items { margin-bottom: 0; border: 1px solid #dfe7ea; }
        .items thead td { padding: 7px 9px; background: #101828; color: #cfe9e4; font-size: 7px; font-weight: bold; letter-spacing: .5px; text-transform: uppercase; }
        .items tbody td { padding: 9px; border-bottom: 1px solid #eef2f3; color: #2b3a52; font-size: 9px; vertical-align: top; }
        .items tbody tr:last-child td { border-bottom: 0; }
        .num { text-align: right; }

        .totals { margin-top: 10px; }
        .totals td { vertical-align: top; }
        .words { width: 58%; padding-right: 12px; }
        .words-box { padding: 9px 11px; border: 1px solid #dfe7ea; background: #f8fbfb; }
        .totals-table { border: 1px solid #dfe7ea; }
        .totals-table td { padding: 6px 11px; border-bottom: 1px solid #eef2f3; font-size: 9px; }
        .totals-table tr:last-child td { border-bottom: 0; }
        .totals-table .k { color: #5a6b82; }
        .totals-table .v { text-align: right; font-weight: bold; color: #25324a; }
        .grand td { background: #f3faf8; color: #0f766e; font-size: 11px; font-weight: bold; }

        .foot { margin-top: 12px; }
        .foot td { width: 50%; vertical-align: top; padding-top: 4px; }
        .note { color: #7a8799; font-size: 7px; line-height: 1.6; }
        .sign { text-align: right; }
        .sign-for { color: #25324a; font-size: 8.5px; font-weight: bold; }
        .sign-space { height: 34px; }
        .sign-label { color: #7a8799; font-size: 7.5px; border-top: 1px solid #d7e2e5; padding-top: 4px; display: inline-block; min-width: 150px; text-align: center; }
    </style>
</head>
<body>
@php
    $money = static fn ($v) => 'Rs. '.number_format((float) $v, 2);

    $inWords = static fn ($amount) => \App\Models\Invoice::amountInWords((float) $amount);
@endphp

<table class="title-bar">
    <tr>
        <td style="width: 55%;">
            <table>
                <tr>
                    <td class="brand-mark">SG</td>
                    <td style="padding-left: 9px;">
                        <div class="brand-name">SAHI<span>GADI</span></div>
                        <div style="margin-top: 3px; color: #667085; font-size: 7px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase;">{{ $invoice->supplier_name }}</div>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <div class="doc-title">TAX INVOICE</div>
            <div class="doc-sub">Original for recipient</div>
        </td>
    </tr>
</table>

<table class="meta">
    <tr>
        <td><div class="label">Invoice number</div><div class="value">{{ $invoice->invoice_number }}</div></td>
        <td><div class="label">Invoice date</div><div class="value">{{ $invoice->issued_at->format('d M Y') }}</div></td>
        <td><div class="label">Place of supply</div><div class="value">{{ $invoice->place_of_supply ?: 'Not specified' }}{{ $invoice->place_of_supply_code ? ' ('.$invoice->place_of_supply_code.')' : '' }}</div></td>
    </tr>
    <tr>
        <td><div class="label">Payment mode</div><div class="value">{{ $invoice->payment_gateway ?: 'Online' }}</div></td>
        <td><div class="label">Payment reference</div><div class="value" style="font-size: 8px;">{{ $invoice->payment_reference ?: 'N/A' }}</div></td>
        <td><div class="label">Reverse charge</div><div class="value">{{ $invoice->reverse_charge ? 'Yes' : 'No' }}</div></td>
    </tr>
</table>

<table class="parties">
    <tr>
        <td>
            <div class="party-head">Supplier</div>
            <div class="party-name">{{ $invoice->supplier_name }}</div>
            <div class="party-line">{{ $invoice->supplier_address }}</div>
            <div class="party-gst">GSTIN: {{ $invoice->supplier_gstin ?: 'N/A' }}</div>
            <div class="party-line">State: {{ $invoice->supplier_state }}{{ $invoice->supplier_state_code ? ' ('.$invoice->supplier_state_code.')' : '' }}</div>
        </td>
        <td>
            <div class="party-head">Recipient</div>
            <div class="party-name">{{ $invoice->buyer_company ?: $invoice->buyer_name }}</div>
            @if($invoice->buyer_company)
                <div class="party-line">Attn: {{ $invoice->buyer_name }}</div>
            @endif
            <div class="party-line">{{ collect([$invoice->buyer_address, $invoice->buyer_city, $invoice->buyer_state, $invoice->buyer_pincode])->filter()->implode(', ') ?: 'Address not provided' }}</div>
            <div class="party-line">{{ collect([$invoice->buyer_phone, $invoice->buyer_email])->filter()->implode(' | ') }}</div>
            <div class="party-gst">GSTIN: {{ $invoice->buyer_gstin ?: 'Unregistered' }}</div>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <td style="width: 5%;">#</td>
            <td style="width: 47%;">Description of service</td>
            <td style="width: 12%;">SAC</td>
            <td style="width: 12%;" class="num">Taxable value</td>
            <td style="width: 12%;" class="num">Tax rate</td>
            <td style="width: 12%;" class="num">Tax amount</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>{{ $invoice->description }}</td>
            <td>{{ $invoice->sac_code }}</td>
            <td class="num">{{ number_format((float) $invoice->taxable_value, 2) }}</td>
            <td class="num">{{ rtrim(rtrim(number_format((float) $invoice->cgst_rate + (float) $invoice->sgst_rate + (float) $invoice->igst_rate, 2), '0'), '.') }}%</td>
            <td class="num">{{ number_format((float) $invoice->total_tax, 2) }}</td>
        </tr>
    </tbody>
</table>

<table class="totals">
    <tr>
        <td class="words">
            <div class="words-box">
                <div class="label">Amount in words</div>
                <div style="margin-top: 3px; color: #25324a; font-size: 9px; font-weight: bold;">{{ $inWords($invoice->total_amount) }}</div>
            </div>
        </td>
        <td>
            <table class="totals-table">
                <tr><td class="k">Taxable value</td><td class="v">{{ $money($invoice->taxable_value) }}</td></tr>
                @if($invoice->isIntraState())
                    <tr><td class="k">CGST @ {{ rtrim(rtrim(number_format((float) $invoice->cgst_rate, 2), '0'), '.') }}%</td><td class="v">{{ $money($invoice->cgst_amount) }}</td></tr>
                    <tr><td class="k">SGST @ {{ rtrim(rtrim(number_format((float) $invoice->sgst_rate, 2), '0'), '.') }}%</td><td class="v">{{ $money($invoice->sgst_amount) }}</td></tr>
                @else
                    <tr><td class="k">IGST @ {{ rtrim(rtrim(number_format((float) $invoice->igst_rate, 2), '0'), '.') }}%</td><td class="v">{{ $money($invoice->igst_amount) }}</td></tr>
                @endif
                <tr class="grand"><td>Total</td><td class="v" style="color: #0f766e;">{{ $money($invoice->total_amount) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<table class="foot">
    <tr>
        <td>
            <div class="note">
                This is a computer-generated tax invoice and does not require a physical signature.<br>
                Amount received against prepaid wallet balance for vehicle information services.<br>
                Subject to Ghaziabad jurisdiction.
            </div>
        </td>
        <td class="sign">
            <div class="sign-for">For {{ $invoice->supplier_name }}</div>
            <div class="sign-space"></div>
            <div class="sign-label">Authorised signatory</div>
        </td>
    </tr>
</table>
</body>
</html>
