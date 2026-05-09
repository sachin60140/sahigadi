<?php $__env->startSection('title', 'Manage Dealers - SAHI GADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-people-fill me-2"></i>Manage Dealers</h4>
        <small class="text-muted">View and manage all registered dealers</small>
    </div>
    <a href="<?php echo e(route('admin.dealers.create')); ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Add Dealer
    </a>
</div>

<div class="stat-card mb-4">
    <form action="<?php echo e(route('admin.dealers.index')); ?>" method="GET" class="row g-3">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by ID, name, email, phone..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="<?php echo e(route('admin.dealers.index')); ?>" class="btn btn-outline-secondary text-center">Clear</a>
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
                <th><i class="bi bi-info-circle me-1"></i>Status</th>
                <th><i class="bi bi-calendar3 me-1"></i>Joined</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $dealers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dealer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><span class="badge bg-secondary"><?php echo e($dealer->dealer_unique_id); ?></span></td>
                <td>
                    <strong><?php echo e($dealer->name); ?></strong>
                    <?php if($dealer->company_name): ?>
                    <br><small class="text-muted"><?php echo e(Str::limit($dealer->company_name, 20)); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo e(Str::limit($dealer->email, 25)); ?></td>
                <td><?php echo e($dealer->phone); ?></td>
                <td><?php echo e($dealer->city ?? 'N/A'); ?></td>
                <td>
                    <?php if($dealer->status === 'approved'): ?>
                        <span class="badge bg-success badge-modern"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    <?php elseif($dealer->status === 'pending'): ?>
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-clock me-1"></i>Pending</span>
                    <?php else: ?>
                        <span class="badge bg-danger badge-modern"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($dealer->created_at->format('d M Y')); ?></td>
                <td>
                    <a href="<?php echo e(route('admin.dealers.show', $dealer)); ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                        <i class="bi bi-eye"></i>
                    </a>
                    
                    <form action="<?php echo e(route('admin.dealers.toggle-status', $dealer)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php if($dealer->status === 'approved'): ?>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to completely deactivate this dealer?')" title="Deactivate">
                                <i class="bi bi-power"></i>
                            </button>
                        <?php elseif($dealer->status === 'rejected'): ?>
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to reactivate this dealer?')" title="Activate">
                                <i class="bi bi-person-check-fill"></i>
                            </button>
                        <?php elseif($dealer->status === 'pending'): ?>
                            <button type="submit" class="btn btn-sm btn-success" title="Approve Dealer">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No dealers found</h5>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($dealers->hasPages()): ?>
<div class="d-flex justify-content-center mt-4">
    <?php echo e($dealers->withQueryString()->links()); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/dealers/index.blade.php ENDPATH**/ ?>