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

            <?php
                if($customer->profile_completion_percentage === 0) {
                    $customer->calculateProfileCompletion();
                }
                $completion = $customer->profile_completion_percentage;
                $missingFields = $customer->getMissingProfileFields();
            ?>

            <?php if($completion < 75): ?>
            <div class="alert alert-warning shadow-sm border-warning border-opacity-50 mb-4 rounded-4">
                <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Profile Incomplete</h5>
                <p class="mb-2">Your profile is currently at <strong><?php echo e($completion); ?>%</strong>. You need to complete at least <strong>75%</strong> of your profile to access all features like the Dashboard, Wallet, and Service History.</p>
                <hr class="border-warning opacity-25 my-2">
                <p class="mb-1 fw-medium">Missing Required Fields:</p>
                <ul class="mb-0 small">
                    <?php $__currentLoopData = $missingFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($field); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Profile Completion</h6>
                            <span class="badge <?php echo e($completion >= 75 ? 'bg-success' : 'bg-warning text-dark'); ?> fs-6"><?php echo e($completion); ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar <?php echo e($completion >= 75 ? 'bg-success' : 'bg-warning progress-bar-striped progress-bar-animated'); ?>" role="progressbar" style="width: <?php echo e($completion); ?>%;" aria-valuenow="<?php echo e($completion); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

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
                            <p class="text-muted small mb-1"><i class="bi bi-check-circle-fill text-success me-1"></i>Verified Mobile Number</p>
                            <p class="badge bg-secondary mb-0">ID: <?php echo e($customer->customer_unique_id); ?></p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Profile Photo <span class="text-danger">*</span></label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                            <div class="form-text">Upload a square image for best results (Max 2MB).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $customer->name)); ?>" placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $customer->email)); ?>" placeholder="Enter your email address">
                            <div class="form-text">We'll use this to send you important updates about your listings.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" <?php echo e(old('gender', $customer->gender) == 'male' ? 'selected' : ''); ?>>Male</option>
                                    <option value="female" <?php echo e(old('gender', $customer->gender) == 'female' ? 'selected' : ''); ?>>Female</option>
                                    <option value="other" <?php echo e(old('gender', $customer->gender) == 'other' ? 'selected' : ''); ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control" value="<?php echo e(old('dob', $customer->dob ? $customer->dob->format('Y-m-d') : '')); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="<?php echo e(old('whatsapp_number', $customer->whatsapp_number)); ?>" placeholder="For easier communication">
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Identity Information <span class="text-danger">*</span></h5>
                        <p class="text-muted small mb-3">Please provide either Aadhaar or PAN to verify your identity.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Aadhaar Number</label>
                                <input type="text" name="aadhaar_number" class="form-control" value="<?php echo e(old('aadhaar_number', $customer->aadhaar_number)); ?>" placeholder="12 Digit Aadhaar Number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">PAN Number</label>
                                <input type="text" name="pan_number" class="form-control" value="<?php echo e(old('pan_number', $customer->pan_number)); ?>" placeholder="10 Digit PAN Number">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Address Information</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo e(old('company_name', $customer->company_name)); ?>" placeholder="If you're representing a business">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">GST Number</label>
                            <input type="text" name="gst_number" class="form-control" value="<?php echo e(old('gst_number', $customer->gst_number)); ?>" placeholder="GSTIN">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Enter your complete address"><?php echo e(old('address', $customer->address)); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="<?php echo e(old('city', $customer->city)); ?>" placeholder="City">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" value="<?php echo e(old('state', $customer->state)); ?>" placeholder="State">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control" value="<?php echo e(old('pincode', $customer->pincode)); ?>" placeholder="Pincode">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/profile.blade.php ENDPATH**/ ?>