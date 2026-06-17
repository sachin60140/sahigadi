@extends('pdf.layout')

@section('doc-title', 'Customer Wallet Recharges Export - SAHI GADI')
@section('brand-subtitle', 'Admin operations')
@section('report-kicker', 'Admin export')
@section('report-title', 'Customer Wallet Recharges')
@section('report-meta', 'Generated '.now('Asia/Kolkata')->format('d M Y, h:i A'))
@section('footer-note', 'Admin trace export')

@section('content')
    @php
        $totalBase = 0; $totalGst = 0; $totalPaid = 0;
    @endphp

    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Customer wallet ledger</td><td class="section-caption">{{ $transactions->count() }} entries</td></tr></table>
        <table class="data-table">
            <thead>
                <tr>
                    <td>Date</td><td>Receipt no</td><td>Company</td><td>Customer</td><td>Contact</td>
                    <td>GST number</td><td>Payment details</td>
                    <td class="num">Base</td><td class="num">GST 18%</td><td class="num">Total</td>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                    @php
                        if ($txn->type === 'debit') {
                            $base = -$txn->amount; $gst = 0; $total = -$txn->amount; $receipt = 'N/A';
                        } else {
                            $base = $txn->amount; $gst = $base * 0.18; $total = $base + $gst;
                            $receipt = 'RCPT-'.$txn->created_at->format('Y').'-'.str_pad($txn->id, 5, '0', STR_PAD_LEFT);
                        }
                        $totalBase += $base; $totalGst += $gst; $totalPaid += $total;
                        $customer = $txn->wallet->customer;
                        if ($txn->type === 'debit') {
                            $paymentMode = 'Admin Deduction'; $orderId = null; $txnId = $txn->remark ?? 'N/A';
                        } elseif ($txn->reference_type === 'admin_credit') {
                            $paymentMode = 'Direct Deposit'; $orderId = null; $txnId = $txn->reference_id ?? 'N/A';
                        } else {
                            $paymentMode = str_starts_with((string) $txn->reference_id, 'PP') ? 'PhonePe' : 'Razorpay';
                            $paymentRecord = \App\Models\Payment::where('razorpay_payment_id', $txn->reference_id)
                                ->orWhere('phonepe_transaction_id', $txn->reference_id)->first();
                            $orderId = $paymentRecord && $paymentRecord->razorpay_order_id ? $paymentRecord->razorpay_order_id : ($paymentMode === 'PhonePe' ? $txn->reference_id : null);
                            $txnId = $paymentMode === 'PhonePe' ? ($paymentRecord && $paymentRecord->reference_id ? $paymentRecord->reference_id : 'Pending Sync') : ($txn->reference_id ?? 'N/A');
                        }
                    @endphp
                    <tr>
                        <td>{{ $txn->created_at->format('d M Y, H:i') }}</td>
                        <td>{{ $receipt }}</td>
                        <td>{{ $customer->company_name ?? 'N/A' }}</td>
                        <td>{{ $customer->name ?? 'N/A' }}</td>
                        <td>{{ $customer->email ?? '' }}<br><span style="color:#7a8799">{{ $customer->phone ?? '' }}</span></td>
                        <td>{{ $customer->gst_number ?? 'N/A' }}</td>
                        <td>{{ $paymentMode }}<br><span style="color:#7a8799">{{ $orderId ? 'Ord: '.$orderId : ($txn->type !== 'debit' ? 'Ord: N/A' : '') }}</span><br><span style="color:#7a8799">{{ $txn->type === 'debit' ? 'Rmk: ' : 'Txn: ' }}{{ $txnId }}</span></td>
                        <td class="num">{{ number_format($base, 2) }}</td>
                        <td class="num">{{ number_format($gst, 2) }}</td>
                        <td class="num">{{ number_format($total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" style="text-align: center; color: #7a8799;">No recharges found for the specified period.</td></tr>
                @endforelse
            </tbody>
            @if($transactions->isNotEmpty())
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right; font-weight: bold; background: #f1f7f7;">Grand total</td>
                        <td class="num" style="font-weight: bold; background: #f1f7f7;">{{ number_format($totalBase, 2) }}</td>
                        <td class="num" style="font-weight: bold; background: #f1f7f7;">{{ number_format($totalGst, 2) }}</td>
                        <td class="num" style="font-weight: bold; background: #f1f7f7; color: #0f766e;">{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endsection
