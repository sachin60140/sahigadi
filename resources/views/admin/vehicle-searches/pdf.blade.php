<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RC Search Reports - SAHIGADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a1a2e; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1a1a2e; }
        .header p { margin: 5px 0; color: #666; }
        .summary { margin-top: 20px; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHIGADI - RC Search Reports</h1>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
        <p>Total Revenue: Rs. {{ number_format($totalRevenue, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reg Number</th>
                <th>Owner</th>
                <th>Dealer</th>
                <th>Vehicle</th>
                <th>RC Status</th>
                <th>Charge</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($searches as $search)
            <tr>
                <td><strong>{{ $search->registration_number }}</strong></td>
                <td>{{ $search->owner_name ?? 'N/A' }}</td>
                <td>{{ $search->dealer->name ?? 'N/A' }}</td>
                <td>{{ ($search->make ?? '') . ' ' . ($search->model ?? '') }}</td>
                <td>
                    @if($search->is_success)
                        <span class="badge badge-success">{{ $search->rc_status ?? 'Active' }}</span>
                    @else
                        <span class="badge badge-danger">Failed</span>
                    @endif
                </td>
                <td>Rs. {{ number_format($search->charge_amount, 2) }}</td>
                <td>{{ $search->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>SAHIGADI - Vehicle Marketplace | This is a system generated report</p>
    </div>
</body>
</html>
