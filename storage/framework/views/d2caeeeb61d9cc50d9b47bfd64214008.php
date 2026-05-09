<?php $__env->startSection('title', 'My Dashboard - SAHI GADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h2 class="fw-bold mb-1">My Dashboard</h2>
                <p class="text-muted mb-0">Manage your listed cars and profile</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            <?php echo $__env->make('frontend.customer.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">My Car Listings</h4>
                <a href="<?php echo e(route('sell-car.index')); ?>" class="btn btn-accent btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Sell a Car
                </a>
            </div>

            <?php if($listings->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="position-relative">
                                <?php
                                    $images = json_decode($listing->images, true) ?? [];
                                ?>
                                <?php if(count($images) > 0): ?>
                                    <img src="<?php echo e(asset('storage/' . $images[0])); ?>" class="card-img-top" alt="<?php echo e($listing->title); ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if($listing->status == 'approved'): ?>
                                    <span class="position-absolute top-0 start-0 badge bg-success m-2">Approved</span>
                                <?php elseif($listing->status == 'pending'): ?>
                                    <span class="position-absolute top-0 start-0 badge bg-warning text-dark m-2">Pending Review</span>
                                <?php else: ?>
                                    <span class="position-absolute top-0 start-0 badge bg-danger m-2">Rejected</span>
                                <?php endif; ?>

                                <?php if($listing->isFeatured()): ?>
                                    <span class="position-absolute top-0 start-0 badge badge-featured m-2" style="<?php echo e($listing->status ? 'margin-left: 80px !important;' : ''); ?>">
                                        <i class="bi bi-star-fill me-1"></i>Featured
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="card-title fw-bold mb-0"><?php echo e(Str::limit($listing->title, 25)); ?></h6>
                                    <span class="badge bg-light text-secondary border small">#<?php echo e($listing->unique_id); ?></span>
                                </div>
                                <div class="d-flex flex-wrap gap-2 small text-muted mb-3 mt-2">
                                    <?php if($listing->km_driven): ?>
                                    <span><i class="bi bi-speedometer2 me-1"></i><?php echo e(number_format($listing->km_driven)); ?> km</span>
                                    <?php endif; ?>
                                    <span><i class="bi bi-gear me-1"></i><?php echo e(ucfirst($listing->transmission ?? 'N/A')); ?></span>
                                    <span><i class="bi bi-fuelPump me-1"></i><?php echo e(ucfirst($listing->fuel_type ?? 'N/A')); ?></span>
                                </div>
                                <h5 class="text-accent fw-bold mb-0">₹<?php echo e(number_format($listing->price ?? 0)); ?></h5>
                                
                                <?php if($listing->isFeatured() && $listing->featured_expires_at): ?>
                                    <div class="small text-warning fw-bold mt-2">
                                        <i class="bi bi-star-fill me-1"></i> Featured till <?php echo e(\Carbon\Carbon::parse($listing->featured_expires_at)->format('d M, Y')); ?>

                                    </div>
                                <?php endif; ?>
                                
                                <?php if($listing->status == 'rejected' && $listing->rejection_reason): ?>
                                    <div class="alert alert-danger mt-3 mb-0 py-2 small">
                                        <strong>Reason:</strong> <?php echo e($listing->rejection_reason); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <div class="d-flex flex-column gap-2">
                                    <?php if($listing->status == 'approved'): ?>
                                        <div class="d-flex w-100 gap-2">
                                            <a href="<?php echo e(route('car.detail', $listing->slug)); ?>" class="btn btn-outline-primary btn-sm flex-grow-1" target="_blank">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <?php if(!$listing->isFeatured()): ?>
                                                <a href="<?php echo e(route('customer.listing.featured-plans', $listing)); ?>" class="btn btn-sm btn-warning text-dark fw-bold flex-grow-1">
                                                    <i class="bi bi-star"></i> Feature
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('customer.listing.featured-plans', $listing)); ?>" class="btn btn-sm btn-outline-warning flex-grow-1">
                                                    <i class="bi bi-star-fill"></i> Extend
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('customer.listing.edit', $listing->id)); ?>" class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="initiateDelete(<?php echo e($listing->id); ?>)">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
                    <i class="bi bi-car-front text-secondary" style="font-size: 5rem;"></i>
                    <h4 class="mt-3 fw-bold">No cars listed yet</h4>
                    <p class="text-muted mb-4">You haven't listed any cars for sale.</p>
                    <a href="<?php echo e(route('sell-car.index')); ?>" class="btn btn-accent px-4 py-2">
                        Sell Your First Car
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- OTP Verification Modal -->
<div class="modal fade" id="deleteOtpModal" tabindex="-1" aria-labelledby="deleteOtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-sm">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteOtpModalLabel"><i class="bi bi-shield-lock text-accent me-2"></i>Security Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-4">To protect your account, please verify your identity to permanently delete this car listing.</p>
                
                <div id="otpStep1">
                    <p class="small fw-semibold mb-3">
                        An OTP will be sent to your registered mobile number<?php echo e($customer->email ? ' and email address' : ''); ?>:
                        <br>+91 <?php echo e($customer->phone); ?>

                        <?php if($customer->email): ?>
                            <br><?php echo e($customer->email); ?>

                        <?php endif; ?>
                    </p>
                    <button type="button" class="btn btn-primary w-100 py-2 fw-bold" id="btnSendDeleteOtp">
                        Send OTP
                    </button>
                    <div id="sendOtpMessage" class="small mt-2 text-danger"></div>
                </div>

                <div id="otpStep2" class="d-none">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Enter 6-digit OTP</label>
                        <input type="text" id="delete_otp_input" class="form-control text-center fs-4 letter-spacing-2" placeholder="------" maxlength="6">
                    </div>
                    <button type="button" class="btn btn-danger w-100 py-2 fw-bold" id="btnVerifyDeleteOtp">
                        Verify & Delete
                    </button>
                    <div id="verifyOtpMessage" class="small mt-2 text-danger"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-link btn-sm text-muted text-decoration-none" id="btnResendDeleteOtp">Resend OTP</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentDeleteId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteOtpModal'));

        window.initiateDelete = function(listingId) {
            currentDeleteId = listingId;
            document.getElementById('otpStep1').classList.remove('d-none');
            document.getElementById('otpStep2').classList.add('d-none');
            document.getElementById('delete_otp_input').value = '';
            document.getElementById('sendOtpMessage').textContent = '';
            document.getElementById('verifyOtpMessage').textContent = '';
            deleteModal.show();
        };

        document.getElementById('btnSendDeleteOtp').addEventListener('click', sendDeleteOtp);
        document.getElementById('btnResendDeleteOtp').addEventListener('click', sendDeleteOtp);

        function sendDeleteOtp() {
            const btn = document.getElementById('btnSendDeleteOtp');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
            document.getElementById('sendOtpMessage').textContent = '';

            fetch("<?php echo e(route('customer.listing.delete.otp')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Send OTP';
                if (data.success) {
                    document.getElementById('otpStep1').classList.add('d-none');
                    document.getElementById('otpStep2').classList.remove('d-none');
                    document.getElementById('delete_otp_input').focus();
                } else {
                    document.getElementById('sendOtpMessage').textContent = data.message || 'Failed to send OTP.';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Send OTP';
                document.getElementById('sendOtpMessage').textContent = 'An error occurred. Please try again.';
            });
        }

        document.getElementById('btnVerifyDeleteOtp').addEventListener('click', function() {
            const otp = document.getElementById('delete_otp_input').value.trim();
            const msg = document.getElementById('verifyOtpMessage');
            
            if(!/^[0-9]{6}$/.test(otp)) {
                msg.textContent = 'Please enter a valid 6-digit OTP.';
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verifying...';
            msg.textContent = '';

            fetch(`/customer/listing/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ otp: otp })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    deleteModal.hide();
                    window.location.reload();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Verify & Delete';
                    msg.textContent = data.message || 'Invalid OTP';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Verify & Delete';
                msg.textContent = 'An error occurred. Please try again.';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/dashboard.blade.php ENDPATH**/ ?>