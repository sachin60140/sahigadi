<?php $__env->startSection('title', 'Service History Reports - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wrench me-2"></i>Service History Reports</h2>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.service-histories.settings')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-gear me-2"></i>Settings
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center">
                <div class="kpi-icon primary">
                    <i class="bi bi-wrench"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0"><?php echo e($searches->total()); ?></h3>
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
                    <h3 class="mb-0"><?php echo e($searches->where('is_success', true)->count()); ?></h3>
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
                    <h3 class="mb-0">₹<?php echo e(number_format($charge, 2)); ?></h3>
                    <small class="text-muted">Per Search</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-12">
                <form action="<?php echo e(route('admin.service-histories.index')); ?>" method="GET" class="row g-2">
                    <div class="col-md-2">
                        <input type="text" name="search" class="form-control" placeholder="Vehicle Number..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="dealer_id" class="form-select">
                            <option value="">All Dealers</option>
                            <?php $__currentLoopData = $dealers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dealer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dealer->id); ?>" <?php echo e(request('dealer_id') == $dealer->id ? 'selected' : ''); ?>><?php echo e($dealer->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                            <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="<?php echo e(route('admin.service-histories.index')); ?>" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="btn-group">
                    <a href="<?php echo e(route('admin.service-histories.exportExcel', request()->query())); ?>" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                    </a>
                    <a href="<?php echo e(route('admin.service-histories.exportPdf', request()->query())); ?>" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Vehicle Number</th>
                        <th>Dealer</th>
                        <th>Services Found</th>
                        <th>Charge</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-bold"><?php echo e($search->vehicle_number); ?></td>
                        <td><?php echo e($search->dealer->name ?? 'N/A'); ?></td>
                        <td><?php echo e($search->service_count ?? 0); ?> Records</td>
                        <td>₹<?php echo e(number_format($search->charge_amount, 2)); ?></td>
                        <td><?php echo e($search->created_at->format('d M Y')); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.service-histories.show', $search)); ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.service-histories.downloadPdf', $search)); ?>" class="btn btn-outline-danger">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-wrench text-secondary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-secondary">No searches found</h5>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($searches->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($searches->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\service-histories\index.blade.php ENDPATH**/ ?>