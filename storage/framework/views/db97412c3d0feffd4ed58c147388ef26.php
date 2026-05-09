<?php $__env->startSection('title', 'Challan PDF Search Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Search Logs</h6>
            <a href="<?php echo e(route('admin.challan-pdf.export-logs')); ?>" class="btn btn-sm btn-success">Export to CSV</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Challan/Vehicle Number</th>
                            <th>User Type</th>
                            <th>User Name</th>
                            <th>Status</th>
                            <th>Charge</th>
                            <th>Error Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($log->created_at->format('d M Y, h:i A')); ?></td>
                            <td><?php echo e($log->vehicle_number); ?></td>
                            <td>
                                <?php if($log->customer_id): ?>
                                    <span class="badge bg-info">Customer</span>
                                <?php elseif($log->dealer_id): ?>
                                    <span class="badge bg-primary">Dealer</span>
                                <?php else: ?>
                                    Unknown
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($log->customer_id): ?>
                                    <?php echo e($log->customer->name ?? 'N/A'); ?>

                                    <?php if(isset($log->customer->customer_unique_id)): ?>
                                        <br><small class="text-muted"><?php echo e($log->customer->customer_unique_id); ?></small>
                                    <?php endif; ?>
                                <?php elseif($log->dealer_id): ?>
                                    <?php echo e($log->dealer->name ?? 'N/A'); ?>

                                    <?php if(isset($log->dealer->dealer_unique_id)): ?>
                                        <br><small class="text-muted"><?php echo e($log->dealer->dealer_unique_id); ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($log->is_success): ?>
                                    <span class="badge bg-success">Success</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td>₹<?php echo e(number_format($log->charge_amount, 2)); ?></td>
                            <td><?php echo e($log->error_message ?? '-'); ?></td>
                            <td>
                                <?php if($log->is_success && $log->pdf_url): ?>
                                    <a href="<?php echo e($log->pdf_url); ?>" target="_blank" class="btn btn-sm btn-primary mb-1">View PDF</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#logModal<?php echo e($log->id); ?>">
                                    View Details
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="logModal<?php echo e($log->id); ?>" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog modal-lg">
                                    <div class="modal-content text-start">
                                      <div class="modal-header">
                                        <h5 class="modal-title">API Details for <?php echo e($log->vehicle_number); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        <h6>API Request:</h6>
                                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 200px; overflow-y: auto;"><code><?php echo e(json_encode($log->api_request, JSON_PRETTY_PRINT)); ?></code></pre>
                                        
                                        <h6 class="mt-3">API Response:</h6>
                                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 300px; overflow-y: auto;"><code><?php echo e(json_encode($log->api_response, JSON_PRETTY_PRINT)); ?></code></pre>
                                        
                                        <?php if($log->error_message): ?>
                                            <h6 class="mt-3 text-danger">Error Message:</h6>
                                            <p class="text-danger"><?php echo e($log->error_message); ?></p>
                                        <?php endif; ?>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">No logs found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?php echo e($logs->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/challan_pdf/logs.blade.php ENDPATH**/ ?>