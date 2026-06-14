<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Maruti Service History Reports - SAHI GADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a1a2e; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1a1a2e; }
        .header p { margin: 5px 0; color: #666; }
        .success { color: #16803a; font-weight: bold; }
        .failed { color: #c62828; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHI GADI - Maruti Service History Reports</h1>
        <p>Generated on: <?php echo e(now()->setTimezone('Asia/Kolkata')->format('d M Y, h:i A')); ?></p>
        <p>Total searches: <?php echo e($searches->count()); ?> | Revenue: Rs. <?php echo e(number_format($totalRevenue, 2)); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vehicle Number</th>
                <th>Dealer</th>
                <th>Services Found</th>
                <th>Charge</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($search->vehicle_number); ?></strong></td>
                    <td><?php echo e($search->dealer->name ?? 'N/A'); ?></td>
                    <td><?php echo e($search->service_count ?? 0); ?></td>
                    <td>Rs. <?php echo e(number_format($search->charge_amount ?? 0, 2)); ?></td>
                    <td class="<?php echo e($search->is_success ? 'success' : 'failed'); ?>">
                        <?php echo e($search->is_success ? 'Success' : 'Failed'); ?>

                    </td>
                    <td><?php echo e(optional($search->created_at)->format('d M Y')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6">No service history searches matched the selected filters.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        SAHI GADI - System generated report
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\maruti-service-histories\pdf.blade.php ENDPATH**/ ?>