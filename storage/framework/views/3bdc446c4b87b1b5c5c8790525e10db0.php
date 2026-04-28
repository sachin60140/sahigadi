<?php $__env->startSection('title', 'Wallet Recharges'); ?>

<?php $__env->startSection('content'); ?>
<div class="top-bar flex-wrap gap-3">
    <div>
        <h4><i class="bi bi-cash-stack text-primary me-2"></i>Dealer Wallet Recharges</h4>
        <p class="text-muted mb-0">Trace all wallet recharge transactions across the platform.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.wallet-recharges.exportExcel', request()->all())); ?>" class="btn btn-success btn-modern">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
        <a href="<?php echo e(route('admin.wallet-recharges.exportPdf', request()->all())); ?>" class="btn btn-danger btn-modern">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="<?php echo e(route('admin.wallet-recharges.index')); ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="from_date" class="form-label text-muted small fw-medium">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo e(request('from_date')); ?>">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label text-muted small fw-medium">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo e(request('to_date')); ?>">
            </div>
            <div class="col-md-3">
                <label for="payment_gateway" class="form-label text-muted small fw-medium">Gateway</label>
                <select class="form-select" id="payment_gateway" name="payment_gateway">
                    <option value="">All</option>
                    <option value="razorpay" <?php echo e(request('payment_gateway') == 'razorpay' ? 'selected' : ''); ?>>Razorpay</option>
                    <option value="phonepe" <?php echo e(request('payment_gateway') == 'phonepe' ? 'selected' : ''); ?>>PhonePe</option>
                    <option value="direct_deposit" <?php echo e(request('payment_gateway') == 'direct_deposit' ? 'selected' : ''); ?>>Direct Deposit</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="<?php echo e(route('admin.wallet-recharges.index')); ?>" class="btn btn-light ms-2">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-modern border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Receipt No</th>
                        <th>Company Name</th>
                        <th>Dealer Details</th>
                        <th>GST Number</th>
                        <th>Recharge (Base)</th>
                        <th>GST (18%)</th>
                        <th>Total Paid</th>
                        <th>Gateway / Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $base = $txn->amount;
                            $gst = $base * 0.18;
                            $total = $base + $gst;
                            $receipt = 'RCPT-' . $txn->created_at->format('Y') . '-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?php echo e($txn->created_at->format('d M Y')); ?></div>
                                <small class="text-muted"><?php echo e($txn->created_at->format('h:i A')); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><i class="bi bi-file-earmark-text"></i> <?php echo e($receipt); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?php echo e($txn->wallet->dealer->company_name ?? 'N/A'); ?></div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?php echo e($txn->wallet->dealer->name ?? 'Unknown Dealer'); ?></div>
                                <small class="text-muted d-block"><?php echo e($txn->wallet->dealer->phone ?? ''); ?></small>
                            </td>
                            <td>
                                <?php if(!empty($txn->wallet->dealer->gst_number)): ?>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><?php echo e($txn->wallet->dealer->gst_number); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Unregistered</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-success fw-medium">₹<?php echo e(number_format($base, 2)); ?></td>
                            <td class="text-danger">₹<?php echo e(number_format($gst, 2)); ?></td>
                            <td class="fw-bold">₹<?php echo e(number_format($total, 2)); ?></td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <?php if($txn->reference_type === 'admin_credit'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1"><i class="bi bi-bank me-1"></i>Direct Deposit</span>
                                        <small class="user-select-all text-secondary" style="font-size:0.7rem;"><i class="bi bi-person-badge"></i> <?php echo e($txn->reference_id ?? 'N/A'); ?></small>
                                    <?php else: ?>
                                        <?php
                                            $paymentMode = 'Razorpay';
                                            $icon = 'bi-credit-card';
                                            $colorClass = 'info';
                                            
                                            if (str_starts_with($txn->reference_id, 'PP_')) {
                                                $paymentMode = 'PhonePe';
                                                $icon = 'bi-phone';
                                                $colorClass = 'success';
                                            }
                                        ?>
                                        <span class="badge bg-<?php echo e($colorClass); ?> bg-opacity-10 text-<?php echo e($colorClass); ?> border border-<?php echo e($colorClass); ?> border-opacity-25 mb-1"><i class="bi <?php echo e($icon); ?> me-1"></i><?php echo e($paymentMode); ?></span>
                                        <small class="user-select-all text-secondary" style="font-size:0.7rem;"><i class="bi bi-hash"></i> <?php echo e($txn->reference_id ?? 'N/A'); ?></small>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                                        <a href="<?php echo e(route('admin.wallet-recharges.receipt', $txn->id)); ?>" class="btn btn-sm btn-outline-primary" style="padding: 1px 6px; font-size: 0.75rem;" title="Download Receipt">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <h5>No wallet recharges found</h5>
                                    <p>Try adjusting your date filters.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($transactions->hasPages()): ?>
        <div class="card-footer bg-white py-3">
            <?php echo e($transactions->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/wallet-recharges/index.blade.php ENDPATH**/ ?>