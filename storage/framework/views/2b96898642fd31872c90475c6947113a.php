<?php $__env->startSection('title', 'Enquiries - SAHI GADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Enquiries</h2>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Filter Enquiries</h5>
        <a href="<?php echo e(route('admin.enquiries.exportExcel', request()->all())); ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
        </a>
    </div>
    <div class="card-body bg-light">
        <form action="<?php echo e(route('admin.enquiries.index')); ?>" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-muted small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or Phone..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="new" <?php echo e(request('status') == 'new' ? 'selected' : ''); ?>>New</option>
                    <option value="contacted" <?php echo e(request('status') == 'contacted' ? 'selected' : ''); ?>>Contacted</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Dealer</label>
                <select name="dealer_id" class="form-select">
                    <option value="">All Dealers</option>
                    <?php $__currentLoopData = $dealers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dealer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dealer->id); ?>" <?php echo e(request('dealer_id') == $dealer->id ? 'selected' : ''); ?>><?php echo e(Str::limit($dealer->name, 20)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" title="Filter"><i class="bi bi-search"></i></button>
                <a href="<?php echo e(route('admin.enquiries.index')); ?>" class="btn btn-outline-secondary w-100 ms-2" title="Clear"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Car</th>
                        <th>Reg No.</th>
                        <th>Listed By</th>
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
                            <?php if($enquiry->actual_car): ?>
                                <?php if($enquiry->dealer_id): ?>
                                    <a href="<?php echo e(route('admin.cars.show', $enquiry->actual_car)); ?>">
                                        <?php echo e(Str::limit($enquiry->actual_car->title, 25)); ?>

                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.customer-listings.show', $enquiry->actual_car)); ?>">
                                        <?php echo e(Str::limit($enquiry->actual_car->title, 25)); ?>

                                    </a>
                                <?php endif; ?>
                                <br><small class="text-muted">₹<?php echo e(number_format($enquiry->actual_car->price ?? 0)); ?></small>
                            <?php else: ?>
                                <span class="text-danger">Car Deleted</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($enquiry->actual_car): ?>
                                <span class="text-uppercase text-nowrap"><i class="bi bi-card-text text-muted"></i> <?php echo e($enquiry->actual_car->registration_number ?? '-'); ?></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($enquiry->dealer_id): ?>
                                <?php if($enquiry->dealer): ?>
                                    <a href="<?php echo e(route('admin.dealers.show', $enquiry->dealer)); ?>" class="fw-bold text-dark text-decoration-none">
                                        <?php echo e($enquiry->dealer->name); ?>

                                    </a>
                                    <br><span class="badge bg-secondary">Dealer</span>
                                <?php else: ?>
                                    <span class="text-danger">Dealer Deleted</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if($enquiry->actual_car && $enquiry->actual_car->owner_name): ?>
                                    <span class="fw-bold text-dark"><?php echo e($enquiry->actual_car->owner_name); ?></span>
                                <?php else: ?>
                                    <span class="fw-bold text-dark">Customer</span>
                                <?php endif; ?>
                                <br><span class="badge bg-info text-dark">Customer</span>
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
                                <form action="<?php echo e(route('admin.enquiries.contacted', $enquiry)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-outline-success" title="Mark as Contacted">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php
                                    $carDetails = $enquiry->actual_car ? " {$enquiry->actual_car->title} (" . ($enquiry->actual_car->year ?? '') . " " . ucfirst($enquiry->actual_car->fuel_type ?? '') . ")" : "";
                                    $waText = urlencode("Hi {$enquiry->customer_name},\n\nThank you for your interest in the{$carDetails} listed on SAHI GADI!\n\nPlease let us know if you need any further details or would like to schedule a visit.");
                                ?>
                                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $enquiry->customer_phone)); ?>?text=<?php echo e($waText); ?>" target="_blank" class="btn btn-outline-success" title="Send WhatsApp Message">
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