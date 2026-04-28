<?php $__env->startSection('title', 'View Enquiry - Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">View Enquiry Details</h3>
            <p class="text-muted mb-0">Reviewing contact submission message #<?php echo e($contact_enquiry->id); ?></p>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?php echo e(route('admin.contact-enquiries.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <a href="mailto:<?php echo e($contact_enquiry->email); ?>?subject=RE: <?php echo e(urlencode($contact_enquiry->subject)); ?>" class="btn btn-primary">
                <i class="bi bi-envelope"></i> Reply via Email
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Message Content</h5>
                        <span class="text-muted small">Received on <?php echo e($contact_enquiry->created_at->format('l, F j, Y \a\t g:i A')); ?> (<?php echo e($contact_enquiry->created_at->diffForHumans()); ?>)</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 border-bottom pb-3">Subject: <span class="text-primary"><?php echo e($contact_enquiry->subject); ?></span></h4>
                    
                    <div class="bg-light rounded p-4 mb-0" style="white-space: pre-wrap; font-size: 1.05rem; line-height: 1.6;"><?php echo e($contact_enquiry->message); ?></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="mb-0 fw-bold">Sender Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center text-uppercase fw-bold fs-3 me-3" style="width: 60px; height: 60px;">
                            <?php echo e(substr($contact_enquiry->name, 0, 1)); ?>

                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold"><?php echo e($contact_enquiry->name); ?></h5>
                            <span class="badge bg-<?php echo e($contact_enquiry->is_read ? 'secondary' : 'success'); ?>">
                                <?php echo e($contact_enquiry->is_read ? 'Status: Read' : 'Status: Unread'); ?>

                            </span>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                            <span class="text-muted"><i class="bi bi-envelope me-2"></i>Email</span>
                            <strong><a href="mailto:<?php echo e($contact_enquiry->email); ?>" class="text-decoration-none"><?php echo e($contact_enquiry->email); ?></a></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                            <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>Received Date</span>
                            <strong><?php echo e($contact_enquiry->created_at->format('M d, Y')); ?></strong>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white p-3 text-center border-top">
                    <form action="<?php echo e(route('admin.contact-enquiries.destroy', $contact_enquiry->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this specific enquiry permanently?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> Delete Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\contact-enquiries\show.blade.php ENDPATH**/ ?>