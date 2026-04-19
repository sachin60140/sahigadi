<?php $__env->startSection('title', 'Wallet'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Wallet</h2>
    <a href="<?php echo e(route('dealer.payments.checkout', ['type' => 'wallet_recharge', 'amount' => 1000])); ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add Money
    </a>
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
                        <td><small><?php echo e($transaction->reference_id ?? '-'); ?></small></td>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/wallet/index.blade.php ENDPATH**/ ?>