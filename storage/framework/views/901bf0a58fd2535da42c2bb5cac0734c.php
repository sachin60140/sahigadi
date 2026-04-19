<?php $__env->startSection('title', 'Payment Checkout'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Payment - <?php echo e($typeLabel); ?></h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td>Type</td>
                        <td><strong><?php echo e($typeLabel); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td><strong class="text-success">₹<?php echo e(number_format($amount, 2)); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Details</h5>
            </div>
            <div class="card-body">
                <form id="razorpay-form" action="<?php echo e(route('dealer.payments.success')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="<?php echo e($order['order_id']); ?>">
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                    <input type="hidden" name="amount" value="<?php echo e($order['amount'] * 100); ?>">
                    <input type="hidden" name="type" value="<?php echo e($type); ?>">
                    <input type="hidden" name="plan_id" value="<?php echo e($planId ?? ''); ?>">
                    <input type="hidden" name="car_id" value="<?php echo e($carId ?? ''); ?>">
                    <input type="hidden" name="days" value="<?php echo e($days ?? ''); ?>">

                    <button type="button" id="rzp-button" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-credit-card"></i> Pay ₹<?php echo e(number_format($amount, 2)); ?> with Razorpay
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo e($keyId); ?>",
    "amount": <?php echo e($order['amount'] * 100); ?>,
    "currency": "INR",
    "name": "CarMarket",
    "description": "<?php echo e($typeLabel); ?>",
    "order_id": "<?php echo e($order['order_id']); ?>",
    "handler": function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.getElementById('razorpay-form').submit();
    },
    "prefill": {
        "name": "<?php echo e(auth('dealer')->user()->name); ?>",
        "email": "<?php echo e(auth('dealer')->user()->email); ?>"
    },
    "theme": {
        "color": "#0d6efd"
    }
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/payments/checkout.blade.php ENDPATH**/ ?>