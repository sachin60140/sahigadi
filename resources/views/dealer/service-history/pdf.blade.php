<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service History - {{ $serviceHistory->vehicle_number }} - SAHIGADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding: 20px; background: #1a1a2e; color: white; }
        .header h1 { margin: 0; }
        .vehicle-number { font-size: 24px; font-weight: bold; }
        .section { margin: 20px 0; }
        .section-title { background: #0d6efd; color: white; padding: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 3px 10px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #28a745; color: white; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    @php
    date_default_timezone_set('Asia/Kolkata');
    @endphp
    <div class="header">
        <h1>SAHIGADI - Service History</h1>
        <div class="vehicle-number">{{ $serviceHistory->vehicle_number }}</div>
        <span class="badge badge-success">{{ $serviceHistory->records->count() }} Services Found</span>
    </div>

    @if($serviceHistory->is_success && $serviceHistory->records->count() > 0)
    <div class="section">
        <div class="section-title">Service Records</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Service Type</th>
                    <th>Dealer</th>
                    <th>Job Card No</th>
                    <th>RO No</th>
                    <th>Part Amt</th>
                    <th>Labour Amt</th>
                    <th>Total Amt</th>
                    <th>Mileage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceHistory->records as $record)
                <tr>
                    <td>{{ $record->svc_date ?? 'N/A' }}</td>
                    <td>{{ $record->service_cate ?? 'N/A' }}</td>
                    <td>{{ $record->dealer_name ?? 'N/A' }}</td>
                    <td>{{ $record->register_no ?? 'N/A' }}</td>
                    <td>{{ $record->repair_order_no ?? 'N/A' }}</td>
                    <td>Rs.{{ number_format($record->part_amount ?? 0, 2) }}</td>
                    <td>Rs.{{ number_format($record->labour_amount ?? 0, 2) }}</td>
                    <td>Rs.{{ number_format($record->net_bill_amt ?? 0, 2) }}</td>
                    <td>{{ $record->mileage ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Summary</div>
        <table>
            <tr><td><strong>Total Services</strong></td><td>{{ $serviceHistory->records->count() }}</td></tr>
            <tr><td><strong>Search Date</strong></td><td>{{ $serviceHistory->created_at->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td></tr>
            <tr><td><strong>Amount Charged</strong></td><td>Rs.{{ number_format($serviceHistory->debit_amount, 2) }}</td></tr>
        </table>
    </div>
    @else
    <div class="section">
        <p><strong>Error:</strong> {{ $serviceHistory->error_message ?? 'Search failed' }}</p>
    </div>
    @endif

    <div class="footer">
        <p>SAHIGADI - Vehicle Marketplace | Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
