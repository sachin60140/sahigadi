<?php $__env->startSection('title', 'Payment - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-credit-card" style="font-size: 4rem; color: var(--accent);"></i>
                    <h3 class="fw-bold mt-3">Complete Payment</h3>
                    <p class="text-muted mb-4">
                        Vehicle: <strong><?php echo e($vehicleNumber); ?></strong>
                    </p>

                    <div class="alert alert-light border mb-4">
                        <h4 class="mb-0">₹<?php echo e(number_format($amount, 0)); ?></h4>
                        <small class="text-muted">Service History Report</small>
                    </div>

                    <form id="payment-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="razorpay_order_id" value="<?php echo e($orderId); ?>">
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                        
                        <button type="button" id="pay-button" class="btn btn-accent w-100 py-3">
                            <i class="bi bi-lock me-2"></i>Pay Now ₹<?php echo e(number_format($amount, 0)); ?>

                        </button>
                    </form>

                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>Secure payment via Razorpay
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const options = {
        "key": "<?php echo e($keyId); ?>",
        "amount": "<?php echo e($amount * 100); ?>",
        "currency": "INR",
        "name": "SAHIGADI",
        "description": "Service History Report - <?php echo e($vehicleNumber); ?>",
        "order_id": "<?php echo e($orderId); ?>",
        "handler": function (response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            
            // Submit to callback
            const form = document.getElementById('payment-form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(input);
            
            // Redirect to callback with payment data
            window.location.href = "<?php echo e(route('service-history.callback')); ?>" + 
                "?razorpay_order_id=" + response.razorpay_order_id + 
                "&razorpay_payment_id=" + response.razorpay_payment_id + 
                "&razorpay_signature=" + response.razorpay_signature;
        },
        "theme": {
            "color": "#e94560"
        }
    };

    const rzp1 = new Razorpay(options);
    
    document.getElementById('pay-button').onclick = function(e) {
        rzp1.open();
        e.preventDefault();
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/service-history/payment.blade.php ENDPATH**/ ?>