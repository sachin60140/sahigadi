<?php $__env->startSection('title', 'Service Tracking - Service History - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wrench me-2"></i>Service History Tracking</h2>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon primary">
                    <i class="bi bi-search"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0"><?php echo e($totalSearches); ?></h3>
                    <small class="text-muted">Total Searches</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0"><?php echo e($successfulSearches); ?></h3>
                    <small class="text-muted">Successful</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon warning">
                    <i class="bi bi-currency-rupee"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">₹<?php echo e(number_format($totalRevenue, 2)); ?></h3>
                    <small class="text-muted">Total Revenue</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon info">
                    <i class="bi bi-tag"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">C: ₹<?php echo e(number_format($charge)); ?> | D: ₹<?php echo e(number_format($dealerCharge)); ?></h3>
                    <small class="text-muted">Charges</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="<?php echo e(route('admin.service-tracking.service-history')); ?>" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search vehicle..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?php echo e(route('admin.service-tracking.service-history')); ?>" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>User Name</th>
                        <th>Phone</th>
                        <th>Vehicle Number</th>
                        <th>Status</th>
                        <th>Charge</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $allSearches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <span class="badge bg-<?php echo e($search->type == 'dealer' ? 'primary' : 'info'); ?>">
                                <?php echo e(ucfirst($search->type)); ?>

                            </span>
                        </td>
                        <td><?php echo e($search->user_name); ?></td>
                        <td><?php echo e($search->customer_phone ?? $search->dealer->phone ?? 'N/A'); ?></td>
                        <td><?php echo e($search->vehicle_number); ?></td>
                        <td>
                            <?php if($search->is_success): ?>
                                <span class="badge bg-success">Success</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td>₹<?php echo e(number_format($search->paid_amount ?? 0)); ?></td>
                        <td><?php echo e($search->created_at->format('d M Y H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No searches found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/service-tracking/service-history.blade.php ENDPATH**/ ?>