<?php $__env->startSection('title', 'Contact Enquiries - Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">Contact Enquiries</h3>
            <p class="text-muted mb-0">Manage customer messages and contact form submissions.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Sender Name</th>
                            <th class="px-4 py-3">Email Address</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e(!$enquiry->is_read ? 'fw-bold bg-light bg-opacity-50' : ''); ?>">
                            <td class="px-4 py-3"><?php echo e($enquiry->id); ?></td>
                            <td class="px-4 py-3">
                                <?php if($enquiry->is_read): ?>
                                    <span class="badge bg-secondary">Read</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Unread</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3"><?php echo e($enquiry->name); ?></td>
                            <td class="px-4 py-3">
                                <a href="mailto:<?php echo e($enquiry->email); ?>" class="text-decoration-none"><?php echo e($enquiry->email); ?></a>
                            </td>
                            <td class="px-4 py-3 text-truncate" style="max-width: 250px;">
                                <?php echo e($enquiry->subject); ?>

                            </td>
                            <td class="px-4 py-3 text-muted small">
                                <?php echo e($enquiry->created_at->format('M d, Y h:i A')); ?>

                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="<?php echo e(route('admin.contact-enquiries.show', $enquiry->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <?php if(!$enquiry->is_read): ?>
                                <form action="<?php echo e(route('admin.contact-enquiries.read', $enquiry->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check-all"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form action="<?php echo e(route('admin.contact-enquiries.destroy', $enquiry->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this enquiry?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                                    <h5>No Enquiries Found</h5>
                                    <p>There are no messages from the contact form yet.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($enquiries->hasPages()): ?>
            <div class="p-4 border-top">
                <?php echo e($enquiries->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\contact-enquiries\index.blade.php ENDPATH**/ ?>