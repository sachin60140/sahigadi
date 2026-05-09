<?php $__env->startSection('title', 'Featured Plans Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800">Featured Car Plans</h4>
        <a href="<?php echo e(route('admin.featured-plans.create')); ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add New Plan
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Plan Name</th>
                            <th class="py-3">Duration</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Status</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 fw-medium"><?php echo e($plan->name); ?></td>
                                <td class="py-3"><?php echo e($plan->duration_days); ?> Days</td>
                                <td class="py-3">₹<?php echo e(number_format($plan->price, 2)); ?></td>
                                <td class="py-3">
                                    <?php if($plan->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admin.featured-plans.edit', $plan)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.featured-plans.destroy', $plan)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No featured plans found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/featured-plans/index.blade.php ENDPATH**/ ?>