<?php $__env->startSection('title', 'Maruti Service History Details - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>Maruti Service History Details</h2>
    <div>
        <a href="<?php echo e(route('admin.maruti-service-histories.downloadPdf', $marutiServiceHistory)); ?>" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
        <a href="<?php echo e(route('admin.maruti-service-histories.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Vehicle Number: <?php echo e($marutiServiceHistory->vehicle_number); ?></h5>
                <p>Dealer: <?php echo e($marutiServiceHistory->dealer->name ?? 'N/A'); ?></p>
                <p>Date: <?php echo e($marutiServiceHistory->created_at->format('d M Y H:i')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p>Services: <?php echo e($marutiServiceHistory->service_count); ?></p>
                <p>Charge: Rs.<?php echo e(number_format($marutiServiceHistory->charge_amount ?? 0)); ?></p>
                <?php if($marutiServiceHistory->is_success): ?>
                    <span class="badge bg-success">Success</span>
                <?php else: ?>
                    <span class="badge bg-danger">Failed</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($marutiServiceHistory->is_success && $marutiServiceHistory->raw_response): ?>
<?php
$records = $marutiServiceHistory->raw_response['result']['serviceHistoryDetails'] ?? [];
?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
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
                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($record['dateOfSVC'] ?? 'N/A'); ?></td>
                        <td><?php echo e($record['serviceType'] ?? 'N/A'); ?></td>
                        <td><?php echo e($record['dealerName'] ?? 'N/A'); ?></td>
                        <td><?php echo e($record['noOfJobCard'] ?? 'N/A'); ?></td>
                        <td><?php echo e($record['noOfRO'] ?? 'N/A'); ?></td>
                        <td>Rs.<?php echo e(number_format($record['partAmmount'] ?? 0)); ?></td>
                        <td>Rs.<?php echo e(number_format($record['labourAmmount'] ?? 0)); ?></td>
                        <td>Rs.<?php echo e(number_format($record['totalAmount'] ?? 0)); ?></td>
                        <td><?php echo e($record['mileage'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">
    <?php echo e($marutiServiceHistory->error_message ?? 'No data available'); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\maruti-service-histories\show.blade.php ENDPATH**/ ?>