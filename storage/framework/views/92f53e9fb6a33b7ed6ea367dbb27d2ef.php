<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RC Details - <?php echo e($vehicleSearch->registration_number); ?> - SAHI GADI</title>
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
        <div class="reg-number"><?php echo e($vehicleSearch->registration_number); ?></div>
        <?php if($vehicleSearch->is_success): ?>
            <span class="badge badge-success">Verified</span>
        <?php else: ?>
            <span class="badge badge-danger">Failed</span>
        <?php endif; ?>
    </div>

    <?php if($vehicleSearch->is_success && $vehicleSearch->vehicle_data): ?>
    <div class="section">
        <div class="section-title">Vehicle Details</div>
        <table>
            <?php $__currentLoopData = $vehicleSearch->vehicle_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></td>
                <td><?php echo e(is_array($value) ? json_encode($value) : ($value ?? 'N/A')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Search Summary</div>
        <table>
            <tr><td class="label">Customer Name</td><td><?php echo e($vehicleSearch->customer_name ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Customer Phone</td><td><?php echo e($vehicleSearch->customer_phone ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Search Date</td><td><?php echo e($vehicleSearch->created_at->format('d M Y, h:i A')); ?></td></tr>
            <tr><td class="label">Amount Paid</td><td>Rs. <?php echo e(number_format($vehicleSearch->paid_amount ?? 0, 2)); ?></td></tr>
        </table>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="section-title">Error</div>
        <p><?php echo e($vehicleSearch->error_message ?? 'Search failed'); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>SAHI GADI - Vehicle Marketplace | Generated on: <?php echo e(date('d M Y, h:i A')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\customer-vehicle-searches\single-pdf.blade.php ENDPATH**/ ?>