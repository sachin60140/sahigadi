<?php $__env->startSection('title', 'Vehicle Search - SAHIGADI'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
.icon-shape { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; border-radius: 14px; }
.form-control-modern { border: 2px solid #e2e8f0; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 500; font-size: 1.1rem; letter-spacing: 1px; transition: border-color 0.2s; }
.form-control-modern:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold mb-1 text-dark"><i class="bi bi-search text-primary me-2"></i>Vahan RC Search</h2>
        <p class="text-muted mb-0">Instantly fetch official RTO records for any vehicle to verify ownership.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="icon-shape bg-success bg-opacity-10 text-success me-4 flex-shrink-0">
                    <i class="bi bi-wallet2 fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted fw-semibold text-uppercase mb-1" style="font-size: 0.8rem;">Wallet Balance</h6>
                    <h2 class="fw-bold text-dark mb-0">₹<?php echo e(number_format($walletBalance, 2)); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="icon-shape bg-primary bg-opacity-10 text-primary me-4 flex-shrink-0">
                    <i class="bi bi-currency-rupee fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted fw-semibold text-uppercase mb-1" style="font-size: 0.8rem;">Cost Per Search</h6>
                    <h2 class="fw-bold text-dark mb-0">₹<?php echo e(number_format($charge, 2)); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="icon-shape bg-info bg-opacity-10 text-info me-4 flex-shrink-0">
                    <i class="bi bi-receipt fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted fw-semibold text-uppercase mb-1" style="font-size: 0.8rem;">Total Searches</h6>
                    <h2 class="fw-bold text-dark mb-0"><?php echo e($searches->total()); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Widget -->
<div class="card border-0 shadow-sm rounded-4 mb-5 position-relative overflow-hidden">
    <!-- background accent -->
    <div class="position-absolute top-0 end-0 h-100 bg-light w-50" style="clip-path: polygon(10% 0, 100% 0, 100% 100%, 0% 100%); z-index: 0;"></div>
    
    <div class="card-body p-4 p-md-5 position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h4 class="fw-bold text-dark mb-2">Search Vehicle Database</h4>
                <p class="text-muted">Enter the complete registration number without spaces (e.g., DL1CA9876) to retrieve real-time regional transport data.</p>
            </div>
            <div class="col-lg-6">
                <form action="<?php echo e(route('dealer.vehicle-search.search')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="bg-white p-3 rounded-4 shadow-sm border">
                        <label class="form-label fw-semibold text-muted ms-1 mb-2">Registration Number</label>
                        <div class="d-flex gap-2">
                            <div class="flex-grow-1">
                                <input type="text" name="registration_number" class="form-control form-control-modern text-uppercase <?php $__errorArgs = ['registration_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="e.g. MH12AB1234" maxlength="20" required>
                                <?php $__errorArgs = ['registration_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback ms-2 mt-1"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 fw-bold rounded-3 d-flex align-items-center" <?php echo e($walletBalance < $charge ? 'disabled' : ''); ?>>
                                <i class="bi bi-search me-2"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
                <?php if($walletBalance < $charge): ?>
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger mt-3 mb-0 rounded-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div>
                            <strong class="d-block">Insufficient Funds</strong>
                            <span>You need at least ₹<?php echo e(number_format($charge, 2)); ?> to search. <a href="<?php echo e(route('dealer.wallet.add')); ?>" class="alert-link text-decoration-none">Top up wallet &rarr;</a></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>Recent Searches</h5>
            <form action="<?php echo e(route('dealer.vehicle-search.index')); ?>" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0 shadow-none" placeholder="Search reg no..." value="<?php echo e(request('search')); ?>">
                </div>
                <select name="status" class="form-select form-select-sm shadow-none bg-light w-auto">
                    <option value="">All Status</option>
                    <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                    <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm px-3 fw-semibold rounded-2 text-white">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-white">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Vehicle</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Owner Info</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Status</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Charge</th>
                        <th class="py-3 text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Date</th>
                        <th class="py-3 pe-4 text-end text-muted fw-semibold text-uppercase" style="font-size: 0.75rem;">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php $__empty_1 = true; $__currentLoopData = $searches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $search): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="transition: background-color 0.2s;">
                        <td class="ps-4 py-3">
                            <span class="badge bg-dark bg-opacity-10 text-dark px-2 py-1 fs-6 font-monospace border border-dark border-opacity-25 rounded-2 d-inline-block shadow-sm">
                                <?php echo e(strtoupper($search->registration_number)); ?>

                            </span>
                        </td>
                        <td class="py-3">
                            <div class="fw-semibold text-dark"><?php echo e($search->owner_name ?? 'Name Unavailable'); ?></div>
                            <div class="small text-muted">
                                <?php if($search->make || $search->model): ?>
                                    <i class="bi bi-car-front opacity-50 me-1"></i><?php echo e($search->make); ?> <?php echo e($search->model); ?>

                                <?php else: ?>
                                    <i class="bi bi-info-circle opacity-50 me-1"></i>No specs available
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="py-3">
                            <?php if($search->is_success): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Success</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1"><i class="bi bi-x-circle-fill me-1"></i> Failed</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 fw-semibold text-muted">₹<?php echo e(number_format($search->debit_amount, 2)); ?></td>
                        <td class="py-3 text-muted small fw-medium">
                            <?php echo e($search->created_at->format('M d, Y')); ?><br>
                            <span class="opacity-75"><?php echo e($search->created_at->format('h:i A')); ?></span>
                        </td>
                        <td class="py-3 pe-4 text-end">
                            <div class="btn-group shadow-sm">
                                <a href="<?php echo e(route('dealer.vehicle-search.show', $search)); ?>" class="btn btn-light btn-sm text-primary border-primary border-opacity-25 fw-semibold px-3" title="View Details">
                                    <i class="bi bi-eye-fill"></i> View
                                </a>
                                <?php if($search->is_success): ?>
                                <a href="<?php echo e(route('dealer.vehicle-search.pdf', $search)); ?>" class="btn btn-primary btn-sm px-3" title="Download PDF">
                                    <i class="bi bi-download"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-secondary" style="width: 80px; height: 80px;">
                                <i class="bi bi-search" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Vehicle Searches Yet</h5>
                            <p class="text-muted">Use the search form above to verify a vehicle's RC details.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($searches->hasPages()): ?>
        <div class="p-3 border-top bg-light rounded-bottom-4 d-flex justify-content-center">
            <?php echo e($searches->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\vehicle-search\index.blade.php ENDPATH**/ ?>