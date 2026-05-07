<?php $__env->startSection('title', 'Maruti Service History - SAHI GADI'); ?>
<?php $__env->startSection('meta_description', 'Search maruti vehicle service history online. Get complete service records from authorized maruti service centers.'); ?>

<?php $__env->startSection('content'); ?>

<?php if(!auth('customer')->check()): ?>
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2"><i class="bi bi-car-front-fill me-2"></i>Maruti Service History</h1>
            <p class="mb-0">Get complete service records from authorized maruti service centers</p>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="container py-5">
    <?php if(auth('customer')->check()): ?>
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h2 class="fw-bold mb-1">Maruti Service History</h2>
                <p class="text-muted mb-0">Get complete service records from authorized maruti service centers</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row <?php echo e(auth('customer')->check() ? '' : 'justify-content-center'); ?>">
        <?php if(auth('customer')->check()): ?>
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            <?php echo $__env->make('frontend.customer.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="<?php echo e(auth('customer')->check() ? 'col-lg-9' : 'col-lg-8'); ?>">
            <div class="card border-0 shadow-lg <?php echo e(auth('customer')->check() ? 'rounded-4' : ''); ?>">
                <div class="card-header bg-primary text-white <?php echo e(auth('customer')->check() ? 'rounded-top-4' : ''); ?>">
                    <h5 class="mb-0"><i class="bi bi-search me-2"></i>Search Vehicle Service History</h5>
                </div>
                <div class="card-body <?php echo e(auth('customer')->check() ? 'p-4' : ''); ?>">
                    <form action="<?php echo e(route('maruti-service-history.search')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Vehicle Registration Number</label>
                            <input type="text" name="vehicle_number" class="form-control form-control-lg text-uppercase <?php $__errorArgs = ['vehicle_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="e.g. DL01AB1234" maxlength="20" required>
                            <?php $__errorArgs = ['vehicle_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Enter your vehicle's registration number</small>
                        </div>
                        
                        <?php if(!auth('customer')->check()): ?>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Your Name</label>
                                    <input type="text" name="customer_name" class="form-control <?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="Enter your name" required>
                                    <?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Mobile Number</label>
                                    <input type="tel" name="customer_phone" class="form-control <?php $__errorArgs = ['customer_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="10-digit mobile number" required maxlength="20">
                                    <?php $__errorArgs = ['customer_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Email (Optional)</label>
                                    <input type="email" name="customer_email" class="form-control" 
                                           placeholder="your@email.com">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <div class="bg-light px-3 py-2 rounded">
                                <span class="text-muted mb-0 me-2">Charge:</span>
                                <span class="fw-bold text-primary fs-5">₹<?php echo e(number_format($charge)); ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search me-2"></i>Search Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if(auth('customer')->check() && isset($history)): ?>
            <div class="card border-0 shadow-lg mt-4 rounded-4">
                <div class="card-header bg-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Your Search History</h5>
                </div>
                <div class="card-body p-4">
                    <?php if($history->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Vehicle Number</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($record->created_at->format('d M Y, h:i A')); ?></td>
                                            <td class="text-uppercase fw-bold"><?php echo e($record->vehicle_number); ?></td>
                                            <td>
                                                <?php if($record->is_success): ?>
                                                    <span class="badge bg-success">Success</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($record->is_success): ?>
                                                    <a href="<?php echo e(route('maruti-service-history.show', $record->id)); ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">No search history found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="card border-0 shadow-lg mt-4 <?php echo e(auth('customer')->check() ? 'rounded-4' : ''); ?>">
                <div class="card-header bg-white <?php echo e(auth('customer')->check() ? 'rounded-top-4' : ''); ?>">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                                <i class="bi bi-car-front text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Enter Vehicle Number</h6>
                            <small class="text-muted">Input your maruti car's registration number</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                                <i class="bi bi-receipt text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Get Service Records</h6>
                            <small class="text-muted">View all service history from authorized centers</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                                <i class="bi bi-file-earmark-pdf text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Download Report</h6>
                            <small class="text-muted">Get PDF report for reference</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-lg mt-4 <?php echo e(auth('customer')->check() ? 'rounded-4' : ''); ?>">
                <div class="card-header bg-white <?php echo e(auth('customer')->check() ? 'rounded-top-4' : ''); ?>">
                    <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>What You'll Get</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Complete service history from maruti authorized service centers</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Service dates, types, and descriptions</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Bill amounts (Parts & Labour)</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Dealer information and location</li>
                        <li class="list-group-item border-0 px-0 py-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Vehicle mileage at each service</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/maruti-service-history/index.blade.php ENDPATH**/ ?>