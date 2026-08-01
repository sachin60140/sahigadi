<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RC Details - {{ $vehicleSearch->registration_number }} - SAHI GADI</title>
    <style>
        @page { margin: 18px 20px 30px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "DejaVu Sans", Arial, sans-serif; color: #172033; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; }

        .masthead { margin-bottom: 9px; border: 1px solid #dce6e8; background: #f7fbfb; }
        .masthead td { padding: 9px 12px; vertical-align: middle; }
        .brand-mark { width: 26px; height: 26px; border-radius: 5px; background: #0f766e; color: #fff; font-size: 11px; font-weight: bold; text-align: center; vertical-align: middle; }
        .brand-name { color: #101828; font-size: 14px; font-weight: bold; line-height: 1; }
        .brand-name span { color: #f26422; }
        .brand-sub { margin-top: 3px; color: #667085; font-size: 6px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .head-right { text-align: right; }
        .head-kicker { color: #0f766e; font-size: 6px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .head-title { margin-top: 2px; color: #101828; font-size: 12px; font-weight: bold; }
        .head-meta { margin-top: 2px; color: #667085; font-size: 6.5px; }

        .hero { margin-bottom: 9px; background: #101828; color: #fff; }
        .hero td { padding: 9px 13px; vertical-align: middle; }
        .hero-label { color: #99e6dc; font-size: 6.5px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .hero-value { margin-top: 2px; font-size: 19px; font-weight: bold; letter-spacing: 1px; }
        .hero-aside { text-align: right; }
        .hero-badge { display: inline-block; padding: 4px 9px; border: 1px solid #46716d; border-radius: 4px; background: #183934; color: #d8fff8; font-size: 7px; font-weight: bold; }
        .hero-badge.is-danger { border-color: #7c4a45; background: #3a201e; color: #ffd9d4; }
        .hero-fact { color: #cbd5e1; font-size: 6.5px; margin-top: 4px; }
        .hero-fact b { color: #fff; }

        .sec-head { margin: 9px 0 4px; border-bottom: 1px solid #d7e2e5; padding-bottom: 3px; }
        .sec-title { color: #101828; font-size: 9px; font-weight: bold; }
        .sec-title:before { content: ""; display: inline-block; width: 3px; height: 9px; margin-right: 6px; background: #f26422; vertical-align: -1px; }
        .sec-count { color: #98a2b3; font-size: 6px; }

        .grid { table-layout: fixed; }
        .grid td { width: 33.33%; padding: 3px 8px 3px 0; vertical-align: top; }
        .f-label { color: #98a2b3; font-size: 6px; font-weight: bold; letter-spacing: .3px; text-transform: uppercase; }
        .f-value { color: #25324a; font-size: 8px; font-weight: bold; margin-top: 1px; word-wrap: break-word; }

        .empty { padding: 26px; border: 1px solid #f1c9b7; background: #fff8f4; text-align: center; }
        .empty-title { color: #9e3e14; font-size: 12px; font-weight: bold; }
        .empty-copy { margin-top: 6px; color: #7a4d39; font-size: 8px; }

        .footer { position: fixed; left: 0; right: 0; bottom: -20px; border-top: 1px solid #dce4e7; padding-top: 5px; color: #7d8999; font-size: 6px; }
        .footer .right { text-align: right; }
    </style>
</head>
<body>
@php
    $reference = 'SG-RC-'.str_pad((string) ($vehicleSearch->id ?? 0), 6, '0', STR_PAD_LEFT);
    $reportDate = optional($vehicleSearch->created_at)->format('d M Y, h:i A') ?? now('Asia/Kolkata')->format('d M Y, h:i A');
    $raw = is_array($vehicleSearch->raw_response) ? $vehicleSearch->raw_response : [];
    $sections = $sections ?? [];
    $makeModel = trim(($raw['makerDescription'] ?? '').' '.($raw['makerModel'] ?? ''));
@endphp

<div class="footer">
    <table><tr>
        <td>SAHI GADI &middot; Vehicle RC report &middot; Confidential</td>
        <td class="right">{{ $reference }} &middot; Generated {{ now('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
    </tr></table>
</div>

<table class="masthead">
    <tr>
        <td style="width: 55%;">
            <table><tr>
                <td class="brand-mark">SG</td>
                <td style="padding-left: 8px;"><div class="brand-name">SAHI<span>GADI</span></div><div class="brand-sub">Verified vehicle intelligence</div></td>
            </tr></table>
        </td>
        <td class="head-right">
            <div class="head-kicker">Vehicle RC dossier</div>
            <div class="head-title">RC Verification Report</div>
            <div class="head-meta">Reference {{ $reference }} &middot; {{ $reportDate }}</div>
        </td>
    </tr>
</table>

<table class="hero">
    <tr>
        <td>
            <div class="hero-label">Registration number</div>
            <div class="hero-value">{{ strtoupper($vehicleSearch->registration_number) }}</div>
            @if($makeModel !== '' || ($raw['fuelType'] ?? ''))
                <div class="hero-fact"><b>{{ $makeModel ?: 'Vehicle' }}</b>@if($raw['fuelType'] ?? '') &middot; {{ $raw['fuelType'] }}@endif @if($raw['rto'] ?? '') &middot; {{ $raw['rto'] }}@endif</div>
            @endif
        </td>
        <td class="hero-aside">
            @if($vehicleSearch->is_success)
                <span class="hero-badge">RC DETAILS VERIFIED</span>
            @else
                <span class="hero-badge is-danger">LOOKUP UNSUCCESSFUL</span>
            @endif
            <div class="hero-fact" style="margin-top: 6px;">Charged Rs.{{ number_format((float) ($vehicleSearch->debit_amount ?? 0), 2) }}</div>
        </td>
    </tr>
</table>

@if($vehicleSearch->is_success && count($sections))
    @foreach($sections as $section)
        <table class="sec-head"><tr><td class="sec-title">{{ $section['title'] }}</td><td style="text-align: right;"><span class="sec-count">{{ count($section['items']) }} fields</span></td></tr></table>
        <table class="grid">
            @foreach(array_chunk($section['items'], 3) as $triple)
                <tr>
                    @foreach($triple as $item)
                        <td><div class="f-label">{{ $item['label'] }}</div><div class="f-value">{{ $item['value'] }}</div></td>
                    @endforeach
                    @for($i = count($triple); $i < 3; $i++)<td></td>@endfor
                </tr>
            @endforeach
        </table>
    @endforeach
@else
    <div class="empty">
        <div class="empty-title">RC details could not be prepared</div>
        <div class="empty-copy">{{ $vehicleSearch->error_message ?: 'No registration data was returned for this number.' }}</div>
    </div>
@endif
</body>
</html>
