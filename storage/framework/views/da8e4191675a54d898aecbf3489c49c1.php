<?php $__env->startSection('title', 'RC Search Result - SAHI GADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <?php if(auth('customer')->check()): ?>
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h3 class="fw-bold mb-0">RC Search Details</h3>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                Wallet Balance: ₹<?php echo e(number_format(auth('customer')->user()->wallet->balance ?? 0, 2)); ?>

            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="row <?php echo e(!auth('customer')->check() ? 'justify-content-center' : ''); ?>">
        <?php if(auth('customer')->check()): ?>
            <div class="col-lg-3 d-none d-lg-block">
                <?php echo $__env->make('frontend.customer.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endif; ?>

        <div class="<?php echo e(auth('customer')->check() ? 'col-lg-9' : 'col-lg-10'); ?>">
            <?php if($vehicleSearch->is_success): ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Vehicle Details Found</h4>
                    </div>
                    <div class="card-body">

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
                <a href="<?php echo e(route('vehicle-search.index')); ?>" class="btn btn-outline-primary me-2">
                    <i class="bi bi-arrow-left me-2"></i>Back to Search
                </a>
                <?php if($vehicleSearch->is_success): ?>
                <a href="<?php echo e(route('vehicle-search.pdf', $vehicleSearch->id)); ?>" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/vehicle-search/show.blade.php ENDPATH**/ ?>