<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer RC Searches</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; padding: 10px; background: #1a1a2e; color: white; }
        .header h1 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        .success { color: green; }
        .failed { color: red; }
    </style>
</head>
<body>
    @php date_default_timezone_set('Asia/Kolkata'); @endphp
    <div class="header">
        <h1>SAHI GADI - Customer RC Searches</h1>
        <p>Total Searches: {{ $searches->count() }} | Total Revenue: Rs.{{ number_format($totalRevenue) }}</p>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Registration Number</th>
                <th>Charge Paid</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($searches as $search)
            <tr>
                <td>{{ $search->id }}</td>
                <td>{{ $search->customer_name ?? 'N/A' }}</td>
                <td>{{ $search->customer_phone ?? 'N/A' }}</td>
                <td>{{ $search->registration_number }}</td>
                <td>Rs.{{ number_format($search->paid_amount ?? 0) }}</td>
                <td class="{{ $search->is_success ? 'success' : 'failed' }}">
                    {{ $search->is_success ? 'Success' : 'Failed' }}
                </td>
                <td>{{ $search->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>SAHI GADI Admin Panel</p>
    </div>
</body>
</html>
