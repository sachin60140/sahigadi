<?php $__env->startSection('title', 'Edit Profile - SAHI GADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            <?php echo $__env->make('frontend.customer.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex align-items-center mb-4">
                <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h2 class="fw-bold mb-0">Edit Profile</h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('customer.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="mb-4 d-none d-lg-block text-center">
                            <?php if($customer->profile_image): ?>
                                <img src="<?php echo e(asset('storage/' . $customer->profile_image)); ?>" alt="Profile" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-person text-secondary" style="font-size: 2.5rem;"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="fw-bold mb-1">+91 <?php echo e($customer->phone); ?></h5>
                            <p class="text-muted small"><i class="bi bi-check-circle-fill text-success me-1"></i>Verified Mobile Number</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Profile Photo</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                            <div class="form-text">Upload a square image for best results (Max 2MB).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $customer->name)); ?>" placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address (Optional)</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $customer->email)); ?>" placeholder="Enter your email address">
                            <div class="form-text">We'll use this to send you important updates about your listings.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">WhatsApp Number (Optional)</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="<?php echo e(old('whatsapp_number', $customer->whatsapp_number)); ?>" placeholder="For easier communication">
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Address Information</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company Name (Optional)</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo e(old('company_name', $customer->company_name)); ?>" placeholder="If you're representing a business">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">GST Number (Optional)</label>
                            <input type="text" name="gst_number" class="form-control" value="<?php echo e(old('gst_number', $customer->gst_number)); ?>" placeholder="GSTIN">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Address (Optional)</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Enter your complete address"><?php echo e(old('address', $customer->address)); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City (Optional)</label>
                                <input type="text" name="city" class="form-control" value="<?php echo e(old('city', $customer->city)); ?>" placeholder="City">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">State (Optional)</label>
                                <input type="text" name="state" class="form-control" value="<?php echo e(old('state', $customer->state)); ?>" placeholder="State">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Pincode (Optional)</label>
                                <input type="text" name="pincode" class="form-control" value="<?php echo e(old('pincode', $customer->pincode)); ?>" placeholder="Pincode">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/profile.blade.php ENDPATH**/ ?>