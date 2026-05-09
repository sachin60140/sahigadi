<?php $__env->startSection('title', 'My Enquiries - SAHI GADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h2 class="fw-bold mb-1">My Enquiries</h2>
                <p class="text-muted mb-0">View all customer enquiries for your listed cars</p>
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
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <?php if($enquiries->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-chat-square-text text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold">No Enquiries Yet</h5>
                            <p class="text-muted">When customers unlock contact details for your cars, they will appear here.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Car</th>
                                        <th>Customer Details</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-muted small">
                                                <?php echo e($enquiry->created_at->format('d M Y')); ?><br>
                                                <?php echo e($enquiry->created_at->format('h:i A')); ?>

                                            </td>
                                            <td>
                                                <?php if($enquiry->customerCar): ?>
                                                    <a href="<?php echo e(route('car.detail', $enquiry->customerCar->slug)); ?>" target="_blank" class="text-decoration-none fw-bold text-dark">
                                                        <?php echo e(\Illuminate\Support\Str::limit($enquiry->customerCar->title, 30)); ?>

                                                    </a>
                                                    <br>
                                                    <span class="badge bg-light text-secondary border small mb-1">#<?php echo e($enquiry->customerCar->unique_id); ?></span>
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-calendar-event"></i> <?php echo e($enquiry->customerCar->year); ?> | 
                                                        <span class="text-uppercase"><i class="bi bi-card-text"></i> <?php echo e($enquiry->customerCar->registration_number); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-danger">Car Deleted</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?php echo e($enquiry->customer_name ?? 'Customer'); ?></div>
                                                <div><a href="tel:<?php echo e($enquiry->customer_phone); ?>" class="text-decoration-none"><i class="bi bi-telephone-fill small me-1 text-muted"></i><?php echo e($enquiry->customer_phone); ?></a></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Contact Unlocked
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($enquiries->hasPages()): ?>
                            <div class="mt-4 d-flex justify-content-center">
                                <?php echo e($enquiries->links('pagination::bootstrap-5')); ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/enquiries.blade.php ENDPATH**/ ?>