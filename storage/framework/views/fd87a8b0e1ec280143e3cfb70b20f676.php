<?php $__env->startSection('title', 'Manage Cars - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-car-front-fill me-2"></i>Manage Cars</h4>
        <small class="text-muted">View and manage all car listings</small>
    </div>
    <a href="<?php echo e(route('admin.cars.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add New Car
    </a>
</div>

<div class="stat-card mb-4">
    <form action="<?php echo e(route('admin.cars.index')); ?>" method="GET" class="row g-3">
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by title..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="<?php echo e(route('admin.cars.index')); ?>" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="table-modern">
    <table class="table table-modern mb-0">
        <thead>
            <tr>
                <th><i class="bi bi-image me-1"></i>Image</th>
                <th><i class="bi bi-car me-1"></i>Car</th>
                <th><i class="bi bi-person me-1"></i>Dealer</th>
                <th><i class="bi bi-currency-rupee me-1"></i>Price</th>
                <th><i class="bi bi-geo-alt me-1"></i>Location</th>
                <th><i class="bi bi-info-circle me-1"></i>Status</th>
                <th><i class="bi bi-star me-1"></i>Featured</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <?php if($car->image_url): ?>
                        <img src="<?php echo e($car->image_url); ?>" alt="" style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 60px; border-radius: 8px;">
                            <i class="bi bi-car-front text-secondary"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?php echo e(Str::limit($car->title, 25)); ?></strong>
                    <br><small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo e($car->city ?? 'N/A'); ?></small>
                </td>
                <td><?php echo e(Str::limit($car->dealer->name ?? 'N/A', 20)); ?></td>
                <td class="fw-bold">₹<?php echo e(number_format($car->price ?? 0)); ?></td>
                <td>
                    <?php if($car->latitude && $car->longitude): ?>
                        <a href="https://www.google.com/maps?q=<?php echo e($car->latitude); ?>,<?php echo e($car->longitude); ?>" target="_blank" class="btn btn-sm btn-outline-success" title="View on Google Maps">
                            <i class="bi bi-geo-alt"></i>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($car->status === 'approved'): ?>
                        <span class="badge bg-success badge-modern"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    <?php elseif($car->status === 'pending'): ?>
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-clock me-1"></i>Pending</span>
                    <?php else: ?>
                        <span class="badge bg-danger badge-modern"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($car->isFeatured()): ?>
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-star-fill me-1"></i>Featured</span>
                    <?php else: ?>
                        <span class="badge bg-secondary badge-modern"><i class="bi bi-star me-1"></i>No</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(route('admin.cars.show', $car)); ?>" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-eye"></i>
                    </a>
                    <?php if($car->status === 'pending'): ?>
                    <form action="<?php echo e(route('admin.cars.approve', $car)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-check"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-car-front" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No cars found</h5>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($cars->hasPages()): ?>
<div class="d-flex justify-content-center mt-4">
    <?php echo e($cars->withQueryString()->links()); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/cars/index.blade.php ENDPATH**/ ?>