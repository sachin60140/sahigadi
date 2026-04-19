<?php $__env->startSection('title', 'Vahan Details (RC Search) - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-car-front-fill" style="font-size: 4rem; color: var(--accent);"></i>
                        <h2 class="fw-bold mt-3">Vahan Details (RC Search)</h2>
                        <p class="text-muted">Get complete vehicle registration details from Vahan database</p>
                    </div>

                    <form action="<?php echo e(route('vehicle-search.search')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Registration Number <span class="text-danger">*</span></label>
                            <input type="text" name="registration_number" class="form-control" placeholder="e.g. BR01AB1234" required>
                            <small class="text-muted">Enter your vehicle registration number</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email Address <span class="text-muted">(Optional)</span></label>
                            <input type="email" name="customer_email" class="form-control" placeholder="Enter email address">
                        </div>

                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Price: ₹<?php echo e(number_format($charge, 0)); ?></strong> per report
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 py-3">
                            <i class="bi bi-search me-2"></i>Search Vehicle Details
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/vehicle-search/index.blade.php ENDPATH**/ ?>