<?php $__env->startSection('title', 'RC Details - ' . $vehicleSearch->registration_number . ' - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('admin.vehicle-searches.index')); ?>" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h2><i class="bi bi-car-front me-2"></i><?php echo e($vehicleSearch->registration_number); ?></h2>
    </div>
    <div class="d-flex gap-2">
        <?php if($vehicleSearch->is_success): ?>
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
        <?php else: ?>
            <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.vehicle-searches.downloadPdf', $vehicleSearch)); ?>" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
        </a>
    </div>
</div>

<?php if(!$vehicleSearch->is_success): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Search Failed:</strong> <?php echo e($vehicleSearch->error_message); ?>

</div>
<?php endif; ?>

<?php if($vehicleSearch->is_success && $dealerSearch): ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Owner Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Owner Name</small>
                            <h5 class="mb-0"><?php echo e($dealerSearch->owner_name ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Father's Name</small>
                            <h5 class="mb-0"><?php echo e($dealerSearch->father_name ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-item">
                            <small class="text-muted">Address</small>
                            <h5 class="mb-0"><?php echo e($dealerSearch->address ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Mobile Number</small>
                            <h5 class="mb-0"><?php echo e($dealerSearch->mobile_number ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">RTO Location</small>
                            <h5 class="mb-0"><?php echo e($dealerSearch->rto_location ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header" style="background: var(--accent); color: white;">
                <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Vehicle Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Category</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->vehicle_category ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Make</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->make ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Model</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->model ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Color</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->color ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Fuel Type</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->fuel_type ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Seating Capacity</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->seats ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">RC Status</small>
                            <span class="badge bg-success"><?php echo e($dealerSearch->rc_status ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Engine Number</small>
                            <h6 class="mb-0 font-monospace"><?php echo e($dealerSearch->engine_number ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Chassis Number</small>
                            <h6 class="mb-0 font-monospace"><?php echo e($dealerSearch->chassis_number ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Documents & Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Insurance Provider</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->insurance_provider ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Insurance Valid Till</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->insurance_date ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Fitness Valid Till</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->fitness_date ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">PUC Valid Till</small>
                            <h6 class="mb-0"><?php echo e($dealerSearch->puc_validity ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Blacklist Status</small>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Clean</span>
                        </div>
                    </div>
                    <?php if($dealerSearch->financed): ?>
                    <div class="col-md-6">
                        <div class="detail-item border-warning">
                            <small class="text-muted">Financed</small>
                            <span class="badge bg-warning text-dark">Yes - <?php echo e($dealerSearch->lender_name ?? 'Lender'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
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
                    <h6 class="mb-0"><?php echo e($vehicleSearch->dealer->name ?? 'N/A'); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Search Date</small>
                    <h6 class="mb-0"><?php echo e($vehicleSearch->created_at->format('d M Y, h:i A')); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Amount Charged</small>
                    <h5 class="mb-0 text-success">₹<?php echo e(number_format($vehicleSearch->charge_amount, 2)); ?></h5>
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
.detail-item.border-warning {
    border-left: 3px solid #ffc107;
    background: #fff9e6;
}
.detail-item small { display: block; margin-bottom: 4px; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\vehicle-searches\show.blade.php ENDPATH**/ ?>