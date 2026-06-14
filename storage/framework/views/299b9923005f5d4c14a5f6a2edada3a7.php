<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service History - <?php echo e($serviceHistory->vehicle_number); ?> - SAHI GADI</title>
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
        .label { font-weight: bold; background: #f8f9fa; width: 40%; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        .badge { padding: 3px 10px; border-radius: 3px; }
        .badge-success { background: #28a745; color: white; }
    </style>
</head>
<body>
    <?php
    date_default_timezone_set('Asia/Kolkata');
    ?>
    <div class="header">
        <h1>SAHI GADI - Service History</h1>
        <div class="vehicle-number"><?php echo e($serviceHistory->vehicle_number); ?></div>
        <span class="badge badge-success"><?php echo e($serviceHistory->service_count ?? 0); ?> Services Found</span>
    </div>

    <?php if($serviceHistory->is_success && $dealerSearch): ?>
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
                <?php $__currentLoopData = $dealerSearch->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($record->svc_date ?? 'N/A'); ?></td>
                    <td><?php echo e($record->service_cate ?? 'N/A'); ?></td>
                    <td><?php echo e($record->dealer_name ?? 'N/A'); ?></td>
                    <td><?php echo e($record->register_no ?? 'N/A'); ?></td>
                    <td><?php echo e($record->repair_order_no ?? 'N/A'); ?></td>
                    <td>Rs.<?php echo e(number_format($record->part_amount ?? 0, 2)); ?></td>
                    <td>Rs.<?php echo e(number_format($record->labour_amount ?? 0, 2)); ?></td>
                    <td>Rs.<?php echo e(number_format($record->net_bill_amt ?? 0, 2)); ?></td>
                    <td><?php echo e($record->mileage ?? 'N/A'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Summary</div>
        <table>
            <tr><td class="label">Dealer</td><td><?php echo e($serviceHistory->dealer->name ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Search Date</td><td><?php echo e($serviceHistory->created_at->setTimezone('Asia/Kolkata')->format('d M Y, h:i A')); ?></td></tr>
            <tr><td class="label">Amount Charged</td><td>Rs.<?php echo e(number_format($serviceHistory->charge_amount, 2)); ?></td></tr>
        </table>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="section-title">Error</div>
        <p><?php echo e($serviceHistory->error_message ?? 'Search failed'); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>SAHI GADI - Vehicle Marketplace | Generated on: <?php echo e(date('d M Y, h:i A')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\service-histories\single-pdf.blade.php ENDPATH**/ ?>