<?php $__env->startSection('title', 'Service History - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wrench me-2"></i>Service History</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-wallet2 text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">Wallet Balance</h6>
                <h3 class="mb-0 text-success">₹<?php echo e(number_format($walletBalance, 2)); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-currency-rupee text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">Charge Per Search</h6>
                <h3 class="mb-0">₹<?php echo e(number_format($charge, 2)); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-receipt text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1">Total Searches</h6>
                <h3 class="mb-0"><?php echo e($searches->total()); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Search Service History</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('dealer.service-history.search')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control form-control-lg text-uppercase <?php $__errorArgs = ['vehicle_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           placeholder="e.g. UP13CT1234" maxlength="20" required>
                    <?php $__errorArgs = ['vehicle_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="text-muted">Enter vehicle registration number</small>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100" <?php echo e($walletBalance < $charge ? 'disabled' : ''); ?>>
                        <i class="bi bi-search me-2"></i>Search Now
                    </button>
                </div>
            </div>
        </form>
        <?php if($walletBalance < $charge): ?>
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Insufficient wallet balance. Please <a href="<?php echo e(route('dealer.wallet.add')); ?>">add money</a> to search.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Search History</h5>
            <form action="<?php echo e(route('dealer.service-history.index')); ?>" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Vehicle number..." value="<?php echo e(request('search')); ?>">
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                    <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Vehicle No.</th>
                        <th>Services Found</th>
                        <th>Status</th>
                        <th>Charges</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-bold"><?php echo e($search->vehicle_number); ?></td>
                        <td><?php echo e($search->records->count() ?? 0); ?> Records</td>
                        <td>
                            <?php if($search->is_success): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Success</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>
                            <?php endif; ?>
                        </td>
                        <td>₹<?php echo e(number_format($search->debit_amount, 2)); ?></td>
                        <td><?php echo e($search->created_at->format('d M Y, h:i A')); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('dealer.service-history.show', $search)); ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if($search->is_success): ?>
                                <a href="<?php echo e(route('dealer.service-history.pdf', $search)); ?>" class="btn btn-outline-danger">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-wrench text-secondary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-secondary">No searches yet</h5>
                            <p class="text-muted">Search for service history using the form above</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($searches->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($searches->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\service-history\index.blade.php ENDPATH**/ ?>