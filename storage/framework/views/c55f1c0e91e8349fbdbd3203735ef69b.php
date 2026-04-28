<?php $__env->startSection('title', 'RC Search Result - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if(isset($success) && !$success): ?>
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Search Failed</h5>
                    <p class="mb-0"><?php echo e($message); ?></p>
                </div>
            <?php endif; ?>

            <?php if($vehicleSearch->is_success): ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Vehicle Details Found</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($cached) && $cached): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5 class="fw-bold">Registration Number: <?php echo e($vehicleSearch->registration_number); ?></h5>
                            <?php if($vehicleSearch->customer_name): ?>
                                <p class="text-muted mb-0">Requested by: <?php echo e($vehicleSearch->customer_name); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if($vehicleSearch->vehicle_data): ?>
                            <div class="row g-3">
                                <?php $__currentLoopData = $vehicleSearch->vehicle_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></small>
                                        <strong><?php echo e($value ?? 'N/A'); ?></strong>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>No detailed data available.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?php echo e($vehicleSearch->error_message ?? 'No vehicle details found for this registration number.'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?php echo e(route('vehicle-search.index')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Search Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\frontend\vehicle-search\result.blade.php ENDPATH**/ ?>