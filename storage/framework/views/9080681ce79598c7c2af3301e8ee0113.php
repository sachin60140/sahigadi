<?php $__env->startPush('styles'); ?>
<style>
.kpi-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    background: #fff;
    padding: 24px;
}
.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.kpi-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
}
/* Colors */
.bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
.bg-info-soft { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
.bg-purple-soft { background-color: rgba(111, 66, 193, 0.1); color: #6f42c1; }
.text-purple { color: #6f42c1 !important; }

.fw-800 { font-weight: 800; }
.mini-stat {
    font-size: 0.85rem;
    font-weight: 500;
}
.quick-action-card {
    border-radius: 12px;
    border: 1px solid #eee;
    transition: all 0.2s ease;
}
.quick-action-card:hover {
    background-color: var(--bs-light);
    border-color: #ddd;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Admin Dashboard - SAHIGADI'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-rocket-takeoff text-primary me-2"></i>Admin Command Center</h3>
        <p class="text-muted mb-0">Platform overview, revenue metrics, and system activity.</p>
    </div>
    <div class="text-end d-none d-md-block">
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border">
            <i class="bi bi-calendar3 me-2 text-primary"></i> <span class="fw-medium text-dark"><?php echo e(now()->format('l, d M Y')); ?></span>
        </div>
    </div>
</div>

<!-- Primary KPIs -->
<div class="row g-4 mb-4">
    <!-- Revenue / Wallet -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card h-100 border-start border-success border-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Wallet Recharges</h6>
                    <h2 class="fw-800 mb-0 text-success">₹<?php echo e(number_format($stats['total_wallet_recharges'])); ?></h2>
                    <p class="mb-0 mt-2 mini-stat text-muted">Platform Liquidity</p>
                </div>
                <div class="kpi-icon-wrapper bg-success-soft">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <a href="<?php echo e(route('admin.wallet-recharges.index')); ?>" class="stretched-link"></a>
        </div>
    </div>

    <!-- Active Dealers -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card h-100 border-start border-primary border-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Verified Dealers</h6>
                    <h2 class="fw-800 mb-0 text-dark"><?php echo e(number_format($stats['approved_dealers'])); ?></h2>
                    <p class="mb-0 mt-2 mini-stat text-warning"><i class="bi bi-hourglass-split me-1"></i><?php echo e($stats['pending_dealers']); ?> Pending Approval</p>
                </div>
                <div class="kpi-icon-wrapper bg-primary-soft">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <a href="<?php echo e(route('admin.dealers.index')); ?>" class="stretched-link"></a>
        </div>
    </div>

    <!-- Managed Cars -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card h-100 border-start border-info border-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Active Car Listings</h6>
                    <h2 class="fw-800 mb-0 text-dark"><?php echo e(number_format($stats['approved_cars'] + $stats['approved_customer_listings'])); ?></h2>
                    <p class="mb-0 mt-2 mini-stat text-info"><i class="bi bi-car-front-fill me-1"></i>Total Network Inventory</p>
                </div>
                <div class="kpi-icon-wrapper bg-info-soft">
                    <i class="bi bi-car-front"></i>
                </div>
            </div>
            <a href="<?php echo e(route('admin.cars.index')); ?>" class="stretched-link"></a>
        </div>
    </div>

    <!-- Pending Action Items -->
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card h-100 border-start border-danger border-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Pending Approval</h6>
                    <h2 class="fw-800 mb-0 text-danger"><?php echo e(number_format($stats['pending_cars'] + $stats['pending_customer_listings'] + $stats['pending_dealers'])); ?></h2>
                    <p class="mb-0 mt-2 mini-stat text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Required Action</p>
                </div>
                <div class="kpi-icon-wrapper bg-danger-soft">
                    <i class="bi bi-bell-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary KPIs & Actions -->
<div class="row g-4">
    <!-- API Lookups & Service Tracking -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-braces-asterisk text-purple me-2"></i>API Gateway Usage</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-3 bg-light border h-100">
                            <div class="kpi-icon-wrapper bg-purple-soft me-3 shadow-sm rounded-circle" style="width:50px;height:50px;font-size:1.2rem;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?php echo e(number_format($stats['vahan_lookups'])); ?></h4>
                                <span class="text-muted small fw-medium">Vahan (RC) Lookups</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-3 bg-light border h-100">
                            <div class="kpi-icon-wrapper bg-danger-soft me-3 shadow-sm rounded-circle" style="width:50px;height:50px;font-size:1.2rem;">
                                <i class="bi bi-wrench-adjustable-circle"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?php echo e(number_format($stats['mahindra_lookups'])); ?></h4>
                                <span class="text-muted small fw-medium">Mahindra Service Lookups</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-3 bg-light border h-100">
                            <div class="kpi-icon-wrapper bg-primary-soft me-3 shadow-sm rounded-circle" style="width:50px;height:50px;font-size:1.2rem;">
                                <i class="bi bi-tools"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?php echo e(number_format($stats['maruti_lookups'])); ?></h4>
                                <span class="text-muted small fw-medium">Maruti Service Lookups</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-3 bg-light border h-100">
                            <div class="kpi-icon-wrapper bg-warning-soft me-3 shadow-sm rounded-circle" style="width:50px;height:50px;font-size:1.2rem;">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?php echo e(number_format($stats['challan_lookups'])); ?></h4>
                                <span class="text-muted small fw-medium">E-Challan Lookups</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links & Comm -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body p-4">
                
                <a href="<?php echo e(route('admin.contact-enquiries.index')); ?>" class="text-decoration-none">
                    <div class="quick-action-card p-3 mb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="text-primary me-3 fs-3"><i class="bi bi-envelope-paper"></i></div>
                            <div>
                                <h6 class="text-dark fw-bold mb-0">Contact Support</h6>
                                <small class="text-muted">Unread Help Tickets</small>
                            </div>
                        </div>
                        <span class="badge <?php echo e($stats['contact_enquiries'] > 0 ? 'bg-danger' : 'bg-success'); ?> rounded-pill"><?php echo e($stats['contact_enquiries']); ?></span>
                    </div>
                </a>

                <a href="<?php echo e(route('admin.plans.index')); ?>" class="text-decoration-none">
                    <div class="quick-action-card p-3 mb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="text-success me-3 fs-3"><i class="bi bi-box-seam"></i></div>
                            <div>
                                <h6 class="text-dark fw-bold mb-0">Subscription Plans</h6>
                                <small class="text-muted">Active Tiers</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill"><?php echo e($stats['total_plans']); ?> Plans</span>
                    </div>
                </a>

                <a href="<?php echo e(route('admin.enquiries.index')); ?>" class="text-decoration-none">
                    <div class="quick-action-card p-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="text-info me-3 fs-3"><i class="bi bi-chat-text"></i></div>
                            <div>
                                <h6 class="text-dark fw-bold mb-0">Car Enquiries</h6>
                                <small class="text-muted">Total Customer Leads</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill"><?php echo e($stats['total_enquiries']); ?></span>
                    </div>
                </a>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>