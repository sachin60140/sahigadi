<?php $__env->startSection('title', 'E-Challan Result - SAHIGADI'); ?>

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

            <?php if($challanSearch->is_success): ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header <?php echo e($challanSearch->total_amount > 0 ? 'bg-danger' : 'bg-success'); ?> text-white py-3">
                        <h4 class="mb-0">
                            <i class="bi <?php echo e($challanSearch->total_amount > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle'); ?> me-2"></i>
                            <?php echo e($challanSearch->total_amount > 0 ? 'Challans Found' : 'No Challans'); ?>

                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($cached) && $cached): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5 class="fw-bold">Vehicle: <?php echo e($challanSearch->vehicle_number); ?></h5>
                            <div class="d-flex gap-3">
                                <span class="badge bg-primary">Total Challans: <?php echo e($challanSearch->challan_count); ?></span>
                                <span class="badge bg-danger">Total Amount: ₹<?php echo e(number_format($challanSearch->total_amount)); ?></span>
                            </div>
                        </div>

                        <?php if($challanSearch->challan_data && count($challanSearch->challan_data) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Challan No</th>
                                            <th>Date</th>
                                            <th>Location</th>
                                            <th>Offence</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $challanSearch->challan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $challan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($challan['challanNo'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($challan['dateChallan'] ? \Carbon\Carbon::parse($challan['dateChallan'])->format('d M Y') : 'N/A'); ?></td>
                                            <td><?php echo e($challan['locationChallan'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($challan['detailsViolation'][0]['offence'] ?? 'N/A'); ?></td>
                                            <td>₹<?php echo e(number_format($challan['amountChallan'] ?? 0)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($challan['status'] == 'Paid' ? 'success' : 'danger'); ?>">
                                                    <?php echo e($challan['status'] ?? 'Unknown'); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>No pending challans found for this vehicle.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?php echo e($challanSearch->error_message ?? 'No challan records found for this vehicle.'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?php echo e(route('challan-search.index')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Check Another Vehicle
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\frontend\challan-search\result.blade.php ENDPATH**/ ?>