<?php $__env->startSection('title', 'View Listing - ' . $listing->title . ' - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <a href="<?php echo e(route('admin.customer-listings.index')); ?>" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <h4><i class="bi bi-car-front-fill me-2"></i><?php echo e(Str::limit($listing->title, 40)); ?></h4>
        <small class="text-muted">
            Listed on <?php echo e($listing->created_at ? $listing->created_at->format('d M Y, h:i A') : 'N/A'); ?>

        </small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if($listing->status === 'pending'): ?>
            <form action="<?php echo e(url('admin/customer-listings/' . $listing->slug . '/approve')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Approve
                </button>
            </form>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-2"></i>Reject
            </button>
        <?php elseif($listing->status === 'approved'): ?>
            <span class="badge bg-success p-2"><i class="bi bi-check-circle me-1"></i>Approved</span>
        <?php else: ?>
            <span class="badge bg-danger p-2"><i class="bi bi-x-circle me-1"></i>Rejected</span>
        <?php endif; ?>
        
        <?php if($listing->isFeatured()): ?>
            <form action="<?php echo e(route('admin.customer-listings.remove-featured', $listing->slug)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-warning">
                    <i class="bi bi-star-fill me-2"></i>Remove Featured
                </button>
            </form>
        <?php else: ?>
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-star me-2"></i>Make Featured
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="<?php echo e(route('admin.customer-listings.featured', $listing->slug)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="days" value="7">
                            <button type="submit" class="dropdown-item">7 Days</button>
                        </form>
                    </li>
                    <li>
                        <form action="<?php echo e(route('admin.customer-listings.featured', $listing->slug)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="days" value="14">
                            <button type="submit" class="dropdown-item">14 Days</button>
                        </form>
                    </li>
                    <li>
                        <form action="<?php echo e(route('admin.customer-listings.featured', $listing->slug)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="days" value="30">
                            <button type="submit" class="dropdown-item">30 Days</button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-car-front me-2 text-danger"></i>Car Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Title</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->title); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Brand</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->brand->name ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Model</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->model ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Year</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->year ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Fuel Type</small>
                            <p class="mb-0 fw-semibold text-capitalize"><?php echo e($listing->fuel_type ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Transmission</small>
                            <p class="mb-0 fw-semibold text-capitalize"><?php echo e($listing->transmission ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">KM Driven</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->km_driven ? number_format($listing->km_driven) . ' km' : 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Price</small>
                            <p class="mb-0 fw-semibold text-danger">₹<?php echo e(number_format($listing->price ?? 0)); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">City</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->city ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <?php if($listing->latitude && $listing->longitude): ?>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Location</small>
                            <p class="mb-0 fw-semibold">
                                <?php echo e($listing->latitude); ?>, <?php echo e($listing->longitude); ?>

                                <a href="https://www.google.com/maps?q=<?php echo e($listing->latitude); ?>,<?php echo e($listing->longitude); ?>" target="_blank" class="btn btn-sm btn-outline-success ms-2">
                                    <i class="bi bi-geo-alt"></i> View on Map
                                </a>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Registration Number</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->registration_number ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted">Number of Owners</small>
                            <p class="mb-0 fw-semibold"><?php echo e($listing->owners ?? 1); ?><?php echo e($listing->owners == 1 ? 'st' : ($listing->owners == 2 ? 'nd' : 'th')); ?> Owner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($listing->images): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-images me-2 text-danger"></i>Car Images</h5>
            </div>
            <div class="card-body">
                <?php
                    $images = json_decode($listing->images, true) ?? [];
                ?>
                <?php if(count($images) > 0): ?>
                    <div class="row g-2">
                        <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-4 col-md-3 col-lg-2">
                                <a href="<?php echo e(asset('storage/' . $image)); ?>" target="_blank">
                                    <img src="<?php echo e(asset('storage/' . $image)); ?>" class="img-thumbnail" style="height: 120px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <small class="text-muted mt-2 d-block"><?php echo e(count($images)); ?> images uploaded</small>
                <?php else: ?>
                    <p class="text-muted mb-0">No images uploaded</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($listing->status === 'rejected' && $listing->rejection_reason): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Rejection Reason:</strong> <?php echo e($listing->rejection_reason); ?>

        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-person me-2 text-danger"></i>Owner Details</h5>
            </div>
            <div class="card-body">
                <div class="detail-item mb-3">
                    <small class="text-muted">Name</small>
                    <p class="mb-0 fw-semibold"><?php echo e($listing->owner_name ?? 'N/A'); ?></p>
                </div>
                <div class="detail-item mb-3">
                    <small class="text-muted">Phone</small>
                    <p class="mb-0">
                        <a href="tel:<?php echo e($listing->owner_phone); ?>" class="fw-semibold text-success">
                            <i class="bi bi-telephone me-1"></i><?php echo e($listing->owner_phone); ?>

                        </a>
                    </p>
                </div>
                <?php if($listing->whatsapp_number): ?>
                <div class="detail-item">
                    <small class="text-muted">WhatsApp</small>
                    <p class="mb-0">
                        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $listing->whatsapp_number)); ?>" target="_blank" class="fw-semibold text-success">
                            <i class="bi bi-whatsapp me-1"></i><?php echo e($listing->whatsapp_number); ?>

                        </a>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-danger"></i>Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <?php if($listing->status === 'approved'): ?>
                        <span class="badge bg-success w-100 py-2"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    <?php elseif($listing->status === 'pending'): ?>
                        <span class="badge bg-warning text-dark w-100 py-2"><i class="bi bi-clock me-1"></i>Pending Review</span>
                    <?php else: ?>
                        <span class="badge bg-danger w-100 py-2"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    <?php endif; ?>
                </div>
                <hr>
                <form action="<?php echo e(url('admin/customer-listings/' . $listing->slug)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-2"></i>Delete Listing
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(url('admin/customer-listings/' . $listing->slug . '/reject')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control <?php $__errorArgs = ['rejection_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  rows="3" required placeholder="Please explain why this listing is being rejected..."><?php echo e(old('rejection_reason')); ?></textarea>
                        <?php $__errorArgs = ['rejection_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/customer-listings/show.blade.php ENDPATH**/ ?>