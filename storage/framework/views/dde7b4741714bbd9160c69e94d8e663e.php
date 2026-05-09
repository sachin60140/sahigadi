<?php $__env->startSection('title', 'Promote Your Listing'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="<?php echo e(route('customer.dashboard')); ?>" class="btn btn-light btn-sm me-3 border shadow-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="mb-0 text-gray-800">Promote "<?php echo e($customerListing->model); ?>"</h3>
                        <p class="text-muted mb-0 small">Get up to 10x more views by featuring your car on the homepage.</p>
                    </div>
                </div>
                <div class="text-end d-none d-md-block">
                    <p class="mb-0 text-muted small">Wallet Balance</p>
                    <h4 class="mb-0 text-primary">₹<?php echo e(number_format(auth('customer')->user()->wallet->balance ?? 0, 2)); ?></h4>
                </div>
            </div>

            <!-- Current Status -->
            <?php if($customerListing->isFeatured()): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                <i class="bi bi-star-fill fs-3 me-3 text-warning"></i>
                <div>
                    <h5 class="alert-heading mb-1">Currently Featured</h5>
                    <p class="mb-0">This car is featured until <strong><?php echo e($customerListing->featured_expires_at->format('d M Y, h:i A')); ?></strong>.</p>
                    <small class="opacity-75">Purchasing a new plan will add days to your existing expiry date.</small>
                </div>
            </div>
            <?php endif; ?>

            <!-- Plans Grid -->
            <div class="row g-4 mt-2">
                <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm position-relative feature-plan-card" style="transition: transform 0.3s ease;">
                        <?php if($loop->first): ?>
                            <div class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger px-3 py-2 text-uppercase fw-bold" style="letter-spacing: 1px;">
                                Most Popular
                            </div>
                        <?php endif; ?>
                        <div class="card-body text-center p-5">
                            <h4 class="text-primary fw-bold mb-3"><?php echo e($plan->name); ?></h4>
                            <h1 class="display-5 fw-bolder mb-0 text-dark">₹<?php echo e(number_format($plan->price)); ?></h1>
                            <p class="text-muted mb-4">for <?php echo e($plan->duration_days); ?> Days</p>

                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Show on Homepage</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Top of search results</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 'Featured' yellow badge</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Randomised display rotation</li>
                            </ul>

                            <form action="<?php echo e(route('customer.listing.featured-purchase', $customerListing)); ?>" method="POST" class="purchase-form no-loader">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                                <button type="submit" class="btn btn-warning w-100 fw-bold py-2 btn-purchase" data-price="<?php echo e($plan->price); ?>">
                                    Buy Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center py-5">
                    <h5 class="text-muted">No featured plans available at the moment.</h5>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<!-- SweetAlert2 for nice confirmation popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hover effects for cards
    document.querySelectorAll('.feature-plan-card').forEach(card => {
        card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-10px)');
        card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
    });

    const walletBalance = <?php echo e(auth('customer')->user()->wallet->balance ?? 0); ?>;

    document.querySelectorAll('.purchase-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('.btn-purchase');
            const price = parseFloat(btn.dataset.price);

            if (price > walletBalance) {
                Swal.fire({
                    icon: 'error',
                    title: 'Low Balance',
                    text: `You need ₹${price} but only have ₹${walletBalance}. Please recharge your wallet.`,
                    confirmButtonText: 'Go to Wallet',
                    confirmButtonColor: '#e94560',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "<?php echo e(route('customer.wallet.index')); ?>";
                    }
                });
                return;
            }

            Swal.fire({
                title: 'Confirm Purchase',
                text: `Are you sure you want to deduct ₹${price} from your wallet to feature this car?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Buy Plan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                    btn.disabled = true;
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', data.message, 'success').then(() => {
                                window.location.href = "<?php echo e(route('customer.dashboard')); ?>";
                            });
                        } else {
                            Swal.fire('Failed', data.message, 'error').then(() => {
                                if (data.redirect) window.location.href = data.redirect;
                            });
                            btn.innerHTML = 'Buy Now';
                            btn.disabled = false;
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Server error occurred. Please try again.', 'error');
                        btn.innerHTML = 'Buy Now';
                        btn.disabled = false;
                    });
                }
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/customer/listings/featured-plans.blade.php ENDPATH**/ ?>