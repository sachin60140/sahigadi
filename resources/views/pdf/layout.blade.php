<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('doc-title', 'SAHI GADI Report')</title>
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

        .hero-panel { margin-bottom: 12px; padding: 12px 15px; background: #101828; color: #fff; }
        .hero-panel td { vertical-align: middle; }
        .hero-label {
            color: #99e6dc; font-size: 7px; font-weight: bold;
            letter-spacing: .8px; text-transform: uppercase;
        }
        .hero-value { margin-top: 3px; font-size: 22px; font-weight: bold; letter-spacing: 1.2px; }
        .hero-aside { width: 38%; text-align: right; }
        .hero-badge {
            display: inline-block; padding: 6px 10px; border: 1px solid #46716d;
            border-radius: 4px; background: #183934; color: #d8fff8; font-size: 8px; font-weight: bold;
        }
        .hero-badge.is-danger { border-color: #7c4a45; background: #3a201e; color: #ffd9d4; }

        .summary-grid { margin-bottom: 13px; table-layout: fixed; }
        .summary-grid td { padding-right: 7px; vertical-align: top; }
        .summary-grid td:last-child { padding-right: 0; }
        .summary-card { min-height: 55px; padding: 10px 11px; border: 1px solid #dfe7ea; background: #fff; }
        .summary-label {
            color: #667085; font-size: 7px; font-weight: bold;
            letter-spacing: .6px; text-transform: uppercase;
        }
        .summary-value { margin-top: 5px; color: #101828; font-size: 14px; font-weight: bold; line-height: 1.15; }
        .summary-note { margin-top: 4px; color: #7a8799; font-size: 7px; }

        .section { margin-top: 14px; }
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
            padding: 9px 10px; border-right: 1px solid #e5ecee;
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
        .amount { color: #e45618; }

        .kv-table { border: 1px solid #dfe7ea; }
        .kv-table td { padding: 7px 10px; border-bottom: 1px solid #eef2f3; vertical-align: top; }
        .kv-table tr:last-child td { border-bottom: 0; }
        .kv-table .kv-key {
            width: 38%; color: #5a6b82; font-size: 7.5px; font-weight: bold;
            letter-spacing: .4px; text-transform: uppercase; background: #f7fafb;
        }
        .kv-table .kv-val { color: #25324a; font-size: 8.5px; font-weight: bold; word-wrap: break-word; }

        .data-table { border: 1px solid #dfe7ea; }
        .data-table thead td {
            padding: 8px 9px; background: #101828; color: #cfe9e4; font-size: 7px;
            font-weight: bold; letter-spacing: .5px; text-transform: uppercase;
        }
        .data-table tbody td {
            padding: 7px 9px; border-bottom: 1px solid #eef2f3; color: #2b3a52; font-size: 8px;
        }
        .data-table tbody tr:nth-child(even) td { background: #f8fbfb; }
        .data-table .num { text-align: right; }

        .totals-box {
            margin-top: 13px; border: 1px solid #cfe0dd; background: #f3faf8;
        }
        .totals-box td { padding: 8px 12px; vertical-align: middle; }
        .totals-label { color: #5a6b82; font-size: 8px; font-weight: bold; }
        .totals-amount { text-align: right; color: #0f766e; font-size: 14px; font-weight: bold; }

        .status {
            display: inline-block; padding: 3px 7px; border-radius: 3px;
            background: #dff7f1; color: #09695e; font-size: 6.8px; font-weight: bold; text-transform: uppercase;
        }
        .status.is-danger { background: #fdeceb; color: #b13a2f; }
        .status.is-warning { background: #fdf3e0; color: #9a6612; }

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
    <div class="footer">
        <table>
            <tr>
                <td>SAHI GADI | @yield('footer-note', 'Verified vehicle marketplace') | Confidential</td>
                <td class="footer-right">@yield('reference', '')@hasSection('reference') | @endif Generated {{ now('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
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
                                    <div class="brand-subtitle">@yield('brand-subtitle', 'Verified vehicle intelligence')</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="report-heading">
                        <div class="report-kicker">@yield('report-kicker', 'Report')</div>
                        <div class="report-title">@yield('report-title', 'SAHI GADI Report')</div>
                        <div class="report-meta">@yield('report-meta', '')</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @yield('content')
</body>
</html>
