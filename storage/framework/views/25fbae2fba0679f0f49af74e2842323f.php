<?php $__env->startSection('title', 'Service History Settings - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-gear me-2"></i>Service History Settings</h2>
    <a href="<?php echo e(route('admin.service-histories.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Reports
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Service Charges</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.service-histories.settings')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Charge (₹)</label>
                            <input type="number" name="charge" class="form-control" 
                                   value="<?php echo e($charge); ?>" min="0" step="0.01" required>
                            <small class="text-muted">Paid by customers via Razorpay</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dealer Charge (₹)</label>
                            <input type="number" name="dealer_charge" class="form-control" 
                                   value="<?php echo e($dealerCharge); ?>" min="0" step="0.01" required>
                            <small class="text-muted">Debited from dealer's wallet</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update Charges
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <h3 class="mb-0 text-primary"><?php echo e($totalSearches); ?></h3>
                            <small class="text-muted">Total Searches</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <h3 class="mb-0 text-success"><?php echo e($successfulSearches); ?></h3>
                            <small class="text-muted">Successful</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-success bg-opacity-10 rounded text-center">
                            <h3 class="mb-0 text-success">₹<?php echo e(number_format($totalRevenue, 2)); ?></h3>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\service-histories\settings.blade.php ENDPATH**/ ?>