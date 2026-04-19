<?php $__env->startSection('title', 'Enquiries - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Enquiries</h2>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="<?php echo e(route('admin.enquiries.index')); ?>" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="new" <?php echo e(request('status') == 'new' ? 'selected' : ''); ?>>New</option>
                    <option value="contacted" <?php echo e(request('status') == 'contacted' ? 'selected' : ''); ?>>Contacted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?php echo e(route('admin.enquiries.index')); ?>" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Car</th>
                        <th>Dealer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($enquiry->created_at->format('d M Y, h:i A')); ?></td>
                        <td>
                            <strong><?php echo e($enquiry->customer_name); ?></strong>
                            <?php if($enquiry->customer_email): ?>
                            <br><small class="text-muted"><?php echo e($enquiry->customer_email); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="tel:<?php echo e($enquiry->customer_phone); ?>"><?php echo e($enquiry->customer_phone); ?></a>
                        </td>
                        <td>
                            <?php if($enquiry->car): ?>
                                <a href="<?php echo e(route('admin.cars.show', $enquiry->car)); ?>">
                                    <?php echo e(Str::limit($enquiry->car->title, 25)); ?>

                                </a>
                                <br><small class="text-muted">₹<?php echo e(number_format($enquiry->car->price ?? 0)); ?></small>
                            <?php else: ?>
                                <span class="text-danger">Car Deleted</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($enquiry->dealer): ?>
                                <a href="<?php echo e(route('admin.dealers.show', $enquiry->dealer)); ?>">
                                    <?php echo e($enquiry->dealer->name); ?>

                                </a>
                            <?php else: ?>
                                <span class="text-danger">Dealer Deleted</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($enquiry->status === 'new'): ?>
                                <span class="badge bg-danger">New</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Contacted</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.enquiries.show', $enquiry)); ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if($enquiry->status === 'new'): ?>
                                <a href="<?php echo e(route('admin.enquiries.contacted', $enquiry)); ?>" class="btn btn-outline-success" title="Mark as Contacted">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                <?php endif; ?>
                                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $enquiry->customer_phone)); ?>" target="_blank" class="btn btn-outline-success">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">No enquiries found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($enquiries->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/enquiries/index.blade.php ENDPATH**/ ?>