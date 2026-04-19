<?php $__env->startSection('title', 'E-Challan Search Details - SAHIGADI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt me-2"></i>E-Challan Search Details</h2>
    <div>
        <a href="<?php echo e(route('admin.challan-searches.download-pdf', $challanSearch->id)); ?>" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
        <a href="<?php echo e(route('admin.challan-searches.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Vehicle Number: <?php echo e($challanSearch->vehicle_number); ?></h5>
                <p>Dealer: <?php echo e($challanSearch->dealer->name ?? 'N/A'); ?></p>
                <p>Date: <?php echo e($challanSearch->created_at->format('d M Y H:i')); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if($challanSearch->is_success): ?>
                    <span class="badge bg-success">Success</span>
                <?php else: ?>
                    <span class="badge bg-danger">Failed</span>
                <?php endif; ?>
                <p class="mt-2">Challans: <?php echo e($challanSearch->challan_count); ?> | Amount: ₹<?php echo e(number_format($challanSearch->total_amount ?? 0)); ?></p>
            </div>
        </div>

        <?php if($challanSearch->is_success && $challanSearch->challan_data): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
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
                            <td><?php echo e($challan['dateChallan'] ?? 'N/A'); ?></td>
                            <td><?php echo e($challan['locationChallan'] ?? 'N/A'); ?></td>
                            <td><?php echo e($challan['detailsViolation'][0]['offence'] ?? 'N/A'); ?></td>
                            <td>₹<?php echo e(number_format($challan['amountChallan'] ?? 0)); ?></td>
                            <td><?php echo e($challan['status'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <?php echo e($challanSearch->error_message ?? 'No data available'); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/challan-searches/show.blade.php ENDPATH**/ ?>