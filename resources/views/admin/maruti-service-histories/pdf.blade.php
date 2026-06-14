<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Maruti Service History Reports - SAHI GADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a1a2e; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1a1a2e; }
        .header p { margin: 5px 0; color: #666; }
        .success { color: #16803a; font-weight: bold; }
        .failed { color: #c62828; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHI GADI - Maruti Service History Reports</h1>
        <p>Generated on: {{ now()->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') }}</p>
        <p>Total searches: {{ $searches->count() }} | Revenue: Rs. {{ number_format($totalRevenue, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vehicle Number</th>
                <th>Dealer</th>
                <th>Services Found</th>
                <th>Charge</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($searches as $search)
                <tr>
                    <td><strong>{{ $search->vehicle_number }}</strong></td>
                    <td>{{ $search->dealer->name ?? 'N/A' }}</td>
                    <td>{{ $search->service_count ?? 0 }}</td>
                    <td>Rs. {{ number_format($search->charge_amount ?? 0, 2) }}</td>
                    <td class="{{ $search->is_success ? 'success' : 'failed' }}">
                        {{ $search->is_success ? 'Success' : 'Failed' }}
                    </td>
                    <td>{{ optional($search->created_at)->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No service history searches matched the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        SAHI GADI - System generated report
    </div>
</body>
</html>
