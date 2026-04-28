<?php $__env->startPush('styles'); ?>
<style>
.dealer-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 16px;
}
.dealer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.dealer-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', $seoTitle); ?>
<?php $__env->startSection('meta_description', $seoDescription); ?>

<?php $__env->startSection('content'); ?>
<section class="py-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Verified Dealers</li>
            </ol>
        </nav>
        <div class="text-white">
            <h1 class="fw-bold mb-2">Verified Dealers</h1>
            <p class="mb-0">Browse <?php echo e($dealers->total()); ?> trusted and verified car dealers</p>
        </div>
    </div>
</section>

<div class="container py-5">
    <?php if($dealers->isEmpty()): ?>
        <div class="text-center py-5">
            <i class="bi bi-shop" style="font-size: 4rem; color: #ccc;"></i>
            <h4 class="mt-3 text-muted">No verified dealers found</h4>
            <p class="text-muted">Check back later for verified dealers</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php $__currentLoopData = $dealers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dealer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-4">
                <div class="card dealer-card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="dealer-avatar me-3">
                                <i class="bi bi-person-fill text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1"><?php echo e($dealer->name); ?></h5>
                                <?php if($dealer->company_name): ?>
                                    <p class="text-muted small mb-2"><?php echo e($dealer->company_name); ?></p>
                                <?php endif; ?>
                                <?php if($dealer->city): ?>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?php echo e($dealer->city); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if($dealer->gst_verified): ?>
                                    <span class="badge bg-success me-1"><i class="bi bi-shield-check me-1"></i>GST Verified</span>
                                <?php elseif($dealer->email_verified_at): ?>
                                    <span class="badge bg-info me-1"><i class="bi bi-check-circle me-1"></i>Email Verified</span>
                                <?php endif; ?>
                                <span class="text-muted small"><?php echo e($dealer->approved_cars_count ?? 0); ?> cars</span>
                            </div>
                            <a href="<?php echo e(route('dealer.catalog', $dealer->slug)); ?>" class="btn btn-sm btn-outline-primary">
                                View Inventory <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <?php if($dealer->phone): ?>
                        <div class="mt-3">
                            <a href="tel:<?php echo e($dealer->phone); ?>" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-telephone me-2"></i>Contact Dealer
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="mt-4">
            <?php echo e($dealers->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\frontend\cars\verified-dealers.blade.php ENDPATH**/ ?>