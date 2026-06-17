<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle }} - {{ $vehicleNumber }} - SAHI GADI</title>
    <style>
        @page { margin: 24px 28px 38px; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #172033;
            background: #fff;
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }
        table { width: 100%; border-collapse: collapse; }

        .masthead { margin-bottom: 14px; border: 1px solid #dce6e8; background: #f7fbfb; }
        .masthead-accent { height: 5px; background: #0f766e; }
        .masthead-body { padding: 16px 18px 15px; }
        .brand-cell { width: 54%; vertical-align: middle; }
        .brand-mark {
            width: 34px; height: 34px; border-radius: 5px; background: #0f766e;
            color: #fff; font-size: 13px; font-weight: bold; text-align: center; vertical-align: middle;
        }
        .brand-copy { padding-left: 10px; vertical-align: middle; }
        .brand-name { color: #101828; font-size: 17px; font-weight: bold; line-height: 1; }
        .brand-name span { color: #f26422; }
        .brand-subtitle {
            margin-top: 5px; color: #667085; font-size: 7px; font-weight: bold;
            letter-spacing: .8px; text-transform: uppercase;
        }
        .report-heading { text-align: right; vertical-align: middle; }
        .report-kicker {
            color: #0f766e; font-size: 7px; font-weight: bold;
            letter-spacing: .8px; text-transform: uppercase;
        }
        .report-title { margin-top: 4px; color: #101828; font-size: 16px; font-weight: bold; }
        .report-meta { margin-top: 5px; color: #667085; font-size: 7.5px; }

        .vehicle-panel { margin-bottom: 12px; padding: 12px 15px; background: #101828; color: #fff; }
        .vehicle-panel td { vertical-align: middle; }
        .vehicle-label {
            color: #99e6dc; font-size: 7px; font-weight: bold;
            letter-spacing: .8px; text-transform: uppercase;
        }
        .vehicle-number { margin-top: 3px; font-size: 22px; font-weight: bold; letter-spacing: 1.2px; }
        .verified-cell { width: 36%; text-align: right; }
        .verified-badge {
            display: inline-block; padding: 6px 10px; border: 1px solid #46716d;
            border-radius: 4px; background: #183934; color: #d8fff8; font-size: 8px; font-weight: bold;
        }

        .summary-grid { margin-bottom: 13px; table-layout: fixed; }
        .summary-grid td { width: 25%; padding-right: 7px; vertical-align: top; }
        .summary-grid td:last-child { padding-right: 0; }
        .summary-card { min-height: 55px; padding: 10px 11px; border: 1px solid #dfe7ea; background: #fff; }
        .summary-label {
            color: #667085; font-size: 7px; font-weight: bold;
            letter-spacing: .6px; text-transform: uppercase;
        }
        .summary-value { margin-top: 5px; color: #101828; font-size: 14px; font-weight: bold; line-height: 1.15; }
        .summary-note { margin-top: 4px; color: #7a8799; font-size: 7px; }

        .section { margin-top: 14px; }
        .timeline-section { page-break-before: always; }
        .section-heading { margin-bottom: 7px; border-bottom: 1px solid #d7e2e5; }
        .section-heading td { padding-bottom: 5px; vertical-align: bottom; }
        .section-title { color: #101828; font-size: 11px; font-weight: bold; }
        .section-title:before {
            content: ""; display: inline-block; width: 4px; height: 11px;
            margin-right: 7px; background: #f26422; vertical-align: -2px;
        }
        .section-caption { color: #7a8799; font-size: 7px; text-align: right; }

        .profile { border: 1px solid #dfe7ea; table-layout: fixed; }
        .profile td {
            width: 25%; padding: 9px 10px; border-right: 1px solid #e5ecee;
            border-bottom: 1px solid #e5ecee; vertical-align: top;
        }
        .profile tr:last-child td { border-bottom: 0; }
        .profile td:last-child { border-right: 0; }
        .field-label {
            color: #7a8799; font-size: 6.8px; font-weight: bold;
            letter-spacing: .45px; text-transform: uppercase;
        }
        .field-value {
            margin-top: 3px; color: #25324a; font-size: 8.5px;
            font-weight: bold; word-wrap: break-word;
        }

        .service-record { margin-bottom: 9px; border: 1px solid #dbe4e7; page-break-inside: avoid; }
        .record-head { background: #f1f7f7; border-bottom: 1px solid #dbe4e7; }
        .record-head td { padding: 8px 10px; vertical-align: middle; }
        .visit-index {
            width: 16%; color: #0f766e; font-size: 8px; font-weight: bold;
            letter-spacing: .5px; text-transform: uppercase;
        }
        .visit-date { width: 32%; color: #101828; font-size: 11px; font-weight: bold; }
        .visit-dealer { color: #40506a; font-size: 8px; font-weight: bold; text-align: right; }
        .status {
            display: inline-block; margin-left: 8px; padding: 3px 7px; border-radius: 3px;
            background: #dff7f1; color: #09695e; font-size: 6.8px; font-weight: bold; text-transform: uppercase;
        }
        .record-grid { table-layout: fixed; }
        .record-grid td {
            width: 25%; padding: 7px 9px; border-right: 1px solid #edf1f2;
            border-bottom: 1px solid #edf1f2; vertical-align: top;
        }
        .record-grid tr:last-child td { border-bottom: 0; }
        .record-grid td:last-child { border-right: 0; }
        .amount { color: #e45618; }

        .empty-state { padding: 30px; border: 1px solid #f1c9b7; background: #fff8f4; text-align: center; }
        .empty-state-title { color: #9e3e14; font-size: 13px; font-weight: bold; }
        .empty-state-copy { margin-top: 7px; color: #7a4d39; font-size: 9px; }
        .notice {
            margin-top: 13px; padding: 9px 11px; border-left: 3px solid #0f766e;
            background: #f5f9fa; color: #58677d; font-size: 7px; line-height: 1.55;
        }
        .footer {
            position: fixed; right: 0; bottom: -24px; left: 0; border-top: 1px solid #dce4e7;
            padding-top: 7px; color: #7d8999; font-size: 6.8px;
        }
        .footer-right { text-align: right; }
    </style>
</head>
<body>
@php
    $timezone = 'Asia/Kolkata';
    $records = collect($records ?? []);
    $generatedAt = now($timezone);

    $parseDate = static function ($value) use ($timezone) {
        if (empty($value)) return null;
        if ($value instanceof \Carbon\CarbonInterface) return $value->copy()->timezone($timezone);

        $text = trim((string) $value);
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'd M Y'] as $dateFormat) {
            try {
                $date = \Carbon\Carbon::createFromFormat($dateFormat, $text, $timezone);
                if ($date !== false) return $date;
            } catch (\Throwable $exception) {
                //
            }
        }

        try {
            return \Carbon\Carbon::parse($text, $timezone);
        } catch (\Throwable $exception) {
            return null;
        }
    };

    $formatDate = static function ($value, string $format = 'd M Y') use ($parseDate) {
        $date = $parseDate($value);
        return $date ? $date->format($format) : (empty($value) ? 'Not available' : (string) $value);
    };

    $sortTimestamp = static function ($record) use ($parseDate) {
        return $parseDate(data_get($record, 'svc_date'))?->timestamp ?? 0;
    };

    $sortedRecords = $records->sortByDesc($sortTimestamp)->values();
    $datedRecords = $records->filter(fn ($record) => ! empty(data_get($record, 'svc_date')));
    $firstVisit = $datedRecords->sortBy($sortTimestamp)->first();
    $lastVisit = $datedRecords->sortByDesc($sortTimestamp)->first();
    $latestMileageRecord = $sortedRecords->first(fn ($record) => trim((string) data_get($record, 'mileage', '')) !== '');
    $latestMileage = data_get($latestMileageRecord, 'mileage');
    $totalBilled = $records->sum(fn ($record) => (float) (data_get($record, 'total_amount') ?? data_get($record, 'net_bill_amt') ?? 0));
    $latestChassis = data_get($sortedRecords->first(fn ($record) => ! empty(data_get($record, 'chassis_no'))), 'chassis_no');
    $reportDate = $formatDate($reportCreatedAt, 'd M Y, h:i A');
    $reference = 'SG-MSH-'.str_pad((string) ($reportId ?? 0), 6, '0', STR_PAD_LEFT);
@endphp

<div class="footer">
    <table>
        <tr>
            <td>SAHI GADI | Vehicle intelligence report | Confidential</td>
            <td class="footer-right">{{ $reference }} | Generated {{ $generatedAt->format('d M Y, h:i A') }}</td>
        </tr>
    </table>
</div>

<div class="masthead">
    <div class="masthead-accent"></div>
    <div class="masthead-body">
        <table>
            <tr>
                <td class="brand-cell">
                    <table>
                        <tr>
                            <td class="brand-mark">SG</td>
                            <td class="brand-copy">
                                <div class="brand-name">SAHI<span>GADI</span></div>
                                <div class="brand-subtitle">Verified vehicle intelligence</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="report-heading">
                    <div class="report-kicker">Workshop history dossier</div>
                    <div class="report-title">{{ $documentTitle }}</div>
                    <div class="report-meta">Reference {{ $reference }} | Report date {{ $reportDate }}</div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="vehicle-panel">
    <table>
        <tr>
            <td>
                <div class="vehicle-label">Registration number</div>
                <div class="vehicle-number">{{ strtoupper($vehicleNumber) }}</div>
            </td>
            <td class="verified-cell">
                <span class="verified-badge">{{ $records->count() }} WORKSHOP VISITS IDENTIFIED</span>
            </td>
        </tr>
    </table>
</div>

@if($isSuccess && $records->count() > 0)
    <table class="summary-grid">
        <tr>
            <td><div class="summary-card"><div class="summary-label">Recorded visits</div><div class="summary-value">{{ $records->count() }}</div><div class="summary-note">Available workshop entries</div></div></td>
            <td><div class="summary-card"><div class="summary-label">Latest visit</div><div class="summary-value">{{ $formatDate(data_get($lastVisit, 'svc_date')) }}</div><div class="summary-note">Most recent recorded service</div></div></td>
            <td><div class="summary-card"><div class="summary-label">Latest odometer</div><div class="summary-value">{{ $latestMileage !== null ? number_format((float) $latestMileage) : 'N/A' }}</div><div class="summary-note">{{ $latestMileage !== null ? 'Kilometres reported' : 'Mileage not reported' }}</div></div></td>
            <td><div class="summary-card"><div class="summary-label">Cumulative billing</div><div class="summary-value">Rs.{{ number_format($totalBilled, 2) }}</div><div class="summary-note">Across available records</div></div></td>
        </tr>
    </table>

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Report profile</td><td class="section-caption">Key identifiers and reporting context</td></tr></table>
        <table class="profile">
            <tr>
                <td><div class="field-label">Vehicle</div><div class="field-value">{{ strtoupper($vehicleNumber) }}</div></td>
                <td><div class="field-label">Chassis reference</div><div class="field-value">{{ $latestChassis ?: 'Not available' }}</div></td>
                <td><div class="field-label">History period</div><div class="field-value">{{ $formatDate(data_get($firstVisit, 'svc_date')) }} - {{ $formatDate(data_get($lastVisit, 'svc_date')) }}</div></td>
                <td><div class="field-label">{{ $subjectLabel }}</div><div class="field-value">{{ $subjectName ?: 'Not available' }}</div></td>
            </tr>
            <tr>
                <td><div class="field-label">Report generated</div><div class="field-value">{{ $reportDate }}</div></td>
                <td><div class="field-label">{{ $chargeLabel }}</div><div class="field-value">Rs.{{ number_format((float) $chargeAmount, 2) }}</div></td>
                <td><div class="field-label">Data status</div><div class="field-value">Records available</div></td>
                <td><div class="field-label">Document reference</div><div class="field-value">{{ $reference }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section timeline-section">
        <table class="section-heading"><tr><td class="section-title">Workshop visit timeline</td><td class="section-caption">Newest service visit shown first</td></tr></table>

        @foreach($sortedRecords as $record)
            @php
                $recordAmount = (float) (data_get($record, 'total_amount') ?? data_get($record, 'net_bill_amt') ?? 0);
                $partAmount = data_get($record, 'part_amount');
                $labourAmount = data_get($record, 'labour_amount');
                $paidAmount = data_get($record, 'paid_amt');
                $outstandingAmount = data_get($record, 'out_standing_amt');
                $location = data_get($record, 'dealer_address') ?? data_get($record, 'location_name');
                $recordStatus = trim((string) data_get($record, 'status', '')) ?: 'Recorded';
            @endphp
            <div class="service-record">
                <table class="record-head">
                    <tr>
                        <td class="visit-index">Visit {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="visit-date">{{ $formatDate(data_get($record, 'svc_date')) }}</td>
                        <td class="visit-dealer">{{ data_get($record, 'dealer_name') ?: 'Dealer not reported' }}<span class="status">{{ $recordStatus }}</span></td>
                    </tr>
                </table>
                <table class="record-grid">
                    <tr>
                        <td><div class="field-label">Service category</div><div class="field-value">{{ data_get($record, 'service_cate') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Work type</div><div class="field-value">{{ data_get($record, 'work_type') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Odometer</div><div class="field-value">{{ data_get($record, 'mileage') !== null ? number_format((float) data_get($record, 'mileage')).' km' : 'Not available' }}</div></td>
                        <td><div class="field-label">Total billed</div><div class="field-value amount">Rs.{{ number_format($recordAmount, 2) }}</div></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="field-label">Workshop</div><div class="field-value">{{ data_get($record, 'dealer_name') ?: 'Not available' }}</div></td>
                        <td colspan="2"><div class="field-label">Location</div><div class="field-value">{{ $location ?: 'Not available' }}</div></td>
                    </tr>
                    <tr>
                        <td><div class="field-label">Job card number</div><div class="field-value">{{ data_get($record, 'register_no') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Repair order</div><div class="field-value">{{ data_get($record, 'repair_order_no') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Bill number</div><div class="field-value">{{ data_get($record, 'repair_order_bill_no') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Bill date</div><div class="field-value">{{ $formatDate(data_get($record, 'repair_order_bill_date')) }}</div></td>
                    </tr>
                    <tr>
                        <td><div class="field-label">Service advisor</div><div class="field-value">{{ data_get($record, 'service_assistant_name') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Dealer code</div><div class="field-value">{{ data_get($record, 'dealer_code') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Location code</div><div class="field-value">{{ data_get($record, 'location_code') ?: 'Not available' }}</div></td>
                        <td><div class="field-label">Chassis reference</div><div class="field-value">{{ data_get($record, 'chassis_no') ?: 'Not available' }}</div></td>
                    </tr>
                    @if((float) $partAmount > 0 || (float) $labourAmount > 0 || (float) $paidAmount > 0 || (float) $outstandingAmount > 0)
                        <tr>
                            <td><div class="field-label">Parts amount</div><div class="field-value">Rs.{{ number_format((float) $partAmount, 2) }}</div></td>
                            <td><div class="field-label">Labour amount</div><div class="field-value">Rs.{{ number_format((float) $labourAmount, 2) }}</div></td>
                            <td><div class="field-label">Paid amount</div><div class="field-value">Rs.{{ number_format((float) $paidAmount, 2) }}</div></td>
                            <td><div class="field-label">Outstanding</div><div class="field-value">Rs.{{ number_format((float) $outstandingAmount, 2) }}</div></td>
                        </tr>
                    @endif
                </table>
            </div>
        @endforeach
    </div>

    <div class="notice">
        This report presents service-history data returned by the connected provider for the stated registration number.
        Review it with a physical inspection, ownership documents and workshop verification before a vehicle purchase decision.
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-title">Service history could not be prepared</div>
        <div class="empty-state-copy">{{ $errorMessage ?: 'No workshop records were returned for this registration number.' }}</div>
    </div>
@endif
</body>
</html>
