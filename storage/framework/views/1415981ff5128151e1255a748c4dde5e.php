<?php $__env->startSection('title', 'Vahan Details (RC Search) - SAHI GADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <?php if(auth('customer')->check()): ?>
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h3 class="fw-bold mb-0">RC Search Details</h3>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                Wallet Balance: ₹<?php echo e(number_format(auth('customer')->user()->wallet->balance ?? 0, 2)); ?>

            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="row <?php echo e(!auth('customer')->check() ? 'justify-content-center' : ''); ?>">
        <?php if(auth('customer')->check()): ?>
            <div class="col-lg-3 d-none d-lg-block">
                <?php echo $__env->make('frontend.customer.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endif; ?>

        <div class="<?php echo e(auth('customer')->check() ? 'col-lg-9' : 'col-lg-8'); ?>">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <?php if(!auth('customer')->check()): ?>
                    <div class="text-center mb-4">
                        <i class="bi bi-car-front-fill" style="font-size: 4rem; color: var(--accent);"></i>
                        <h2 class="fw-bold mt-3">Vahan Details (RC Search)</h2>
                        <p class="text-muted">Get complete vehicle registration details from Vahan database</p>
                    </div>
                    <?php else: ?>
                    <div class="mb-4">
                        <h4 class="fw-bold text-primary"><i class="bi bi-search me-2"></i>New RC Search</h4>
                        <p class="text-muted mb-0">Get complete vehicle registration details from Vahan database</p>
                    </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('vehicle-search.search')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Registration Number <span class="text-danger">*</span></label>
                            <input type="text" name="registration_number" class="form-control" placeholder="e.g. BR01AB1234" required>
                            <small class="text-muted">Enter your vehicle registration number</small>
                        </div>

                        <?php if(!auth('customer')->check()): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email Address <span class="text-muted">(Optional)</span></label>
                            <input type="email" name="customer_email" class="form-control" placeholder="Enter email address">
                        </div>
                        <?php endif; ?>

                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Price: ₹<?php echo e(number_format($charge, 0)); ?></strong> per report
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 py-3">
                            <i class="bi bi-search me-2"></i>Search Vehicle Details
                        </button>
                    </form>
                </div>
            </div>

            <?php if(auth('customer')->check() && isset($history)): ?>
            <div class="card border-0 shadow-lg mt-4 rounded-4">
                <div class="card-header bg-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Your Search History</h5>
                </div>
                <div class="card-body p-4">
                    <?php if($history->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Registration Number</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($record->created_at->format('d M Y, h:i A')); ?></td>
                                            <td class="text-uppercase fw-bold"><?php echo e($record->registration_number); ?></td>
                                            <td>
                                                <?php if($record->is_success): ?>
                                                    <span class="badge bg-success">Success</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($record->is_success): ?>
                                                    <div class="btn-group">
                                                        <a href="<?php echo e(route('vehicle-search.show', $record->id)); ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                        <a href="<?php echo e(route('vehicle-search.pdf', $record->id)); ?>" class="btn btn-sm btn-outline-danger" title="Download PDF">
                                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">No search history found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/vehicle-search/index.blade.php ENDPATH**/ ?>