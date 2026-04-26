<?php $__env->startSection('title', 'Enquiries'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Enquiries</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('dealer.enquiries.index')); ?>" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">All Status</option>
                    <option value="new" <?php echo e(request('status') == 'new' ? 'selected' : ''); ?>>New</option>
                    <option value="contacted" <?php echo e(request('status') == 'contacted' ? 'selected' : ''); ?>>Contacted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?php echo e(route('dealer.enquiries.index')); ?>" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Car</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($enquiry->created_at->format('d M Y')); ?></td>
                        <td><?php echo e($enquiry->customer_name); ?></td>
                        <td>
                            <a href="tel:<?php echo e($enquiry->customer_phone); ?>"><?php echo e($enquiry->customer_phone); ?></a>
                            <?php if($enquiry->customer_email): ?>
                            <br><small class="text-muted"><?php echo e($enquiry->customer_email); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><a href="<?php echo e(route('car.detail', $enquiry->car->slug ?? '#')); ?>"><?php echo e(Str::limit($enquiry->car->title ?? 'N/A', 25)); ?></a></td>
                        <td>
                            <?php if($enquiry->status === 'new'): ?>
                                <span class="badge bg-danger">New</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Contacted</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('dealer.enquiries.show', $enquiry)); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $enquiry->customer_phone)); ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No enquiries found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($enquiries->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/enquiries/index.blade.php ENDPATH**/ ?>