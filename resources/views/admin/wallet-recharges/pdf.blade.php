<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wallet Recharges Export - SAHIGADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a1a2e; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #1a1a2e; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f0f2f5; font-weight: bold; color: #1a1a2e; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    @php
    date_default_timezone_set('Asia/Kolkata');
    $totalBase = 0;
    $totalGst = 0;
    $totalPaid = 0;
    @endphp

    <div class="header">
        <h1>SAHIGADI - Admin Wallet Recharges</h1>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt No</th>
                <th>Company Name</th>
                <th>Dealer Name</th>
                <th>Contact</th>
                <th>GST Number</th>
                <th>Payment Mode</th>
                <th class="text-right">Base (Rs)</th>
                <th class="text-right">GST 18% (Rs)</th>
                <th class="text-right">Total Paid (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
            @php
                $base = $txn->amount;
                $gst = $base * 0.18;
                $total = $base + $gst;
                
                $totalBase += $base;
                $totalGst += $gst;
                $totalPaid += $total;

                $receipt = 'RCPT-' . $txn->created_at->format('Y') . '-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT);
                $dealer = $txn->wallet->dealer;
                $paymentMode = $txn->reference_type === 'admin_credit' ? 'Direct Deposit' : 'Razorpay';
            @endphp
            <tr>
                <td>{{ $txn->created_at->format('d M Y, H:i') }}</td>
                <td>{{ $receipt }}</td>
                <td>{{ $dealer->company_name ?? 'N/A' }}</td>
                <td>{{ $dealer->name ?? 'N/A' }}</td>
                <td>{{ $dealer->email ?? '' }}<br><small style="color:#666">{{ $dealer->phone ?? '' }}</small></td>
                <td>{{ $dealer->gst_number ?? 'N/A' }}</td>
                <td>{{ $paymentMode }}<br><small style="color:#666">{{ $txn->reference_id ?? '' }}</small></td>
                <td class="text-right">{{ number_format($base, 2) }}</td>
                <td class="text-right">{{ number_format($gst, 2) }}</td>
                <td class="text-right">{{ number_format($total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center">No recharges found for the specified period.</td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="7" class="text-right font-weight-bold">Grand Total:</td>
                <td class="text-right font-weight-bold">{{ number_format($totalBase, 2) }}</td>
                <td class="text-right font-weight-bold">{{ number_format($totalGst, 2) }}</td>
                <td class="text-right font-weight-bold">{{ number_format($totalPaid, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        SAHIGADI - Confidential Document | Admin Trace Export
    </div>
</body>
</html>
