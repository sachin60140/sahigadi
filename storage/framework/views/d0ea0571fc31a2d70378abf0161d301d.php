<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>E-Challan Reports - SAHI GADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a1a2e; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1a1a2e; }
        .header p { margin: 5px 0; color: #666; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHI GADI - E-Challan Reports</h1>
        <p>Generated on: <?php echo e(date('d M Y, h:i A')); ?></p>
        <p>Total Revenue: Rs. <?php echo e(number_format($totalRevenue, 2)); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vehicle Number</th>
                <th>Dealer</th>
                <th>Challans</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><strong><?php echo e($search->vehicle_number); ?></strong></td>
                <td><?php echo e($search->dealer->name ?? 'N/A'); ?></td>
                <td><?php echo e($search->challan_count ?? 0); ?></td>
                <td>Rs. <?php echo e(number_format($search->total_amount ?? 0, 2)); ?></td>
                <td><?php echo e($search->is_success ? 'Success' : 'Failed'); ?></td>
                <td><?php echo e($search->created_at->format('d M Y')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p>SAHI GADI - Vehicle Marketplace | This is a system generated report</p>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\challan-searches\pdf.blade.php ENDPATH**/ ?>