<?php $__env->startSection('title', 'Maruti Service History - ' . $marutiServiceHistory->vehicle_number . ' - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dealer.maruti-service-history.index')); ?>">Maruti Service History</a></li>
                <li class="breadcrumb-item active"><?php echo e($marutiServiceHistory->vehicle_number); ?></li>
            </ol>
        </nav>
        <h2><i class="bi bi-car-front me-2"></i><?php echo e($marutiServiceHistory->vehicle_number); ?></h2>
    </div>
    <div class="d-flex gap-2">
        <?php if($marutiServiceHistory->is_success): ?>
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
            <a href="<?php echo e(route('dealer.maruti-service-history.pdf', $marutiServiceHistory)); ?>" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
            </a>
        <?php else: ?>
            <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
        <?php endif; ?>
    </div>
</div>

<?php if(!$marutiServiceHistory->is_success): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Search Failed:</strong> <?php echo e($marutiServiceHistory->error_message); ?>

</div>
<?php endif; ?>

<?php if($marutiServiceHistory->is_success): ?>
<div class="row">
    <div class="col-lg-8">
        <?php if($marutiServiceHistory->records->count() > 0): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Service Records (<?php echo e($marutiServiceHistory->records->count()); ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Service Type</th>
                                <th>Dealer</th>
                                <th>Job Card</th>
                                <th>RO No</th>
                                <th class="text-end">Total Amt</th>
                                <th>Mileage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $marutiServiceHistory->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($record->svc_date ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($record->service_cate ?? 'N/A'); ?></span>
                                </td>
                                <td><?php echo e($record->dealer_name ?? 'N/A'); ?></td>
                                <td><?php echo e($record->register_no ?? 'N/A'); ?></td>
                                <td><?php echo e($record->repair_order_no ?? 'N/A'); ?></td>
                                <td class="text-end text-success">₹<?php echo e(number_format($record->total_amount ?? 0, 2)); ?></td>
                                <td><?php echo e($record->mileage ?? 'N/A'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: var(--accent); color: white;">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Bill Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Service Type</th>
                                <th class="text-end">Part Amt</th>
                                <th class="text-end">Labour Amt</th>
                                <th class="text-end">Total</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grouped = $marutiServiceHistory->records->groupBy('service_cate');
                            ?>
                            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($type ?? 'Unknown'); ?></td>
                                <td class="text-end">₹<?php echo e(number_format($records->sum('part_amount'), 2)); ?></td>
                                <td class="text-end">₹<?php echo e(number_format($records->sum('labour_amount'), 2)); ?></td>
                                <td class="text-end">₹<?php echo e(number_format($records->sum('total_amount'), 2)); ?></td>
                                <td><?php echo e($records->count()); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr class="fw-bold bg-light">
                                <td>Total</td>
                                <td class="text-end">₹<?php echo e(number_format($marutiServiceHistory->records->sum('part_amount'), 2)); ?></td>
                                <td class="text-end">₹<?php echo e(number_format($marutiServiceHistory->records->sum('labour_amount'), 2)); ?></td>
                                <td class="text-end">₹<?php echo e(number_format($marutiServiceHistory->records->sum('total_amount'), 2)); ?></td>
                                <td><?php echo e($marutiServiceHistory->records->count()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No service records found for this vehicle.
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Search Summary</h5>
            </div>
            <div class="card-body">
                <div class="detail-item mb-3">
                    <small class="text-muted">Search Date</small>
                    <h6 class="mb-0"><?php echo e($marutiServiceHistory->created_at->format('d M Y, h:i A')); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Total Services</small>
                    <h6 class="mb-0"><?php echo e($marutiServiceHistory->records->count()); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Amount Charged</small>
                    <h5 class="mb-0 text-success">₹<?php echo e(number_format($marutiServiceHistory->debit_amount ?? 0, 2)); ?></h5>
                </div>
                <hr>
                <a href="<?php echo e(route('dealer.maruti-service-history.index')); ?>" class="btn btn-outline-primary w-100">
                    <i class="bi bi-arrow-left me-2"></i>Back to Search
                </a>
                <a href="<?php echo e(route('dealer.maruti-service-history.search')); ?>?vehicle_number=<?php echo e($marutiServiceHistory->vehicle_number); ?>" class="btn btn-primary w-100 mt-2">
                    <i class="bi bi-arrow-repeat me-2"></i>Search Again
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.detail-item {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}
.detail-item small {
    display: block;
    margin-bottom: 4px;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/maruti-service-history/show.blade.php ENDPATH**/ ?>