<?php $__env->startSection('title', 'Maruti Service History - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dealer.maruti-service-history.index')); ?>">Maruti Service History</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($marutiServiceHistory->vehicle_number); ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Maruti Service History - <?php echo e($marutiServiceHistory->vehicle_number); ?></h2>
        <a href="<?php echo e(route('dealer.maruti-service-history.pdf', $marutiServiceHistory)); ?>" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
    </div>

    <?php if($marutiServiceHistory->is_success): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4><?php echo e($marutiServiceHistory->records->count()); ?></h4>
                        <p class="text-muted mb-0">Total Services</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4>Rs.<?php echo e(number_format($marutiServiceHistory->debit_amount ?? 0)); ?></h4>
                        <p class="text-muted mb-0">Amount Debited</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <span class="badge bg-success p-2">Success</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Service Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service Type</th>
                                <th>Dealer</th>
                                <th>Job Card No</th>
                                <th>RO No</th>
                                <th>Part Amt</th>
                                <th>Labour Amt</th>
                                <th>Total Amt</th>
                                <th>Mileage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $marutiServiceHistory->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($record->svc_date ?? 'N/A'); ?></td>
                                <td><?php echo e($record->service_cate ?? 'N/A'); ?></td>
                                <td><?php echo e($record->dealer_name ?? 'N/A'); ?></td>
                                <td><?php echo e($record->register_no ?? 'N/A'); ?></td>
                                <td><?php echo e($record->repair_order_no ?? 'N/A'); ?></td>
                                <td>Rs.<?php echo e(number_format($record->part_amount ?? 0, 2)); ?></td>
                                <td>Rs.<?php echo e(number_format($record->labour_amount ?? 0, 2)); ?></td>
                                <td>Rs.<?php echo e(number_format($record->total_amount ?? 0, 2)); ?></td>
                                <td><?php echo e($record->mileage ?? 'N/A'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h5>Search Failed</h5>
            <p><?php echo e($marutiServiceHistory->error_message ?? 'No service records found'); ?></p>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="<?php echo e(route('dealer.maruti-service-history.index')); ?>" class="btn btn-outline-primary w-100 mb-2">Back to History</a>
        <a href="<?php echo e(route('dealer.maruti-service-history.search')); ?>?vehicle_number=<?php echo e($marutiServiceHistory->vehicle_number); ?>" class="btn btn-primary w-100 mt-2">Search Again</a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/dealer/maruti-service-history/show.blade.php ENDPATH**/ ?>