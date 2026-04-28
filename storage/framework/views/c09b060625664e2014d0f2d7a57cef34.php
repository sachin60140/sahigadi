<?php $__env->startSection('title', 'E-Challan Search - Dealer Panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-receipt me-2"></i>E-Challan Check</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Price: ₹<?php echo e(number_format($charge, 0)); ?></strong> per search
                    </div>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('dealer.challan-search.search')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control" 
                                placeholder="e.g. BR01AB1234" required>
                            <small class="text-muted">Enter vehicle registration number</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Search Challan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Search History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <th>Date</th>
                                    <th>Challans</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $challans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $challan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($challan->vehicle_number); ?></td>
                                    <td><?php echo e($challan->created_at->format('d M Y')); ?></td>
                                    <td><?php echo e($challan->challan_count); ?></td>
                                    <td>₹<?php echo e(number_format($challan->total_amount ?? 0)); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('dealer.challan-search.show', $challan)); ?>" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No searches yet</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo e($challans->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\challan-searches\index.blade.php ENDPATH**/ ?>