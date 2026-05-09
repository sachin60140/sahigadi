<?php $__env->startSection('title', 'Dealer Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar">
    <div>
        <h4><i class="bi bi-person me-2"></i><?php echo e($dealer->name); ?></h4>
        <small class="text-muted"><?php echo e($dealer->email); ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.dealers.edit', $dealer)); ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Dealer
        </a>
        <a href="<?php echo e(route('admin.dealers.index')); ?>" class="btn btn-outline-secondary">
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
                            <td><?php echo e($dealer->name); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td><?php echo e($dealer->email); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td><?php echo e($dealer->phone); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Company</td>
                            <td><?php echo e($dealer->company_name ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">City</td>
                            <td><?php echo e($dealer->city ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">State</td>
                            <td><?php echo e($dealer->state ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pincode</td>
                            <td><?php echo e($dealer->pincode ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Full Address</td>
                            <td><?php echo e($dealer->address ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <?php if($dealer->status === 'approved'): ?>
                                    <span class="badge bg-success badge-modern">Approved</span>
                                <?php elseif($dealer->status === 'pending'): ?>
                                    <span class="badge bg-warning badge-modern">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-danger badge-modern">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wallet Balance</td>
                            <td class="text-success fw-bold">₹<?php echo e(number_format($dealer->wallet->balance ?? 0, 2)); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">KYC Documents</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Document Type</td>
                            <td><strong>Aadhaar Card</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Aadhaar Number</td>
                            <td><strong><?php echo e($dealer->kyc_document_number ?? 'N/A'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Aadhaar File</td>
                            <td>
                                <?php if($dealer->kyc_document_path): ?>
                                    <a href="<?php echo e(asset('storage/'.$dealer->kyc_document_path)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-text me-1"></i>View Document
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Not uploaded</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr class="my-2"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">PAN Number</td>
                            <td><strong><?php echo e($dealer->pan_number ?? 'N/A'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">PAN Document File</td>
                            <td>
                                <?php if($dealer->pan_document_path): ?>
                                    <a href="<?php echo e(asset('storage/'.$dealer->pan_document_path)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-text me-1"></i>View Document
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Not uploaded</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <?php if($dealer->gst_number): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">GST Documents</h5>
                <?php if($dealer->gst_document_path && !$dealer->gst_verified): ?>
                    <form action="<?php echo e(route('admin.dealers.verify-gst', $dealer)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i>Verify GST
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">GST Number</td>
                            <td>
                                <strong><?php echo e($dealer->gst_number); ?></strong>
                                <?php if($dealer->gst_verified): ?>
                                    <span class="badge bg-success ms-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Verification Date</td>
                            <td><?php echo e($dealer->gst_verified_at ? $dealer->gst_verified_at->format('d M Y') : 'Not verified'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Document File</td>
                            <td>
                                <?php if($dealer->gst_document_path): ?>
                                    <a href="<?php echo e(asset('storage/'.$dealer->gst_document_path)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-text me-1"></i>View GST
                                    </a>
                                    <?php if($dealer->gst_verified): ?>
                                        <form action="<?php echo e(route('admin.dealers.unverify-gst', $dealer)); ?>" method="POST" class="d-inline ms-2">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove GST verification?')">
                                                <i class="bi bi-x-circle me-1"></i>Unverify
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Not uploaded</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <?php if($dealer->status === 'pending'): ?>
                <div class="row">
                    <div class="col-md-6">
                        <form action="<?php echo e(route('admin.dealers.approve', $dealer)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Approve Dealer</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Reject Dealer
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Add Money to Wallet</h6>
                        <form action="<?php echo e(route('admin.dealers.add-money', $dealer)); ?>" method="POST" class="row g-2">
                            <?php echo csrf_field(); ?>
                            <div class="col-8">
                                <input type="number" name="amount" class="form-control" placeholder="Amount" min="1" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="remark" class="form-control" placeholder="Remark (optional)">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100 mt-2"><i class="bi bi-plus-circle"></i> Add Money</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h6>Debit Money from Wallet</h6>
                        <form action="<?php echo e(route('admin.dealers.debit-money', $dealer)); ?>" method="POST" class="row g-2">
                            <?php echo csrf_field(); ?>
                            <div class="col-8">
                                <input type="number" name="amount" class="form-control" placeholder="Amount" min="1" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="remark" class="form-control" placeholder="Remark (optional)">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger w-100 mt-2"><i class="bi bi-dash-circle"></i> Debit Money</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cars (<?php echo e($dealer->cars->count()); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if($dealer->cars->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Car</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $dealer->cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(Str::limit($car->title, 30)); ?></td>
                                <td>₹<?php echo e(number_format($car->price ?? 0)); ?></td>
                                <td>
                                    <?php if($car->status === 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif($car->status === 'pending'): ?>
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

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Subscriptions</h5>
            </div>
            <div class="card-body">
                <?php if($dealer->subscriptions->count() > 0): ?>
                    <?php $__currentLoopData = $dealer->subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong><?php echo e($sub->plan->name); ?></strong> - <?php echo e($sub->getActiveListingsCount()); ?>/<?php echo e($sub->plan->listing_limit); ?> listings (<?php echo e($sub->expires_at->format('d M Y')); ?>)</span>
                        <span class="badge bg-<?php echo e($sub->isActive() ? 'success' : 'secondary'); ?> badge-modern">
                            <?php echo e($sub->isActive() ? 'Active' : 'Expired'); ?>

                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="text-muted mb-0">No active subscription.</p>
                <?php endif; ?>
                <hr>
                <h6 class="mb-3">Assign New Plan</h6>
                <form action="<?php echo e(route('admin.dealers.assign-plan', $dealer)); ?>" method="POST" class="row g-2">
                    <?php echo csrf_field(); ?>
                    <div class="col-8">
                        <select name="plan_id" class="form-select <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Select Plan</option>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($plan->id); ?>">
                                <?php echo e($plan->name); ?> - ₹<?php echo e(number_format($plan->price)); ?> (<?php echo e($plan->listing_limit); ?> listings)
                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Assign Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if($walletTransactions->count() > 0): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Wallet Transactions</h5>
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
                            <?php $__currentLoopData = $walletTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.dealers.reject', $dealer)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for rejection *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Dealer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/dealers/show.blade.php ENDPATH**/ ?>