<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wallet Recharge Receipt - {{ $transaction->id }} - SAHI GADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5; color: #333; }
        .header { border-bottom: 2px solid #1a1a2e; padding-bottom: 20px; margin-bottom: 30px; display: table; width: 100%; }
        .company-info { display: table-cell; width: 50%; vertical-align: top; }
        .receipt-info { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        h1 { margin: 0; color: #1a1a2e; font-size: 28px; }
        h2 { margin: 0 0 10px 0; font-size: 18px; color: #666; }
        .brand-name { font-size: 20px; font-weight: bold; color: #e94560; margin-bottom: 5px; }
        .company-name { font-weight: bold; }
        .address { font-size: 12px; color: #555; max-width: 250px; }
        
        .section { margin-bottom: 30px; }
        .section-title { background: #f0f2f5; padding: 10px; font-weight: bold; border-left: 4px solid #1a1a2e; margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        
        .totals-table { width: 50%; float: right; margin-top: 20px; }
        .totals-table th, .totals-table td { border: none; padding: 8px 12px; }
        .totals-table tr.total-row { font-weight: bold; font-size: 18px; border-top: 2px solid #333; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    @php
    date_default_timezone_set('Asia/Kolkata');
    @endphp
    
    <div class="header">
        <div class="company-info">
            <div class="brand-name">SAHI GADI</div>
            <div class="company-name">Awani Enterprises</div>
            <div class="address">
                UGF-4-5, Parsvanath Majestic Arcade<br>
                Vaibhav Khand, Indirapuram<br>
                Ghaziabad, UP. - 201014<br><br>
                <strong>GST Number:</strong> 09CEKPS2342H1Z8<br>
                <strong>WhatsApp:</strong> 9811588801<br>
                <strong>Email:</strong> support@sahigadi.com
            </div>
        </div>
        <div class="receipt-info">
            <h1>MONEY RECEIPT</h1>
            <p>
                <strong>Receipt No:</strong> RCPT-{{ date('Y') }}-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}<br>
                <strong>Date:</strong> {{ $date }}<br>
                <strong>Txn Ref:</strong> {{ $transaction->reference_id ?? 'N/A' }}
            </p>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="section">
        <div class="section-title">Billed To</div>
        <p>
            <strong>Dealer ID:</strong> {{ $dealer->dealer_unique_id ?? 'N/A' }}<br>
            <strong>Dealer Name:</strong> {{ $dealer->name ?? 'N/A' }}<br>
            <strong>Firm Name:</strong> {{ $dealer->company_name ?? $dealer->firm_name ?? 'N/A' }}<br>
            <strong>Address:</strong> {{ $dealer->address ?? 'N/A' }}<br>
            <strong>City:</strong> {{ $dealer->city ?? 'N/A' }}<br>
            <strong>State:</strong> {{ $dealer->state ?? 'N/A' }}<br>
            <strong>Pincode:</strong> {{ $dealer->pincode ?? 'N/A' }}<br>
            <strong>Phone:</strong> {{ $dealer->phone }}<br>
             <strong>Email:</strong> {{ $dealer->email }}<br>
            <strong>GST Number:</strong> {{ $dealer->gst_number ?? 'N/A' }}
        </p>
    </div>

    <div class="section clearfix">
        <div class="section-title">Transaction Details</div>
        <table>
            <thead>
                <tr>
                    <th>Reference No</th>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (INR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $transaction->reference_id ?? 'N/A' }}</td>
                    <td>Dealer Wallet Recharge</td>
                    <td style="text-align: right;">{{ number_format($baseAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td style="text-align: right;">Base Amount:</td>
                <td style="text-align: right;">Rs. {{ number_format($baseAmount, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">GST (18%):</td>
                <td style="text-align: right;">Rs. {{ number_format($gstAmount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td style="text-align: right;">Total Paid:</td>
                <td style="text-align: right;">Rs. {{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt and does not require a physical signature.</p>
        <p>Thank you for your business with SAHI GADI!</p>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
