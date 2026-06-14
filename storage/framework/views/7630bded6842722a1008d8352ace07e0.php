<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Mahindra Service Histories</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; padding: 10px; background: #1a1a2e; color: white; }
        .header h1 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        .success { color: green; }
        .failed { color: red; }
    </style>
</head>
<body>
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <div class="header">
        <h1>SAHI GADI - Customer Mahindra Service Histories</h1>
        <p>Total Searches: <?php echo e($searches->count()); ?> | Total Revenue: Rs.<?php echo e(number_format($totalRevenue)); ?></p>
        <p>Generated on: <?php echo e(date('d M Y, h:i A')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Vehicle Number</th>
                <th>Charge Paid</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($search->id); ?></td>
                <td><?php echo e($search->customer_name ?? 'N/A'); ?></td>
                <td><?php echo e($search->customer_phone ?? 'N/A'); ?></td>
                <td><?php echo e($search->vehicle_number); ?></td>
                <td>Rs.<?php echo e(number_format($search->paid_amount ?? 0)); ?></td>
                <td class="<?php echo e($search->is_success ? 'success' : 'failed'); ?>">
                    <?php echo e($search->is_success ? 'Success' : 'Failed'); ?>

                </td>
                <td><?php echo e($search->created_at->format('d M Y')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p>SAHI GADI Admin Panel</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\mahindra-service-histories\pdf.blade.php ENDPATH**/ ?>