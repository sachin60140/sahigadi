<?php $__env->startSection('title', 'Maruti Service History Result - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if(isset($success) && !$success): ?>
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Search Failed</h5>
                    <p class="mb-0"><?php echo e($message); ?></p>
                </div>
            <?php endif; ?>

            <?php if($marutiServiceHistory->is_success): ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Service History Found</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($cached) && $cached): ?>
                            <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div><i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)</div>
                                <form action="<?php echo e(route('maruti-service-history.search')); ?>" method="POST" class="m-0">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="vehicle_number" value="<?php echo e($marutiServiceHistory->vehicle_number); ?>">
                                    <input type="hidden" name="customer_name" value="<?php echo e($marutiServiceHistory->customer_name ?? ''); ?>">
                                    <input type="hidden" name="customer_phone" value="<?php echo e($marutiServiceHistory->customer_phone ?? ''); ?>">
                                    <input type="hidden" name="customer_email" value="<?php echo e($marutiServiceHistory->customer_email ?? ''); ?>">
                                    <input type="hidden" name="force_fresh" value="1">
                                    <button type="submit" class="btn btn-sm btn-warning border border-dark fw-bold">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Fresh Search
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5 class="fw-bold">Vehicle: <?php echo e($marutiServiceHistory->vehicle_number); ?></h5>
                            <?php if($marutiServiceHistory->customer_name): ?>
                                <p class="text-muted mb-0">Requested by: <?php echo e($marutiServiceHistory->customer_name); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if($marutiServiceHistory->records->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Service Type</th>
                                            <th>Dealer</th>
                                            <th>Job Card</th>
                                            <th>RO No</th>
                                            <th>Total Amount</th>
                                            <th>Mileage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $marutiServiceHistory->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($record->svc_date ?? 'N/A'); ?></td>
                                            <td><span class="badge bg-info"><?php echo e($record->service_cate ?? 'N/A'); ?></span></td>
                                            <td><?php echo e($record->dealer_name ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->register_no ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->repair_order_no ?? 'N/A'); ?></td>
                                            <td class="text-success">₹<?php echo e(number_format($record->total_amount ?? 0, 2)); ?></td>
                                            <td><?php echo e($record->mileage ?? 'N/A'); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>No service records found for this vehicle.
                            </div>
                        <?php endif; ?>

                        <div class="mt-4 text-center">
                            <a href="<?php echo e(route('maruti-service-history.pdf', $marutiServiceHistory->id)); ?>" class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?php echo e($marutiServiceHistory->error_message ?? 'No service history records found for this vehicle.'); ?></p>
                        <p class="text-muted mt-3">This could mean:</p>
                        <ul>
                            <li>Vehicle is not registered with Maruti authorized service centers</li>
                            <li>Vehicle has no service records</li>
                            <li>Vehicle brand/model is not supported</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?php echo e(route('maruti-service-history.index')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Search Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\frontend\maruti-service-history\result.blade.php ENDPATH**/ ?>