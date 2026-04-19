<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>E-Challan Report - <?php echo e($challanSearch->vehicle_number); ?> - SAHIGADI</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header { background: #1a1a2e; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header span { color: #e94560; }
        .header p { margin: 5px 0 0; font-size: 11px; color: #ccc; }
        .vehicle-box { background: #2d2d4a; padding: 15px; margin-top: 15px; display: inline-block; border: 2px solid #e94560; border-radius: 5px; }
        .vehicle-box .label { font-size: 9px; color: #aaa; text-transform: uppercase; }
        .vehicle-box .number { font-size: 18px; font-weight: bold; color: #fff; }
        .stats { background: #f5f5f5; padding: 15px; display: table; width: 100%; }
        .stat { display: table-cell; padding: 10px; text-align: center; border: 1px solid #ddd; background: #fff; margin: 2px; }
        .stat h3 { font-size: 14px; margin: 0; }
        .stat.danger h3 { color: #dc3545; }
        .stat.success h3 { color: #28a745; }
        .stat.warning h3 { color: #d97706; }
        .stat.primary h3 { color: #2563eb; }
        .stat p { margin: 3px 0 0; font-size: 8px; color: #666; text-transform: uppercase; }
        .court-stats { background: #fff; padding: 10px; display: table; width: 100%; border-bottom: 1px solid #ddd; }
        .court { display: table-cell; padding: 8px; text-align: center; border: 1px solid #eee; background: #fafafa; margin: 2px; }
        .court h4 { font-size: 12px; margin: 0; }
        .court p { margin: 2px 0 0; font-size: 8px; color: #666; }
        .section { margin: 15px; }
        .section-title { background: #1a1a2e; color: white; padding: 10px; font-weight: bold; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f0f0f0; padding: 8px; text-align: left; font-size: 9px; border-bottom: 1px solid #ccc; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 9px; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-unpaid { background: #f8d7da; color: #721c24; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-physical { background: #e2e3e5; color: #383d41; }
        .badge-virtual { background: #d1ecf1; color: #0c5460; }
        .amount-paid { color: #28a745; font-weight: bold; }
        .amount-unpaid { color: #dc3545; font-weight: bold; }
        .amount-pending { color: #d97706; font-weight: bold; }
        .summary { width: 100%; max-width: 400px; margin: 0 auto; border: 1px solid #ddd; }
        .summary td { padding: 8px; border-bottom: 1px solid #eee; }
        .summary .label { color: #666; }
        .summary .value { text-align: right; font-weight: bold; }
        .footer { background: #f5f5f5; padding: 10px; text-align: center; font-size: 9px; color: #999; margin-top: 20px; }
    </style>
</head>
<body>
    <?php
    date_default_timezone_set('Asia/Kolkata');
    $challans = $challanSearch->challan_data ?? [];
    $paidAmount = 0;
    $unpaidAmount = 0;
    $pendingAmount = 0;
    $physicalCourt = 0;
    $virtualCourt = 0;
    $noCourt = 0;
    
    usort($challans, function ($a, $b) {
        $dateA = isset($a['dateChallan']) ? strtotime($a['dateChallan']) : 0;
        $dateB = isset($b['dateChallan']) ? strtotime($b['dateChallan']) : 0;
        return $dateB - $dateA;
    });
    
    foreach ($challans as $c) {
        $amount = floatval($c['amountChallan'] ?? 0);
        $status = strtolower($c['status'] ?? '');
        
        if ($status == 'paid') {
            $paidAmount += $amount;
        } elseif ($status == 'pending') {
            $pendingAmount += $amount;
        } else {
            $unpaidAmount += $amount;
        }
        
        $courtStatus = strtolower($c['court_status_desc'] ?? '');
        if ($courtStatus == 'physical court') {
            $physicalCourt++;
        } elseif ($courtStatus == 'virtual court') {
            $virtualCourt++;
        } else {
            $noCourt++;
        }
    }
    ?>

    <div class="header">
        <h1>SAHI<span>GADI</span></h1>
        <p>E-Challan Search Report</p>
        <div class="vehicle-box">
            <div class="label">Vehicle Number</div>
            <div class="number"><?php echo e($challanSearch->vehicle_number); ?></div>
        </div>
    </div>

    <div class="stats">
        <div class="stat danger">
            <h3>Rs.<?php echo e(number_format($unpaidAmount + $pendingAmount)); ?></h3>
            <p>Pending Amount</p>
        </div>
        <div class="stat success">
            <h3>Rs.<?php echo e(number_format($paidAmount)); ?></h3>
            <p>Paid Amount</p>
        </div>
        <div class="stat warning">
            <h3>Rs.<?php echo e(number_format($unpaidAmount)); ?></h3>
            <p>Unpaid Amount</p>
        </div>
        <div class="stat primary">
            <h3><?php echo e(count($challans)); ?></h3>
            <p>Total Challans</p>
        </div>
    </div>

    <?php if($physicalCourt > 0 || $virtualCourt > 0 || $noCourt > 0): ?>
    <div class="court-stats">
        <div class="court">
            <h4><?php echo e($physicalCourt); ?></h4>
            <p>Physical Court</p>
        </div>
        <div class="court">
            <h4><?php echo e($virtualCourt); ?></h4>
            <p>Virtual Court</p>
        </div>
        <div class="court">
            <h4><?php echo e($noCourt); ?></h4>
            <p>No Court Data</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($challanSearch->is_success && $challanSearch->challan_data): ?>
    <div class="section">
        <div class="section-title">Challan Details (Sorted by Date - Newest First)</div>
        <table>
            <thead>
                <tr>
                    <th>Challan No</th>
                    <th>Date</th>
                    <th>State</th>
                    <th>Offence</th>
                    <th>RTO</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Court</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $challans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $status = strtolower($c['status'] ?? '');
                    $isPaid = ($status == 'paid');
                    $isPending = ($status == 'pending');
                ?>
                <tr>
                    <td><?php echo e($c['challanNo'] ?? 'N/A'); ?></td>
                    <td><?php echo e(isset($c['dateChallan']) ? date('d M Y', strtotime($c['dateChallan'])) : 'N/A'); ?></td>
                    <td><?php echo e($c['State'] ?? 'N/A'); ?></td>
                    <td><?php echo e($c['detailsViolation'][0]['offence'] ?? 'N/A'); ?></td>
                    <td><?php echo e($c['nameRTO'] ?? 'N/A'); ?></td>
                    <td class="<?php echo e($isPaid ? 'amount-paid' : ($isPending ? 'amount-pending' : 'amount-unpaid')); ?>">Rs.<?php echo e(number_format($c['amountChallan'] ?? 0)); ?></td>
                    <td>
                        <?php if($isPaid): ?>
                            <span class="badge badge-paid">Paid</span>
                        <?php elseif($isPending): ?>
                            <span class="badge badge-pending">Pending</span>
                        <?php else: ?>
                            <span class="badge badge-unpaid">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $courtStatus = $c['court_status_desc'] ?? ''; ?>
                        <?php if($courtStatus): ?>
                            <?php if(strtolower($courtStatus) == 'physical court'): ?>
                                <span class="badge badge-physical">Physical Court</span>
                            <?php elseif(strtolower($courtStatus) == 'virtual court'): ?>
                                <span class="badge badge-virtual">Virtual Court</span>
                            <?php else: ?>
                                <?php echo e($courtStatus); ?>

                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Summary</div>
        <table class="summary">
            <tr><td class="label">Total Challans</td><td class="value"><?php echo e(count($challans)); ?></td></tr>
            <tr><td class="label">Total Amount</td><td class="value">Rs.<?php echo e(number_format($challanSearch->total_amount ?? 0)); ?></td></tr>
            <tr><td class="label">Paid Amount</td><td class="value amount-paid">Rs.<?php echo e(number_format($paidAmount)); ?></td></tr>
            <tr><td class="label">Unpaid Amount</td><td class="value amount-unpaid">Rs.<?php echo e(number_format($unpaidAmount)); ?></td></tr>
            <tr><td class="label">Pending Amount</td><td class="value amount-pending">Rs.<?php echo e(number_format($pendingAmount)); ?></td></tr>
            <tr><td class="label">Physical Court</td><td class="value"><?php echo e($physicalCourt); ?></td></tr>
            <tr><td class="label">Virtual Court</td><td class="value"><?php echo e($virtualCourt); ?></td></tr>
            <tr><td class="label">Search Date</td><td class="value"><?php echo e($challanSearch->created_at->setTimezone('Asia/Kolkata')->format('d M Y, h:i A')); ?></td></tr>
            <tr><td class="label">Service Charge</td><td class="value">Rs.<?php echo e(number_format($challanSearch->charge_amount, 2)); ?></td></tr>
        </table>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="section-title">Result</div>
        <p style="padding: 20px; text-align: center; color: #666;">
            <?php echo e($challanSearch->error_message ?? 'No challans found for this vehicle'); ?>

        </p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <strong>SAHIGADI</strong> - Vehicle Marketplace | Generated on: <?php echo e(date('d M Y, h:i A')); ?>

    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/challan-searches/pdf.blade.php ENDPATH**/ ?>