<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wallet Recharges Export - SAHI GADI</title>
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
    <?php
    date_default_timezone_set('Asia/Kolkata');
    $totalBase = 0;
    $totalGst = 0;
    $totalPaid = 0;
    ?>

    <div class="header">
        <h1>SAHI GADI - Admin Customer Wallet Recharges</h1>
        <p>Generated on: <?php echo e(date('d M Y, h:i A')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt No</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Contact</th>
                <th>GST Number</th>
                <th>Payment Details</th>
                <th class="text-right">Base (Rs)</th>
                <th class="text-right">GST 18% (Rs)</th>
                <th class="text-right">Total Paid (Rs)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                if ($txn->type === 'debit') {
                    $base = -$txn->amount;
                    $gst = 0;
                    $total = -$txn->amount;
                    $receipt = 'N/A';
                } else {
                    $base = $txn->amount;
                    $gst = $base * 0.18;
                    $total = $base + $gst;
                    $receipt = 'RCPT-' . $txn->created_at->format('Y') . '-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT);
                }
                
                $totalBase += $base;
                $totalGst += $gst;
                $totalPaid += $total;

                $customer = $txn->wallet->customer;
                if ($txn->type === 'debit') {
                    $paymentMode = 'Admin Deduction';
                    $orderId = null;
                    $txnId = $txn->remark ?? 'N/A';
                } elseif ($txn->reference_type === 'admin_credit') {
                    $paymentMode = 'Direct Deposit';
                    $orderId = null;
                    $txnId = $txn->reference_id ?? 'N/A';
                } else {
                    $paymentMode = str_starts_with($txn->reference_id, 'PP') ? 'PhonePe' : 'Razorpay';
                    $paymentRecord = \App\Models\Payment::where('razorpay_payment_id', $txn->reference_id)
                        ->orWhere('phonepe_transaction_id', $txn->reference_id)
                        ->first();
                    $orderId = $paymentRecord && $paymentRecord->razorpay_order_id ? $paymentRecord->razorpay_order_id : ($paymentMode === 'PhonePe' ? $txn->reference_id : null);
                    $txnId = $paymentMode === 'PhonePe' ? ($paymentRecord && $paymentRecord->reference_id ? $paymentRecord->reference_id : 'Pending Sync') : ($txn->reference_id ?? 'N/A');
                }
            ?>
            <tr>
                <td><?php echo e($txn->created_at->format('d M Y, H:i')); ?></td>
                <td><?php echo e($receipt); ?></td>
                <td><?php echo e($customer->company_name ?? 'N/A'); ?></td>
                <td><?php echo e($customer->name ?? 'N/A'); ?></td>
                <td><?php echo e($customer->email ?? ''); ?><br><small style="color:#666"><?php echo e($customer->phone ?? ''); ?></small></td>
                <td><?php echo e($customer->gst_number ?? 'N/A'); ?></td>
                <td>
                    <?php echo e($paymentMode); ?><br>
                    <?php if($orderId): ?>
                        <small style="color:#666">Ord: <?php echo e($orderId); ?></small><br>
                    <?php elseif($txn->type !== 'debit'): ?>
                        <small style="color:#666">Ord: N/A</small><br>
                    <?php endif; ?>
                    <small style="color:#666"><?php echo e($txn->type === 'debit' ? 'Rmk: ' : 'Txn: '); ?><?php echo e($txnId); ?></small>
                </td>
                <td class="text-right"><?php echo e(number_format($base, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($gst, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($total, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="10" style="text-align:center">No recharges found for the specified period.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <?php if($transactions->isNotEmpty()): ?>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right font-weight-bold">Grand Total:</td>
                <td class="text-right font-weight-bold"><?php echo e(number_format($totalBase, 2)); ?></td>
                <td class="text-right font-weight-bold"><?php echo e(number_format($totalGst, 2)); ?></td>
                <td class="text-right font-weight-bold"><?php echo e(number_format($totalPaid, 2)); ?></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <div class="footer">
        SAHI GADI - Confidential Document | Admin Trace Export
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\customer-wallet-recharges\pdf.blade.php ENDPATH**/ ?>