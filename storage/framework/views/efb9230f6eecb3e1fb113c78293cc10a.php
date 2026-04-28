<?php $__env->startSection('title', 'Service History - ' . $serviceHistory->vehicle_number . ' - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('admin.service-histories.index')); ?>" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h2><i class="bi bi-wrench me-2"></i><?php echo e($serviceHistory->vehicle_number); ?></h2>
    </div>
    <div class="d-flex gap-2">
        <?php if($serviceHistory->is_success): ?>
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i><?php echo e($serviceHistory->service_count ?? 0); ?> Services</span>
        <?php else: ?>
            <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.service-histories.downloadPdf', $serviceHistory)); ?>" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
        </a>
    </div>
</div>

<?php if(!$serviceHistory->is_success): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Search Failed:</strong> <?php echo e($serviceHistory->error_message); ?>

</div>
<?php endif; ?>

<?php if($serviceHistory->is_success && $dealerSearch): ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-wrench me-2"></i>Service Records (<?php echo e($dealerSearch->records->count()); ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Dealer</th>
                                <th>Work Type</th>
                                <th>Bill Amount</th>
                                <th>Mileage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $dealerSearch->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($record->svc_date ?? 'N/A'); ?></td>
                                <td>
                                    <small><?php echo e($record->dealer_name ?? 'N/A'); ?></small><br>
                                    <small class="text-muted"><?php echo e($record->location_name ?? ''); ?></small>
                                </td>
                                <td><?php echo e($record->work_type ?? 'N/A'); ?></td>
                                <td>₹<?php echo e($record->net_bill_amt ?? '0'); ?></td>
                                <td><?php echo e($record->mileage ?? 'N/A'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Search Summary</h5>
            </div>
            <div class="card-body">
                <div class="detail-item mb-3">
                    <small class="text-muted">Dealer</small>
                    <h6 class="mb-0"><?php echo e($serviceHistory->dealer->name ?? 'N/A'); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Search Date</small>
                    <h6 class="mb-0"><?php echo e($serviceHistory->created_at->format('d M Y, h:i A')); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Amount Charged</small>
                    <h5 class="mb-0 text-success">₹<?php echo e(number_format($serviceHistory->charge_amount, 2)); ?></h5>
                </div>
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
.detail-item small { display: block; margin-bottom: 4px; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\service-histories\show.blade.php ENDPATH**/ ?>