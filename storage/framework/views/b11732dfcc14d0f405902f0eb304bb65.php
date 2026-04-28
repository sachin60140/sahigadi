<?php $__env->startPush('styles'); ?>
<style>
.summary-card { transition: transform 0.2s; }
.summary-card:hover { transform: translateY(-3px); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Challan Result - Dealer Panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <?php if(isset($success) && !$success): ?>
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Search Failed</h5>
                    <p class="mb-0"><?php echo e($message); ?></p>
                </div>
            <?php endif; ?>

            <?php if($challan->is_success): ?>
                <?php
                $challans = $challan->challan_data ?? [];
                $paidAmount = 0;
                $unpaidAmount = 0;
                $pendingAmount = 0;
                $physicalCourt = 0;
                $virtualCourt = 0;
                $noCourt = 0;
                
                usort($challans, function($a, $b) {
                    $dateA = isset($a['dateChallan']) ? strtotime($a['dateChallan']) : 0;
                    $dateB = isset($b['dateChallan']) ? strtotime($b['dateChallan']) : 0;
                    return $dateB - $dateA;
                });
                
                foreach ($challans as $c) {
                    $amount = floatval($c['amountChallan'] ?? 0);
                    $status = strtolower($c['status'] ?? '');
                    
                    if ($status == 'paid') {
                        $paidAmount += $amount;
                    } elseif ($status == 'pending') {
                        $pendingAmount += $amount;
                    } else {
                        $unpaidAmount += $amount;
                    }
                    
                    $courtStatus = strtolower($c['court_status_desc'] ?? '');
                    if ($courtStatus == 'physical court') {
                        $physicalCourt++;
                    } elseif ($courtStatus == 'virtual court') {
                        $virtualCourt++;
                    } else {
                        $noCourt++;
                    }
                }
                ?>

                <div class="card mb-4">
                    <div class="card-header <?php echo e(($unpaidAmount + $pendingAmount) > 0 ? 'bg-danger' : 'bg-success'); ?> text-white">
                        <h4 class="mb-0">
                            <i class="bi <?php echo e(($unpaidAmount + $pendingAmount) > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle'); ?> me-2"></i>
                            <?php echo e(($unpaidAmount + $pendingAmount) > 0 ? 'Challans Found' : 'No Pending Challans'); ?>

                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($cached) && $cached): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-clock-history me-2"></i>Retrieved from cache (last 24 hours)
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5>Vehicle: <?php echo e($challan->vehicle_number); ?></h5>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card summary-card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">₹<?php echo e(number_format($unpaidAmount + $pendingAmount)); ?></h3>
                                        <small>Pending Amount</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">₹<?php echo e(number_format($paidAmount)); ?></h3>
                                        <small>Paid Amount</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card bg-warning">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">₹<?php echo e(number_format($unpaidAmount)); ?></h3>
                                        <small>Unpaid Amount</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?php echo e($challan->challan_count); ?></h3>
                                        <small>Total Challans</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if($physicalCourt > 0 || $virtualCourt > 0 || $noCourt > 0): ?>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card summary-card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?php echo e($physicalCourt); ?></h3>
                                        <small>Physical Court</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card summary-card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?php echo e($virtualCourt); ?></h3>
                                        <small>Virtual Court</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card summary-card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?php echo e($noCourt); ?></h3>
                                        <small>No Court Data</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($challan->challan_data && count($challan->challan_data) > 0): ?>
                            <h5 class="mb-3">Challan Details <small class="text-muted">(Sorted by Date - Newest First)</small></h5>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Challan No</th>
                                            <th>Date</th>
                                            <th>State</th>
                                            <th>Location</th>
                                            <th>Offence</th>
                                            <th>RTO</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Court</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $challans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $status = strtolower($c['status'] ?? '');
                                            $isPaid = ($status == 'paid');
                                            $isPending = ($status == 'pending');
                                        ?>
                                        <tr>
                                            <td><?php echo e($c['challanNo'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($c['dateChallan'] ? \Carbon\Carbon::parse($c['dateChallan'])->format('d M Y') : 'N/A'); ?></td>
                                            <td><?php echo e($c['State'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($c['locationChallan'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($c['detailsViolation'][0]['offence'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($c['nameRTO'] ?? 'N/A'); ?></td>
                                            <td class="<?php echo e($isPaid ? 'text-success' : ($isPending ? 'text-warning' : 'text-danger')); ?> fw-bold">₹<?php echo e(number_format($c['amountChallan'] ?? 0)); ?></td>
                                            <td>
                                                <?php if($isPaid): ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php elseif($isPending): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Unpaid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php $courtStatus = $c['court_status_desc'] ?? ''; ?>
                                                <?php if($courtStatus): ?>
                                                    <?php if(strtolower($courtStatus) == 'physical court'): ?>
                                                        <span class="badge bg-secondary"><?php echo e($courtStatus); ?></span>
                                                    <?php elseif(strtolower($courtStatus) == 'virtual court'): ?>
                                                        <span class="badge bg-info"><?php echo e($courtStatus); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-outline-dark"><?php echo e($courtStatus); ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>No pending challans found.
                            </div>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Search Date: <?php echo e($challan->created_at->format('d M Y, h:i A')); ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('dealer.challan-search.pdf', $challan)); ?>" class="btn btn-primary">
                                        <i class="bi bi-download me-2"></i>Download PDF
                                    </a>
                                    <a href="<?php echo e(route('dealer.challan-search.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="bi bi-x-circle me-2"></i>No Records Found</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?php echo e($challan->error_message ?? 'No challan records found.'); ?></p>
                        <a href="<?php echo e(route('dealer.challan-search.index')); ?>" class="btn btn-outline-secondary mt-3">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dealer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\dealer\challan-searches\result.blade.php ENDPATH**/ ?>