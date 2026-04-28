<?php $__env->startSection('title', 'Service History - ' . $serviceHistory->vehicle_number . ' - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dealer.service-history.index')); ?>">Service History</a></li>
                <li class="breadcrumb-item active"><?php echo e($serviceHistory->vehicle_number); ?></li>
            </ol>
        </nav>
        <h2><i class="bi bi-wrench me-2"></i><?php echo e($serviceHistory->vehicle_number); ?></h2>
    </div>
    <div class="d-flex gap-2">
        <?php if($serviceHistory->is_success): ?>
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i><?php echo e($serviceHistory->records->count()); ?> Services Found</span>
            <a href="<?php echo e(route('dealer.service-history.pdf', $serviceHistory)); ?>" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
            </a>
        <?php else: ?>
            <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
        <?php endif; ?>
    </div>
</div>

<?php if(!$serviceHistory->is_success): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Search Failed:</strong> <?php echo e($serviceHistory->error_message); ?>

</div>
<?php endif; ?>

<?php if($serviceHistory->is_success && $serviceHistory->records->count() > 0): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-wrench me-2"></i>Service Records (<?php echo e($serviceHistory->records->count()); ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Service Date</th>
                        <th>Dealer</th>
                        <th>Work Type</th>
                        <th>Status</th>
                        <th>Bill Amount</th>
                        <th>Mileage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $serviceHistory->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($record->svc_date ?? 'N/A'); ?></td>
                        <td>
                            <small><?php echo e($record->dealer_name ?? 'N/A'); ?></small><br>
                            <small class="text-muted"><?php echo e($record->location_name ?? 'N/A'); ?></small>
                        </td>
                        <td><?php echo e($record->work_type ?? 'N/A'); ?></td>
                        <td><span class="badge bg-success"><?php echo e($record->status ?? 'N/A'); ?></span></td>
                        <td>₹<?php echo e($record->net_bill_amt ?? '0'); ?></td>
                        <td><?php echo e($record->mileage ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Detailed Service Information</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php $__currentLoopData = $serviceHistory->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Service #<?php echo e($index + 1); ?></strong>
                        <span class="badge bg-secondary"><?php echo e($record->service_cate ?? 'N/A'); ?></span>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-6">
                            <small class="text-muted">RO Number:</small>
                            <div><?php echo e($record->repair_order_no ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Bill Number:</small>
                            <div><?php echo e($record->repair_order_bill_no ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Service By:</small>
                            <div><?php echo e($record->service_assistant_name ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Outstanding:</small>
                            <div>₹<?php echo e($record->out_standing_amt ?? '0'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Search Summary</h5>
    </div>
    <div class="card-body">
        <div class="detail-item mb-3">
            <small class="text-muted">Search Date</small>
            <h6 class="mb-0"><?php echo e($serviceHistory->created_at->format('d M Y, h:i A')); ?></h6>
        </div>
        <div class="detail-item mb-3">
            <small class="text-muted">Amount Charged</small>
            <h5 class="mb-0 text-success">₹<?php echo e(number_format($serviceHistory->debit_amount, 2)); ?></h5>
        </div>
        <hr>
        <a href="<?php echo e(route('dealer.service-history.index')); ?>" class="btn btn-outline-primary w-100">
            <i class="bi bi-arrow-left me-2"></i>Back to Search
        </a>
        <a href="<?php echo e(route('dealer.service-history.search')); ?>?vehicle_number=<?php echo e($serviceHistory->vehicle_number); ?>" class="btn btn-primary w-100 mt-2">
            <i class="bi bi-arrow-repeat me-2"></i>Search Again
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.detail-item {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}
.detail-item small { display: block; margin-bottom: 4px; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\service-history\show.blade.php ENDPATH**/ ?>