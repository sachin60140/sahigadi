<?php $__env->startSection('title', 'Manage Customers - SAHI GADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-hearts me-2"></i>Manage Customers</h4>
        <small class="text-muted">View and manage all registered customers on the platform</small>
    </div>
</div>

<div class="stat-card mb-4">
    <form action="<?php echo e(route('admin.customers.index')); ?>" method="GET" class="row g-3">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control" placeholder="Search by ID, name, email, phone..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-outline-secondary w-100 text-center">Clear</a>
        </div>
    </form>
</div>

<div class="table-modern">
    <table class="table mb-0">
        <thead>
            <tr>
                <th><i class="bi bi-hash me-1"></i>ID</th>
                <th><i class="bi bi-person me-1"></i>Name</th>
                <th><i class="bi bi-envelope me-1"></i>Email</th>
                <th><i class="bi bi-telephone me-1"></i>Phone</th>
                <th><i class="bi bi-geo-alt me-1"></i>City</th>
                <th><i class="bi bi-wallet2 me-1"></i>Wallet Balance</th>
                <th><i class="bi bi-calendar3 me-1"></i>Joined</th>
                <th><i class="bi bi-person-badge me-1"></i>Profile</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><span class="badge bg-secondary"><?php echo e($customer->customer_unique_id); ?></span></td>
                <td>
                    <strong><?php echo e($customer->name); ?></strong>
                    <?php if($customer->company_name): ?>
                    <br><small class="text-muted"><?php echo e(Str::limit($customer->company_name, 20)); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo e(Str::limit($customer->email, 25)); ?></td>
                <td><?php echo e($customer->phone); ?></td>
                <td><?php echo e($customer->city ?? 'N/A'); ?></td>
                <td>
                    <?php if($customer->wallet): ?>
                        <strong class="text-success">₹<?php echo e(number_format($customer->wallet->balance, 2)); ?></strong>
                    <?php else: ?>
                        <span class="text-muted">₹0.00</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($customer->created_at->format('d M Y')); ?></td>
                <td>
                    <div class="progress mb-1" style="height: 6px;">
                        <div class="progress-bar <?php echo e($customer->profile_completion_percentage >= 75 ? 'bg-success' : 'bg-warning'); ?>" role="progressbar" style="width: <?php echo e($customer->profile_completion_percentage); ?>%;" aria-valuenow="<?php echo e($customer->profile_completion_percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted"><?php echo e($customer->profile_completion_percentage); ?>% Completed</small>
                </td>
                <td>
                    <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                        <i class="bi bi-eye"></i> View
                    </a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No customers found</h5>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($customers->hasPages()): ?>
<div class="d-flex justify-content-center mt-4">
    <?php echo e($customers->withQueryString()->links()); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/customers/index.blade.php ENDPATH**/ ?>