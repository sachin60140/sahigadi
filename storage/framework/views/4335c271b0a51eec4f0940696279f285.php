<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Listings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Customer Car Listings</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Car Details</th>
                <th>Year</th>
                <th>Price</th>
                <th>Owner Details</th>
                <th>City</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($listing->id); ?></td>
                    <td>
                        <strong><?php echo e($listing->title); ?></strong><br>
                        <?php echo e($listing->brand ? $listing->brand->name : ''); ?> <?php echo e($listing->model); ?>

                    </td>
                    <td><?php echo e($listing->year); ?></td>
                    <td>₹<?php echo e(number_format($listing->price ?? 0)); ?></td>
                    <td>
                        <?php echo e($listing->owner_name); ?><br>
                        <?php echo e($listing->owner_phone); ?>

                    </td>
                    <td><?php echo e($listing->city); ?></td>
                    <td><?php echo e(ucfirst($listing->status)); ?></td>
                    <td><?php echo e($listing->created_at->format('d M, Y')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/customer-listings/pdf.blade.php ENDPATH**/ ?>