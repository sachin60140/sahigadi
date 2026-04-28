<?php $__env->startSection('title', 'Customer Car Listings - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person-badge me-2"></i>Customer Car Listings</h4>
        <small class="text-muted">Manage individual seller listings</small>
        <?php if($pendingCount > 0): ?>
            <span class="badge bg-warning text-dark ms-2"><?php echo e($pendingCount); ?> pending</span>
        <?php endif; ?>
    </div>
</div>

<div class="stat-card mb-4">
    <form action="<?php echo e(route('admin.customer-listings.index')); ?>" method="GET" class="row g-3">
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by title, model, phone, email..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Filter</button>
            <a href="<?php echo e(route('admin.customer-listings.index')); ?>" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="table-modern">
    <table class="table table-modern mb-0">
        <thead>
            <tr>
                <th><i class="bi bi-car me-1"></i>Car</th>
                <th><i class="bi bi-person me-1"></i>Owner</th>
                <th><i class="bi bi-currency-rupee me-1"></i>Price</th>
                <th><i class="bi bi-star me-1"></i>Featured</th>
                <th><i class="bi bi-geo-alt me-1"></i>Location</th>
                <th><i class="bi bi-info-circle me-1"></i>Status</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <strong><?php echo e(Str::limit($listing->title, 30)); ?></strong>
                    <br>
                    <small class="text-muted">
                        <?php if($listing->brand): ?>
                            <?php echo e($listing->brand->name); ?> |
                        <?php endif; ?>
                        <?php echo e($listing->year ?? 'N/A'); ?>

                    </small>
                </td>
                <td>
                    <?php echo e($listing->owner_name ?? 'N/A'); ?>

                </td>
                <td class="fw-bold">₹<?php echo e(number_format($listing->price ?? 0)); ?></td>
                <td>
                    <?php if($listing->isFeatured()): ?>
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-star-fill me-1"></i>Featured</span>
                        <form action="<?php echo e(route('admin.customer-listings.remove-featured', $listing->slug)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Remove Featured">
                                <i class="bi bi-star"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-star"></i>
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
                </td>
                <td>
                    <?php if($listing->latitude && $listing->longitude): ?>
                        <a href="https://www.google.com/maps?q=<?php echo e($listing->latitude); ?>,<?php echo e($listing->longitude); ?>" target="_blank" class="btn btn-sm btn-outline-success" title="View on Google Maps">
                            <i class="bi bi-geo-alt"></i>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($listing->status === 'approved'): ?>
                        <span class="badge bg-success badge-modern"><i class="bi bi-check-circle me-1"></i>Approved</span>
                    <?php elseif($listing->status === 'pending'): ?>
                        <span class="badge bg-warning text-dark badge-modern"><i class="bi bi-clock me-1"></i>Pending</span>
                    <?php else: ?>
                        <span class="badge bg-danger badge-modern" title="<?php echo e($listing->rejection_reason); ?>"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(url('admin/customer-listings/' . $listing->slug)); ?>" class="btn btn-sm btn-outline-primary me-1" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?php echo e(route('admin.customer-listings.edit', $listing->slug)); ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php if($listing->status === 'pending'): ?>
                        <form action="<?php echo e(url('admin/customer-listings/' . $listing->slug . '/approve')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-car-front" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-2 text-muted">No customer listings found</h5>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($listings->hasPages()): ?>
<div class="d-flex justify-content-center mt-4">
    <?php echo e($listings->withQueryString()->links()); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\customer-listings\index.blade.php ENDPATH**/ ?>