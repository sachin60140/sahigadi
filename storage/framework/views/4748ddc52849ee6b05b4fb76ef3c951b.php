<?php $__env->startSection('title', 'Customer RC Searches - SAHI GADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-car-front me-2"></i>Customer RC Searches</h2>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary"><?php echo e($searches->total()); ?></h3>
                <p class="text-muted mb-0">Total Searches</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success">Rs.<?php echo e(number_format($totalRevenue)); ?></h3>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('admin.customer-vehicle-searches.index')); ?>" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Registration No, Name, Phone" value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                    <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Search</button>
                <a href="<?php echo e(route('admin.customer-vehicle-searches.index')); ?>" class="btn btn-outline-secondary">Clear</a>
            </div>
            <div class="col-12 mt-3">
                <div class="float-end">
                    <a href="<?php echo e(route('admin.customer-vehicle-searches.exportExcel', request()->query())); ?>" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>Excel
                    </a>
                    <a href="<?php echo e(route('admin.customer-vehicle-searches.exportPdf', request()->query())); ?>" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Registration Number</th>
                        <th>Charge Paid</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($search->id); ?></td>
                        <td>
                            <?php echo e($search->customer_name ?? 'N/A'); ?><br>
                            <small class="text-muted"><?php echo e($search->customer_phone ?? 'N/A'); ?></small>
                        </td>
                        <td class="text-uppercase fw-bold"><?php echo e($search->registration_number); ?></td>
                        <td class="fw-bold text-success">Rs.<?php echo e(number_format($search->paid_amount ?? 0)); ?></td>
                        <td>
                            <?php if($search->is_success): ?>
                                <span class="badge bg-success">Success</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($search->created_at->format('d M Y h:i A')); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.customer-vehicle-searches.show', $search)); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if($search->is_success): ?>
                            <a href="<?php echo e(route('admin.customer-vehicle-searches.downloadPdf', $search)); ?>" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">No searches found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php echo e($searches->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/customer-vehicle-searches/index.blade.php ENDPATH**/ ?>