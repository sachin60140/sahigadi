<?php $__env->startSection('title', 'Car Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Car Details</h2>
    <a href="<?php echo e(route('admin.cars.index')); ?>" class="btn btn-secondary">Back</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><?php echo e($car->title); ?></h5>
            </div>
            <div class="card-body">
                <?php if($car->images->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $car->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 mb-2">
                        <img src="<?php echo e($image->url); ?>" class="img-thumbnail" alt="Car Image">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <hr>

                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" style="width: 150px;">Price</td>
                        <td><strong>₹<?php echo e(number_format($car->price ?? 0)); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Year</td>
                        <td><?php echo e($car->year ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fuel Type</td>
                        <td><?php echo e(ucfirst($car->fuel_type ?? 'N/A')); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Transmission</td>
                        <td><?php echo e(ucfirst($car->transmission ?? 'N/A')); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">KM Driven</td>
                        <td><?php echo e($car->km_driven ? number_format($car->km_driven) . ' km' : 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">City</td>
                        <td><?php echo e($car->city ?? 'N/A'); ?></td>
                    </tr>
                    <?php if($car->latitude && $car->longitude): ?>
                    <tr>
                        <td class="text-muted">Location</td>
                        <td>
                            <span class="me-2"><?php echo e($car->latitude); ?>, <?php echo e($car->longitude); ?></span>
                            <a href="https://www.google.com/maps?q=<?php echo e($car->latitude); ?>,<?php echo e($car->longitude); ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-geo-alt"></i> View on Map
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted">Owners</td>
                        <td><?php echo e($car->owners ?? 1); ?></td>
                    </tr>
                </table>

                <?php if($car->description): ?>
                <hr>
                <h6>Description</h6>
                <p><?php echo e($car->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dealer</h5>
            </div>
            <div class="card-body">
                <p><strong><?php echo e($car->dealer->name ?? 'N/A'); ?></strong></p>
                <p class="text-muted mb-1"><?php echo e($car->dealer->email ?? ''); ?></p>
                <p class="text-muted mb-0"><?php echo e($car->dealer->phone ?? ''); ?></p>
                <a href="<?php echo e(route('admin.dealers.show', $car->dealer_id)); ?>" class="btn btn-sm btn-outline-primary mt-2">View Dealer</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <?php if($car->status === 'pending'): ?>
                <form action="<?php echo e(route('admin.cars.approve', $car)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-success w-100 mb-2"><i class="bi bi-check-circle"></i> Approve</button>
                </form>
                <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle"></i> Reject
                </button>
                <?php endif; ?>

                <?php if(!$car->isFeatured()): ?>
                <form action="<?php echo e(route('admin.cars.featured', $car)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-warning w-100 mb-2"><i class="bi bi-star"></i> Make Featured</button>
                </form>
                <?php else: ?>
                <form action="<?php echo e(route('admin.cars.remove-featured', $car)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary w-100 mb-2"><i class="bi bi-star-fill"></i> Remove Featured</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.cars.reject', $car)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for rejection *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/cars/show.blade.php ENDPATH**/ ?>