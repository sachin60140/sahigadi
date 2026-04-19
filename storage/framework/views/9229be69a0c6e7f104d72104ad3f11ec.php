<?php $__env->startSection('title', 'Wallet'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Wallet</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rechargeModal">
        <i class="bi bi-plus-circle"></i> Add Money
    </button>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Available Balance</h5>
                <h2>₹<?php echo e(number_format($balance, 2)); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Transaction History</h5>
    </div>
    <div class="card-body">
        <?php if($transactions->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Remark</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($transaction->created_at->format('d M Y, h:i A')); ?></td>
                        <td>
                            <?php if($transaction->type === 'credit'): ?>
                                <span class="badge bg-success">Credit</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Debit</span>
                            <?php endif; ?>
                        </td>
                        <td class="<?php echo e($transaction->type === 'credit' ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($transaction->type === 'credit' ? '+' : '-'); ?>₹<?php echo e(number_format($transaction->amount, 2)); ?>

                        </td>
                        <td><?php echo e($transaction->remark ?? '-'); ?></td>
                        <td>
                            <small><?php echo e($transaction->reference_id ?? '-'); ?></small>
                            <?php if(str_contains(strtolower($transaction->remark), 'recharge via razorpay') && $transaction->type === 'credit'): ?>
                                <br>
                                <a href="<?php echo e(route('dealer.wallet.receipt', $transaction->id)); ?>" class="btn btn-sm btn-outline-primary mt-1" style="font-size: 0.75rem; padding: 2px 6px;">
                                    <i class="bi bi-download"></i> Receipt
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php echo e($transactions->links()); ?>

        <?php else: ?>
        <p class="text-muted text-center mb-0">No transactions yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Recharge Modal -->
<div class="modal fade" id="rechargeModal" tabindex="-1" aria-labelledby="rechargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('dealer.payments.checkout')); ?>" method="GET" id="rechargeForm">
                <input type="hidden" name="type" value="wallet_recharge">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeModalLabel">Recharge Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recharge_amount" class="form-label">Recharge Amount (₹)</label>
                        <input type="number" class="form-control" id="recharge_amount" name="recharge_amount" min="1000" value="1000" required>
                        <div class="form-text">Minimum recharge amount is ₹1000.</div>
                    </div>
                    <div class="card bg-light border-0">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">Base Amount:</span>
                                <span id="displayBase" class="fw-medium">₹1000.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">GST (18%):</span>
                                <span id="displayGst" class="fw-medium text-danger">+ ₹180.00</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Payable:</span>
                                <span id="displayTotal" class="fw-bold text-success fs-5">₹1180.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="proceedBtn">Proceed to Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.getElementById('recharge_amount');
        const displayBase = document.getElementById('displayBase');
        const displayGst = document.getElementById('displayGst');
        const displayTotal = document.getElementById('displayTotal');
        const proceedBtn = document.getElementById('proceedBtn');

        function updateCalculation() {
            let amount = parseFloat(amountInput.value) || 0;
            if (amount < 1000) {
                proceedBtn.disabled = true;
            } else {
                proceedBtn.disabled = false;
            }

            let gst = amount * 0.18;
            let total = amount + gst;

            displayBase.textContent = '₹' + amount.toFixed(2);
            displayGst.textContent = '+ ₹' + gst.toFixed(2);
            displayTotal.textContent = '₹' + total.toFixed(2);
        }

        amountInput.addEventListener('input', updateCalculation);
        // Initial calc
        updateCalculation();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/wallet/index.blade.php ENDPATH**/ ?>