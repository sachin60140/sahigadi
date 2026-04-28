<?php $__env->startSection('title', $plan->name); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Plan Details</h2>
    <a href="<?php echo e(route('dealer.plans.index')); ?>" class="btn btn-secondary">Back</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo e($plan->name); ?></h5>
            </div>
            <div class="card-body">
                <?php if($plan->price == 0): ?>
                <h3 class="text-success">FREE</h3>
                <?php else: ?>
                <h3 class="text-primary">₹<?php echo e(number_format($plan->price)); ?></h3>
                <?php endif; ?>
                <p class="text-muted"><?php echo e($plan->duration_days); ?> Days</p>
                <hr>
                <p><i class="bi bi-check-circle text-success"></i> <?php echo e($plan->listing_limit); ?> Car Listings</p>
                <p><i class="bi bi-check-circle text-success"></i> <?php echo e($plan->duration_days); ?> Days Validity</p>
                <?php if($plan->description): ?>
                <p class="text-muted"><?php echo e($plan->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo e($plan->price == 0 ? 'Activate Free Plan' : 'Payment Summary'); ?></h5>
            </div>
            <div class="card-body">
                <?php if($plan->price == 0): ?>
                <form action="<?php echo e(route('dealer.plans.purchase', $plan)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i> Activate Free Plan
                    </button>
                </form>
                <?php else: ?>
                <table class="table">
                    <tr>
                        <td>Plan Price</td>
                        <td class="text-end">₹<?php echo e(number_format($plan->price)); ?></td>
                    </tr>
                    <tr>
                        <td>Wallet Balance</td>
                        <td class="text-end">₹<?php echo e(number_format($balance, 2)); ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Balance After Purchase</td>
                        <td class="text-end">₹<?php echo e(number_format($balance - $plan->price, 2)); ?></td>
                    </tr>
                </table>
                
                <?php if($balance >= $plan->price): ?>
                <form action="<?php echo e(route('dealer.plans.purchase', $plan)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Confirm Purchase
                    </button>
                </form>
                <?php else: ?>
                <div class="alert alert-danger">
                    Insufficient wallet balance. 
                    <a href="<?php echo e(route('dealer.wallet.add')); ?>" class="alert-link">Add Funds</a>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\plans\show.blade.php ENDPATH**/ ?>