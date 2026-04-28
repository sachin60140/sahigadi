<?php $__env->startSection('title', 'RC Details - ' . $vehicleSearch->registration_number . ' - SAHIGADI'); ?>

<?php
function formatDate($date) {
    if (empty($date)) return null;
    try {
        $carbon = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        return $carbon->format('d M Y');
    } catch (\Exception $e) {
        return $date;
    }
}

function isExpired($date): bool {
    if (empty($date)) return false;
    try {
        $carbon = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        return $carbon->isPast();
    } catch (\Exception $e) {
        return false;
    }
}
?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dealer.vehicle-search.index')); ?>">RC Details</a></li>
                <li class="breadcrumb-item active"><?php echo e($vehicleSearch->registration_number); ?></li>
            </ol>
        </nav>
        <h2><i class="bi bi-car-front me-2"></i><?php echo e($vehicleSearch->registration_number); ?></h2>
    </div>
    <div class="d-flex gap-2">
        <?php if($vehicleSearch->is_success): ?>
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
            <a href="<?php echo e(route('dealer.vehicle-search.pdf', $vehicleSearch)); ?>" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
            </a>
        <?php else: ?>
            <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Failed</span>
        <?php endif; ?>
    </div>
</div>

<?php if(!$vehicleSearch->is_success): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Search Failed:</strong> <?php echo e($vehicleSearch->error_message); ?>

</div>
<?php endif; ?>

<?php if($vehicleSearch->is_success): ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Owner Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Owner Name</small>
                            <h5 class="mb-0"><?php echo e($vehicleSearch->owner_name ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Father's Name</small>
                            <h5 class="mb-0"><?php echo e($vehicleSearch->father_name ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-item">
                            <small class="text-muted">Address</small>
                            <h5 class="mb-0"><?php echo e($vehicleSearch->address ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Mobile Number</small>
                            <h5 class="mb-0"><?php echo e($vehicleSearch->mobile_number ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">RTO Location</small>
                            <h5 class="mb-0"><?php echo e($vehicleSearch->rto_location ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header" style="background: var(--accent); color: white;">
                <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Vehicle Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Vehicle Class</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->vehicle_class ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Category</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->vehicle_category ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Make</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->make ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Model</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->model ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Variant</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->variant ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Color</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->color ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Fuel Type</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->fuel_type ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Seating Capacity</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->seats ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-item">
                            <small class="text-muted">Norms Type</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->norms_type ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Engine Number</small>
                            <h6 class="mb-0 font-monospace"><?php echo e($vehicleSearch->engine_number ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Chassis Number</small>
                            <h6 class="mb-0 font-monospace"><?php echo e($vehicleSearch->chassis_number ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Cubic Capacity</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->cubic_capacity ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Unladen Weight</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->unladen_weight ?? 'N/A'); ?> kg</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Cylinders</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->cylinders ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Registration Date</small>
                            <h6 class="mb-0"><?php echo e(formatDate($vehicleSearch->registration_date) ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">Manufactured</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->manufactured_date ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <small class="text-muted">RC Status</small>
                            <div>
                                <?php if($vehicleSearch->rc_status): ?>
                                    <?php $status = strtolower($vehicleSearch->rc_status); ?>
                                    <?php if($status === 'active'): ?>
                                        <span class="badge bg-success"><?php echo e($vehicleSearch->rc_status); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?php echo e($vehicleSearch->rc_status); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Documents & Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Insurance Provider</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->insurance_provider ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Insurance Policy No.</small>
                            <h6 class="mb-0 font-monospace"><?php echo e($vehicleSearch->insurance_policy_number ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Insurance Valid Till</small>
                            <h6 class="mb-0">
                                <?php if($vehicleSearch->insurance_date): ?>
                                    <?php if(isExpired($vehicleSearch->insurance_date)): ?>
                                        <span class="text-danger"><?php echo e(formatDate($vehicleSearch->insurance_date)); ?> (Expired)</span>
                                    <?php else: ?>
                                        <span class="text-success"><?php echo e(formatDate($vehicleSearch->insurance_date)); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Fitness Valid Till</small>
                            <h6 class="mb-0">
                                <?php if($vehicleSearch->fitness_date): ?>
                                    <?php if(isExpired($vehicleSearch->fitness_date)): ?>
                                        <span class="text-danger"><?php echo e(formatDate($vehicleSearch->fitness_date)); ?> (Expired)</span>
                                    <?php else: ?>
                                        <span class="text-success"><?php echo e(formatDate($vehicleSearch->fitness_date)); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">PUC Number</small>
                            <h6 class="mb-0 font-monospace"><?php echo e($vehicleSearch->puc_number ?? 'N/A'); ?></h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">PUC Valid Till</small>
                            <h6 class="mb-0">
                                <?php if($vehicleSearch->puc_validity): ?>
                                    <?php if(isExpired($vehicleSearch->puc_validity)): ?>
                                        <span class="text-danger"><?php echo e(formatDate($vehicleSearch->puc_validity)); ?> (Expired)</span>
                                    <?php else: ?>
                                        <span class="text-success"><?php echo e(formatDate($vehicleSearch->puc_validity)); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Tax Valid Till</small>
                            <h6 class="mb-0">
                                <?php if($vehicleSearch->tax_validity): ?>
                                    <?php if(isExpired($vehicleSearch->tax_validity)): ?>
                                        <span class="text-danger"><?php echo e(formatDate($vehicleSearch->tax_validity)); ?> (Expired)</span>
                                    <?php else: ?>
                                        <span class="text-success"><?php echo e(formatDate($vehicleSearch->tax_validity)); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Blacklist Status</small>
                            <div>
                                <?php if($vehicleSearch->blacklist_status): ?>
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i><?php echo e($vehicleSearch->blacklist_status); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Clean</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($vehicleSearch->financed || $vehicleSearch->permit_number || $vehicleSearch->is_commercial): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if($vehicleSearch->financed): ?>
                    <div class="col-md-6">
                        <div class="detail-item border-warning">
                            <small class="text-muted">Financed</small>
                            <div>
                                <span class="badge bg-warning text-dark"><i class="bi bi-currency-dollar me-1"></i>Yes - Financed Vehicle</span>
                            </div>
                            <?php if($vehicleSearch->lender_name): ?>
                            <small class="text-muted mt-1">Lender: <?php echo e($vehicleSearch->lender_name); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($vehicleSearch->permit_number): ?>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Permit Number</small>
                            <h6 class="mb-0"><?php echo e($vehicleSearch->permit_number); ?></h6>
                            <?php if($vehicleSearch->permit_type): ?>
                            <small class="text-muted">Type: <?php echo e($vehicleSearch->permit_type); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($vehicleSearch->is_commercial): ?>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Commercial Vehicle</small>
                            <div><span class="badge bg-info">Yes - Commercial</span></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
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
                    <h6 class="mb-0"><?php echo e($vehicleSearch->created_at->format('d M Y, h:i A')); ?></h6>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Amount Charged</small>
                    <h5 class="mb-0 text-success">₹<?php echo e(number_format($vehicleSearch->debit_amount, 2)); ?></h5>
                </div>
                <hr>
                <a href="<?php echo e(route('dealer.vehicle-search.index')); ?>" class="btn btn-outline-primary w-100">
                    <i class="bi bi-arrow-left me-2"></i>Back to Search
                </a>
                <a href="<?php echo e(route('dealer.vehicle-search.search')); ?>?registration_number=<?php echo e($vehicleSearch->registration_number); ?>" class="btn btn-primary w-100 mt-2">
                    <i class="bi bi-arrow-repeat me-2"></i>Search Again
                </a>
            </div>
        </div>

        <?php if($vehicleSearch->is_success): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <?php if($vehicleSearch->mobile_number): ?>
                <a href="tel:<?php echo e($vehicleSearch->mobile_number); ?>" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-telephone me-2"></i>Call Owner
                </a>
                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $vehicleSearch->mobile_number)); ?>" target="_blank" class="btn btn-success w-100 mb-2">
                    <i class="bi bi-whatsapp me-2"></i>WhatsApp
                </a>
                <?php endif; ?>
                <button onclick="copyToClipboard('<?php echo e($vehicleSearch->registration_number); ?>')" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-clipboard me-2"></i>Copy Reg Number
                </button>
            </div>
        </div>
        <?php endif; ?>
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
.detail-item small {
    display: block;
    margin-bottom: 4px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied: ' + text);
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\vehicle-search\show.blade.php ENDPATH**/ ?>