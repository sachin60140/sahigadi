<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mahindra Service History - {{ $mahindraServiceHistory->vehicle_number }} - SAHI GADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; padding: 20px; background: #1a1a2e; color: white; }
        .header h1 { margin: 0; }
        .vehicle-number { font-size: 24px; font-weight: bold; }
        .section { margin: 20px 0; }
        .section-title { background: #0d6efd; color: white; padding: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; }
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
        <h1>SAHI GADI - Mahindra Service History</h1>
        <div class="vehicle-number">{{ $mahindraServiceHistory->vehicle_number }}</div>
        <span class="badge badge-success">{{ $mahindraServiceHistory->records->count() }} Services Found</span>
    </div>

    @if($mahindraServiceHistory->is_success && $mahindraServiceHistory->records->count() > 0)
    <div class="section">
        <div class="section-title">Service Records</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Work Type</th>
                    <th>Dealer Name</th>
                    <th>Location</th>
                    <th>Job Card No</th>
                    <th>RO No</th>
                    <th>Bill No</th>
                    <th>Bill Date</th>
                    <th>Assistant</th>
                    <th>Total Amt</th>
                    <th>Status</th>
                    <th>Mileage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahindraServiceHistory->records as $record)
                <tr>
                    <td>{{ $record->svc_date ? $record->svc_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $record->service_cate ?? 'N/A' }}</td>
                    <td>{{ $record->work_type ?? 'N/A' }}</td>
                    <td>{{ $record->dealer_name ?? 'N/A' }}</td>
                    <td>{{ $record->dealer_address ?? 'N/A' }}</td>
                    <td>{{ $record->register_no ?? 'N/A' }}</td>
                    <td>{{ $record->repair_order_no ?? 'N/A' }}</td>
                    <td>{{ $record->repair_order_bill_no ?? 'N/A' }}</td>
                    <td>{{ $record->repair_order_bill_date ? $record->repair_order_bill_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $record->service_assistant_name ?? 'N/A' }}</td>
                    <td>Rs.{{ number_format($record->total_amount ?? 0, 2) }}</td>
                    <td>{{ $record->status ?? 'N/A' }}</td>
                    <td>{{ $record->mileage ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Summary</div>
        <table>
            <tr><td><strong>Total Services</strong></td><td>{{ $mahindraServiceHistory->records->count() }}</td></tr>
            <tr><td><strong>Search Date</strong></td><td>{{ $mahindraServiceHistory->created_at->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td></tr>
            <tr><td><strong>Amount Paid</strong></td><td>Rs.{{ number_format($mahindraServiceHistory->paid_amount ?? 0, 2) }}</td></tr>
        </table>
    </div>
    @else
    <div class="section">
        <p><strong>Error:</strong> {{ $mahindraServiceHistory->error_message ?? 'Search failed' }}</p>
    </div>
    @endif

    <div class="footer">
        <p>SAHI GADI - Vehicle Marketplace | Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
