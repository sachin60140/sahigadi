<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>E-Challan Reports - SAHIGADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a1a2e; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1a1a2e; }
        .header p { margin: 5px 0; color: #666; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHIGADI - E-Challan Reports</h1>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
        <p>Total Revenue: Rs. {{ number_format($totalRevenue, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vehicle Number</th>
                <th>Dealer</th>
                <th>Challans</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($searches as $search)
            <tr>
                <td><strong>{{ $search->vehicle_number }}</strong></td>
                <td>{{ $search->dealer->name ?? 'N/A' }}</td>
                <td>{{ $search->challan_count ?? 0 }}</td>
                <td>Rs. {{ number_format($search->total_amount ?? 0, 2) }}</td>
                <td>{{ $search->is_success ? 'Success' : 'Failed' }}</td>
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