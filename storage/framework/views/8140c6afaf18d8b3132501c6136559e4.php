<?php $__env->startSection('title', 'Secure Payment - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-4 p-md-5">
                    <?php if(session('success')): ?>
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 fw-bold text-dark">Payment Successful!</h3>
                            <p class="text-muted">Thank you for your payment.</p>
                            <a href="<?php echo e(url('/')); ?>" class="btn btn-outline-primary mt-3 rounded-pill px-4">Return Home</a>
                        </div>
                    <?php else: ?>
                        <?php if(session('error')): ?>
                            <div class="alert alert-danger mb-4">
                                <?php echo e(session('error')); ?>

                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark mb-1">Secure Checkout</h3>
                            <p class="text-muted small">Complete your payment below</p>
                        </div>

                        <div class="bg-light p-4 rounded-3 mb-4 text-center">
                            <div class="text-muted small fw-medium text-uppercase mb-2">Amount to Pay</div>
                            <div class="display-5 fw-bold text-dark">₹<?php echo e(number_format($link->amount, 2)); ?></div>
                            <hr class="my-3 border-secondary border-opacity-25">
                            <div class="d-flex justify-content-between text-start small">
                                <span class="text-muted">Purpose:</span>
                                <span class="fw-medium text-dark"><?php echo e($link->purpose); ?></span>
                            </div>
                            <div class="d-flex justify-content-between text-start small mt-2">
                                <span class="text-muted">Payee:</span>
                                <span class="fw-medium text-dark"><?php echo e($link->dealer ? ($link->dealer->company_name ?? $link->dealer->name) : $link->customer_name); ?></span>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <?php if(in_array($link->gateway, ['any', 'phonepe']) && $isPhonePeActive): ?>
                                <form action="<?php echo e(route('pay.link.checkout', $link->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="gateway" value="phonepe">
                                    <button type="submit" class="btn btn-lg w-100 rounded-pill text-white fw-bold d-flex align-items-center justify-content-center gap-2" style="background: #5f259f; border: none; padding: 12px;">
                                        <i class="bi bi-phone"></i> Pay with PhonePe
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if(in_array($link->gateway, ['any', 'razorpay']) && $isRazorpayActive && $order): ?>
                                <form action="<?php echo e(route('pay.link.checkout', $link->id)); ?>" method="POST" id="razorpay-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="gateway" value="razorpay">
                                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="<?php echo e($order['order_id']); ?>">
                                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                                    
                                    <button type="button" id="rzp-button" class="btn btn-lg w-100 rounded-pill text-white fw-bold d-flex align-items-center justify-content-center gap-2" style="background: #3395ff; border: none; padding: 12px;">
                                        <i class="bi bi-credit-card"></i> Pay with Razorpay
                                    </button>
                                </form>

                                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                                <script>
                                    var options = {
                                        "key": "<?php echo e($keyId); ?>",
                                        "amount": "<?php echo e($order['amount'] * 100); ?>",
                                        "currency": "INR",
                                        "name": "<?php echo e(config('app.name')); ?>",
                                        "description": "<?php echo e($link->purpose); ?>",
                                        "order_id": "<?php echo e($order['order_id']); ?>",
                                        "handler": function (response){
                                            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                                            document.getElementById('razorpay_signature').value = response.razorpay_signature;
                                            document.getElementById('razorpay-form').submit();
                                        },
                                        "prefill": {
                                            "name": "<?php echo e($link->dealer ? $link->dealer->name : $link->customer_name); ?>",
                                            "email": "<?php echo e($link->dealer ? $link->dealer->email : $link->customer_email); ?>",
                                            "contact": "<?php echo e($link->dealer ? $link->dealer->phone : $link->customer_mobile); ?>"
                                        },
                                        "theme": {
                                            "color": "#3395ff"
                                        }
                                    };
                                    var rzp1 = new Razorpay(options);
                                    document.getElementById('rzp-button').onclick = function(e){
                                        rzp1.open();
                                        e.preventDefault();
                                    }
                                </script>
                            <?php endif; ?>

                            <?php if(!$isPhonePeActive && !$isRazorpayActive): ?>
                                <div class="alert alert-warning text-center">
                                    Payments are currently disabled.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-center text-muted small mt-4 mb-0">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i> Secured by 256-bit encryption
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/payment-links/show.blade.php ENDPATH**/ ?>