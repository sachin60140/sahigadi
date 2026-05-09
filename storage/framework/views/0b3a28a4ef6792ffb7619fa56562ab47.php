<div class="offcanvas-lg offcanvas-start" tabindex="-1" id="customerSidebar" aria-labelledby="customerSidebarLabel">
    <div class="offcanvas-header d-lg-none border-bottom bg-light">
        <h5 class="offcanvas-title fw-bold" id="customerSidebarLabel"><i class="bi bi-person-circle me-2"></i>My Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#customerSidebar" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-block p-3 p-lg-0">
        <div class="w-100">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <?php if(Auth::guard('customer')->user()->profile_image): ?>
                        <img src="<?php echo e(asset('storage/' . Auth::guard('customer')->user()->profile_image)); ?>" alt="Profile" class="rounded-circle mb-3 shadow-sm border border-2 border-white" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 border border-2 border-white shadow-sm" style="width: 80px; height: 80px;">
                            <i class="bi bi-person text-secondary" style="font-size: 2.5rem;"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="fw-bold mb-1"><?php echo e(Auth::guard('customer')->user()->name ?? 'User'); ?></h5>
                    <p class="text-muted small mb-1">+91 <?php echo e(Auth::guard('customer')->user()->phone); ?></p>
                    <p class="badge bg-secondary mb-3">ID: <?php echo e(Auth::guard('customer')->user()->customer_unique_id); ?></p>

                    <?php
                        $walletBalance = \App\Models\CustomerWallet::where('customer_id', Auth::guard('customer')->id())
                                            ->first()->balance ?? 0;
                    ?>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3 mt-2">
                        <p class="text-success small fw-semibold mb-1"><i class="bi bi-wallet2 me-1"></i> Wallet Balance</p>
                        <h4 class="text-success fw-bold mb-0">₹<?php echo e(number_format($walletBalance, 2)); ?></h4>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <ul class="nav flex-column nav-pills custom-sidebar-nav">
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('customer.dashboard') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('customer.dashboard')); ?>">
                                <i class="bi bi-grid-1x2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('customer.profile.edit') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('customer.profile.edit')); ?>">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('customer.enquiries.*') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('customer.enquiries.index')); ?>">
                                <i class="bi bi-chat-left-dots me-2"></i> Enquiries
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('customer.wallet.index') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('customer.wallet.index')); ?>">
                                <i class="bi bi-wallet2 me-2"></i> My Wallet
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-dark rounded-3 px-3 py-2" href="<?php echo e(route('sell-car.index')); ?>">
                                <i class="bi bi-car-front me-2"></i> Sell a Car
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('vehicle-search.*') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('vehicle-search.index')); ?>">
                                <i class="bi bi-search me-2"></i> RC Search
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('mahindra-service-history.*') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('mahindra-service-history.index')); ?>">
                                <i class="bi bi-truck-front me-2"></i> Mahindra Service History
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('maruti-service-history.*') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('maruti-service-history.index')); ?>">
                                <i class="bi bi-car-front me-2"></i> Maruti Service History
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo e(request()->routeIs('customer.challan-pdf.*') ? 'active bg-primary text-white' : 'text-dark'); ?> rounded-3 px-3 py-2" href="<?php echo e(route('customer.challan-pdf.index')); ?>">
                                <i class="bi bi-file-earmark-pdf me-2"></i> Challan PDF Search
                            </a>
                        </li>
                        <li class="nav-item mt-3 pt-3 border-top">
                            <form action="<?php echo e(route('customer.logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="nav-link text-danger w-100 text-start rounded-3 px-3 py-2 border-0 bg-transparent">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-sidebar-nav .nav-link {
    transition: all 0.2s;
    font-weight: 500;
}
.custom-sidebar-nav .nav-link:hover:not(.active) {
    background-color: #f8f9fa;
    color: var(--bs-primary) !important;
}
</style>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/partials/sidebar.blade.php ENDPATH**/ ?>