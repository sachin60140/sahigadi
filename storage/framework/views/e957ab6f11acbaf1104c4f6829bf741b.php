<?php $__env->startSection('title', 'Edit Dealer - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-gear me-2"></i>Edit Dealer</h4>
        <small class="text-muted">Update dealer information and documents</small>
    </div>
    <a href="<?php echo e(route('admin.dealers.show', $dealer)); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="stat-card">
            <form action="<?php echo e(route('admin.dealers.update', $dealer)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <h5 class="mb-3"><i class="bi bi-person me-2"></i>Basic Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $dealer->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
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
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $dealer->email)); ?>" required>
                        <?php $__errorArgs = ['email'];
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
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $dealer->phone)); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="<?php echo e(old('company_name', $dealer->company_name)); ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Full Address</label>
                        <textarea name="address" rows="2" class="form-control"><?php echo e(old('address', $dealer->address)); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo e(old('city', $dealer->city)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?php echo e(old('state', $dealer->state)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="<?php echo e(old('pincode', $dealer->pincode)); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['password'];
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
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="pending" <?php echo e(old('status', $dealer->status) == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="approved" <?php echo e(old('status', $dealer->status) == 'approved' ? 'selected' : ''); ?>>Approved</option>
                            <option value="rejected" <?php echo e(old('status', $dealer->status) == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                        </select>
                        <?php $__errorArgs = ['status'];
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

                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>KYC Documents</h5>
                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Aadhaar Card</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Aadhaar Number</label>
                        <input type="text" name="kyc_document_number" class="form-control" value="<?php echo e(old('kyc_document_number', $dealer->kyc_document_number)); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Update Aadhaar Document (PDF, JPG, PNG - Max 5MB)</label>
                        <?php if($dealer->kyc_document_path): ?>
                        <div class="mb-2">
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current document uploaded</span>
                            <a href="<?php echo e(asset('storage/'.$dealer->kyc_document_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye me-1"></i>View Current
                            </a>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="kyc_document" class="form-control <?php $__errorArgs = ['kyc_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf,.jpg,.jpeg,.png">
                        <?php $__errorArgs = ['kyc_document'];
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

                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">PAN Card</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" style="text-transform:uppercase" class="form-control" value="<?php echo e(old('pan_number', $dealer->pan_number)); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Update PAN Document (PDF, JPG, PNG - Max 5MB)</label>
                        <?php if($dealer->pan_document_path): ?>
                        <div class="mb-2">
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current document uploaded</span>
                            <a href="<?php echo e(asset('storage/'.$dealer->pan_document_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye me-1"></i>View Current
                            </a>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="pan_document" class="form-control <?php $__errorArgs = ['pan_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf,.jpg,.jpeg,.png">
                        <?php $__errorArgs = ['pan_document'];
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

                <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>GST Document</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number" class="form-control" value="<?php echo e(old('gst_number', $dealer->gst_number)); ?>" maxlength="15">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload GST Document (PDF, JPG, PNG - Max 5MB)</label>
                        <?php if($dealer->gst_document_path): ?>
                        <div class="mb-2">
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current document uploaded</span>
                            <a href="<?php echo e(asset('storage/'.$dealer->gst_document_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye me-1"></i>View Current
                            </a>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="gst_document" class="form-control <?php $__errorArgs = ['gst_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf,.jpg,.jpeg,.png">
                        <?php $__errorArgs = ['gst_document'];
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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update Dealer
                    </button>
                    <a href="<?php echo e(route('admin.dealers.show', $dealer)); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/dealers/edit.blade.php ENDPATH**/ ?>