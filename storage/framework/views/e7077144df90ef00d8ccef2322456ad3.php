<?php $__env->startSection('title', 'My Cars'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Cars</h2>
    <a href="<?php echo e(route('dealer.cars.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Car
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('dealer.cars.index')); ?>" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?php echo e(route('dealer.cars.index')); ?>" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <?php if($car->image_url): ?>
                                <img src="<?php echo e($car->image_url); ?>" alt="" style="width: 80px; height: 60px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                    <i class="bi bi-car-front text-white"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('dealer.cars.edit', $car)); ?>"><?php echo e(Str::limit($car->title, 30)); ?></a>
                            <?php if($car->rejection_reason): ?>
                                <br><small class="text-danger"><?php echo e($car->rejection_reason); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>₹<?php echo e(number_format($car->price ?? 0)); ?></td>
                        <td>
                            <?php if($car->status === 'approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif($car->status === 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($car->isFeatured()): ?>
                                <span class="badge bg-warning"><i class="bi bi-star-fill"></i> Featured</span>
                            <?php else: ?>
                                <span class="text-muted">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('dealer.cars.edit', $car)); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('dealer.cars.destroy', $car)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No cars found. <a href="<?php echo e(route('dealer.cars.create')); ?>">Add your first car</a></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($cars->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\cars\index.blade.php ENDPATH**/ ?>