<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RC Details - {{ $vehicleSearch->registration_number }} - SAHI GADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding: 20px; background: #1a1a2e; color: white; }
        .header h1 { margin: 0; }
        .reg-number { font-size: 24px; font-weight: bold; }
        .section { margin: 20px 0; }
        .section-title { background: #e94560; color: white; padding: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { border: 1px solid #ddd; padding: 8px; }
        .label { font-weight: bold; background: #f8f9fa; width: 40%; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        .badge { padding: 3px 10px; border-radius: 3px; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHI GADI - RC Details</h1>
        <div class="reg-number">{{ $vehicleSearch->registration_number }}</div>
        @if($vehicleSearch->is_success)
            <span class="badge badge-success">Verified</span>
        @else
            <span class="badge badge-danger">Failed</span>
        @endif
    </div>

    @if($vehicleSearch->is_success && $dealerSearch)
    <div class="section">
        <div class="section-title">Owner Details</div>
        <table>
            <tr><td class="label">Owner Name</td><td>{{ $dealerSearch->owner_name ?? 'N/A' }}</td></tr>
            <tr><td class="label">Father's Name</td><td>{{ $dealerSearch->father_name ?? 'N/A' }}</td></tr>
            <tr><td class="label">Address</td><td>{{ $dealerSearch->address ?? 'N/A' }}</td></tr>
            <tr><td class="label">Mobile Number</td><td>{{ $dealerSearch->mobile_number ?? 'N/A' }}</td></tr>
            <tr><td class="label">RTO Location</td><td>{{ $dealerSearch->rto_location ?? 'N/A' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Vehicle Details</div>
        <table>
            <tr><td class="label">Category</td><td>{{ $dealerSearch->vehicle_category ?? 'N/A' }}</td></tr>
            <tr><td class="label">Make</td><td>{{ $dealerSearch->make ?? 'N/A' }}</td></tr>
            <tr><td class="label">Model</td><td>{{ $dealerSearch->model ?? 'N/A' }}</td></tr>
            <tr><td class="label">Color</td><td>{{ $dealerSearch->color ?? 'N/A' }}</td></tr>
            <tr><td class="label">Fuel Type</td><td>{{ $dealerSearch->fuel_type ?? 'N/A' }}</td></tr>
            <tr><td class="label">Seating Capacity</td><td>{{ $dealerSearch->seats ?? 'N/A' }}</td></tr>
            <tr><td class="label">RC Status</td><td>{{ $dealerSearch->rc_status ?? 'N/A' }}</td></tr>
            <tr><td class="label">Engine Number</td><td>{{ $dealerSearch->engine_number ?? 'N/A' }}</td></tr>
            <tr><td class="label">Chassis Number</td><td>{{ $dealerSearch->chassis_number ?? 'N/A' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Documents & Status</div>
        <table>
            <tr><td class="label">Insurance Provider</td><td>{{ $dealerSearch->insurance_provider ?? 'N/A' }}</td></tr>
            <tr><td class="label">Insurance Valid Till</td><td>{{ $dealerSearch->insurance_date ?? 'N/A' }}</td></tr>
            <tr><td class="label">Fitness Valid Till</td><td>{{ $dealerSearch->fitness_date ?? 'N/A' }}</td></tr>
            <tr><td class="label">PUC Valid Till</td><td>{{ $dealerSearch->puc_validity ?? 'N/A' }}</td></tr>
            <tr><td class="label">Blacklist Status</td><td>{{ $dealerSearch->blacklist_status ?? 'Clean' }}</td></tr>
            <tr><td class="label">Financed</td><td>{{ $dealerSearch->financed ? 'Yes - ' . $dealerSearch->lender_name : 'No' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Search Summary</div>
        <table>
            <tr><td class="label">Dealer</td><td>{{ $vehicleSearch->dealer->name ?? 'N/A' }}</td></tr>
            <tr><td class="label">Search Date</td><td>{{ $vehicleSearch->created_at->format('d M Y, h:i A') }}</td></tr>
            <tr><td class="label">Amount Charged</td><td>Rs. {{ number_format($vehicleSearch->charge_amount, 2) }}</td></tr>
        </table>
    </div>
    @else
    <div class="section">
        <div class="section-title">Error</div>
        <p>{{ $vehicleSearch->error_message ?? 'Search failed' }}</p>
    </div>
    @endif

    <div class="footer">
        <p>SAHI GADI - Vehicle Marketplace | Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
