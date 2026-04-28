<?php $__env->startSection('title', 'Edit Listing - ' . $listing->title . ' - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-pencil me-2"></i>Edit Listing</h4>
        <small class="text-muted">Update customer car listing</small>
    </div>
    <a href="<?php echo e(route('admin.customer-listings.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?php echo e(route('admin.customer-listings.update', $listing)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?php echo e($listing->title); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">Select Brand</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->id); ?>" <?php echo e($listing->brand_id == $brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="form-control" value="<?php echo e($listing->model); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" value="<?php echo e($listing->year); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fuel Type</label>
                    <select name="fuel_type" class="form-select">
                        <option value="">Select</option>
                        <option value="petrol" <?php echo e($listing->fuel_type == 'petrol' ? 'selected' : ''); ?>>Petrol</option>
                        <option value="diesel" <?php echo e($listing->fuel_type == 'diesel' ? 'selected' : ''); ?>>Diesel</option>
                        <option value="electric" <?php echo e($listing->fuel_type == 'electric' ? 'selected' : ''); ?>>Electric</option>
                        <option value="hybrid" <?php echo e($listing->fuel_type == 'hybrid' ? 'selected' : ''); ?>>Hybrid</option>
                        <option value="cng" <?php echo e($listing->fuel_type == 'cng' ? 'selected' : ''); ?>>CNG</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transmission</label>
                    <select name="transmission" class="form-select">
                        <option value="">Select</option>
                        <option value="manual" <?php echo e($listing->transmission == 'manual' ? 'selected' : ''); ?>>Manual</option>
                        <option value="automatic" <?php echo e($listing->transmission == 'automatic' ? 'selected' : ''); ?>>Automatic</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" value="<?php echo e($listing->price); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?php echo e($listing->city); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">KM Driven</label>
                    <input type="number" name="km_driven" class="form-control" value="<?php echo e($listing->km_driven); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" value="<?php echo e($listing->registration_number); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Number of Owners</label>
                    <input type="number" name="owners" class="form-control" value="<?php echo e($listing->owners); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Phone <span class="text-danger">*</span></label>
                    <input type="text" name="owner_phone" class="form-control" value="<?php echo e($listing->owner_phone); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" value="<?php echo e($listing->owner_name); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="<?php echo e($listing->whatsapp_number); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" <?php echo e($listing->status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="approved" <?php echo e($listing->status == 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="rejected" <?php echo e($listing->status == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                    </select>
                </div>
            </div>

            <?php
                $images = json_decode($listing->images, true) ?? [];
            ?>
            
            <div class="mt-4 border-top pt-4">
                <h6 class="mb-3">Car Images (Select Featured Image)</h6>
                <div class="mb-3">
                    <label class="form-label">Add More Images</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                
                <?php if(count($images) > 0): ?>
                <div class="d-flex flex-wrap gap-3">
                    <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="position-relative border p-2 rounded <?php echo e($index === 0 ? 'bg-primary-subtle border-primary' : 'bg-light'); ?>">
                        <a href="<?php echo e(asset('storage/' . $image)); ?>" target="_blank">
                            <img src="<?php echo e(asset('storage/' . $image)); ?>" class="rounded" style="width: 120px; height: 90px; object-fit: cover;">
                        </a>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <div class="form-check d-inline-block mb-0">
                                <input class="form-check-input" type="radio" name="primary_image" id="img<?php echo e($index); ?>" value="<?php echo e($image); ?>" <?php echo e($index === 0 ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="img<?php echo e($index); ?>">Featured</label>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <small class="text-muted d-block mt-2">The selected image will be shown as the main thumbnail. Save the listing to apply the new featured image.</small>
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Listing
                </button>
            </div>
        </form>

        <?php if(count($images) > 0): ?>
        <div class="mt-4 pt-4 border-top">
            <h6 class="mb-3 text-danger">Delete Images</h6>
            <div class="d-flex flex-wrap gap-3">
                <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="position-relative border p-2 rounded bg-light">
                    <img src="<?php echo e(asset('storage/' . $image)); ?>" class="rounded" style="width: 80px; height: 60px; object-fit: cover; opacity: 0.8;">
                    <form action="<?php echo e(route('admin.customer-listings.image.delete', $listing)); ?>" method="POST" class="mt-2 text-center" onsubmit="return confirm('Are you sure you want to delete this image?')">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="image" value="<?php echo e($image); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i> Delete</button>
                    </form>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>


    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\customer-listings\edit.blade.php ENDPATH**/ ?>