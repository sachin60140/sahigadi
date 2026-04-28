<?php $__env->startSection('title', 'Subscription Plans'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subscription Plans</h2>
</div>

<?php if($currentPlan): ?>
<div class="alert alert-info">
    <strong>Current Plan:</strong> <?php echo e($currentPlan->plan->name); ?> 
    | <strong>Active Listings:</strong> <?php echo e($currentPlan->getActiveListingsCount()); ?> / <?php echo e($currentPlan->plan->listing_limit); ?>

    | <strong>Expires:</strong> <?php echo e($currentPlan->expires_at->format('d M Y')); ?>

</div>
<?php else: ?>
<div class="alert alert-warning">
    You don't have an active subscription. Purchase a plan to start listing cars.
</div>
<?php endif; ?>

<div class="row">
    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-lg-4 mb-4">
        <div class="card h-100 <?php echo e($currentPlan && $currentPlan->plan_id === $plan->id ? 'border-success' : ''); ?>">
            <div class="card-header bg-<?php echo e($currentPlan && $currentPlan->plan_id === $plan->id ? 'success text-white' : 'light'); ?>">
                <h5 class="mb-0"><?php echo e($plan->name); ?></h5>
            </div>
            <div class="card-body text-center">
                <h2 class="text-primary">₹<?php echo e(number_format($plan->price)); ?></h2>
                <p class="text-muted"><?php echo e($plan->duration_days); ?> Days</p>
                <hr>
                <p><i class="bi bi-check-circle text-success"></i> <?php echo e($plan->listing_limit); ?> Car Listings</p>
                <p><i class="bi bi-check-circle text-success"></i> <?php echo e($plan->duration_days); ?> Days Validity</p>
                <?php if($plan->description): ?>
                <p class="text-muted small"><?php echo e($plan->description); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white border-top-0">
                <?php if($currentPlan && $currentPlan->plan_id === $plan->id): ?>
                <button class="btn btn-success w-100" disabled>Current Plan</button>
                <?php else: ?>
                <a href="<?php echo e(route('dealer.plans.show', $plan)); ?>" class="btn btn-primary w-100">Purchase Plan</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\plans\index.blade.php ENDPATH**/ ?>