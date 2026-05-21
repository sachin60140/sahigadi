<?php $__env->startSection('title', 'Customer Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person me-2"></i><?php echo e($customer->name); ?></h4>
        <small class="text-muted"><?php echo e($customer->email); ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.customers.edit', $customer)); ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Customer
        </a>
        <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Name</td>
                            <td><?php echo e($customer->name); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td><?php echo e($customer->email); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td><?php echo e($customer->phone); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">WhatsApp</td>
                            <td><?php echo e($customer->whatsapp_number ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Company</td>
                            <td><?php echo e($customer->company_name ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">City</td>
                            <td><?php echo e($customer->city ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">State</td>
                            <td><?php echo e($customer->state ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pincode</td>
                            <td><?php echo e($customer->pincode ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Full Address</td>
                            <td><?php echo e($customer->address ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">GST Number</td>
                            <td><?php echo e($customer->gst_number ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wallet Balance</td>
                            <td class="text-success fw-bold">₹<?php echo e(number_format($customer->wallet->balance ?? 0, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Aadhaar Number</td>
                            <td><?php echo e($customer->aadhaar_number ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">PAN Number</td>
                            <td><?php echo e($customer->pan_number ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Gender</td>
                            <td><?php echo e(ucfirst($customer->gender ?? 'N/A')); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date of Birth</td>
                            <td><?php echo e($customer->dob ? $customer->dob->format('d M Y') : 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Profile Completion</td>
                            <td>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar <?php echo e($customer->profile_completion_percentage >= 75 ? 'bg-success' : 'bg-warning'); ?>" role="progressbar" style="width: <?php echo e($customer->profile_completion_percentage); ?>%;" aria-valuenow="<?php echo e($customer->profile_completion_percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted"><?php echo e($customer->profile_completion_percentage); ?>% Completed</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Joined On</td>
                            <td><?php echo e($customer->created_at->format('d M Y')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Car Listings (<?php echo e($customer->listings->count()); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if($customer->listings->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Reg. Year</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $customer->listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($listing->brand->name ?? 'Unknown'); ?> <?php echo e($listing->model); ?></strong>
                                    <br><small class="text-muted"><?php echo e(ucfirst($listing->transmission)); ?> (<?php echo e($listing->fuel_type); ?>)</small>
                                </td>
                                <td><?php echo e($listing->year); ?></td>
                                <td>₹<?php echo e(number_format($listing->price ?? 0)); ?></td>
                                <td>
                                    <?php if($listing->status === 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif($listing->status === 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0">No cars listed.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if($customer->wallet && $customer->wallet->transactions->count() > 0): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Wallet Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $customer->wallet->transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($txn->created_at->format('d M Y, h:i A')); ?></td>
                                <td>
                                    <?php if($txn->type === 'credit'): ?>
                                        <span class="badge bg-success">Credit</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Debit</span>
                                    <?php endif; ?>
                                </td>
                                <td class="<?php echo e($txn->type === 'credit' ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e($txn->type === 'credit' ? '+' : '-'); ?>₹<?php echo e(number_format($txn->amount, 2)); ?>

                                </td>
                                <td><?php echo e($txn->remark ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/customers/show.blade.php ENDPATH**/ ?>