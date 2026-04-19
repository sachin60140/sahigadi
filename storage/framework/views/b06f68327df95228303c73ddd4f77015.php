<?php $__env->startSection('title', 'Payment Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i>Razorpay Payment Settings</h4>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.payment-settings.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Razorpay Key ID</label>
                                <input type="text" name="razorpay_key_id" class="form-control" 
                                    value="<?php echo e(old('razorpay_key_id', $keyId)); ?>" required>
                                <small class="text-muted">Your Razorpay Key ID (e.g., rzp_test_xxxxx)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Razorpay Key Secret</label>
                                <input type="password" name="razorpay_key_secret" class="form-control" 
                                    value="<?php echo e(old('razorpay_key_secret', $keySecret)); ?>" required>
                                <small class="text-muted">Your Razorpay Key Secret</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/payment-settings/index.blade.php ENDPATH**/ ?>