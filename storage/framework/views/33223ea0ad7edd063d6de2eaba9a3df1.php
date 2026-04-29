<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9PBTKQDNF5"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-9PBTKQDNF5');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dealer Dashboard'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Dealer Dashboard for SAHIGADI'); ?>">
    <link rel="canonical" href="<?php echo $__env->yieldContent('canonical', url()->current()); ?>">
    
    <!-- OpenGraph / Facebook -->
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_type', 'website'); ?>">
    <meta property="og:url" content="<?php echo $__env->yieldContent('og_url', url()->current()); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('og_title', 'Dealer Dashboard - SAHIGADI'); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('og_description', 'Dealer Dashboard for SAHIGADI'); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('og_image', asset('images/og-image.png')); ?>">
    <meta property="og:site_name" content="SAHIGADI">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo $__env->yieldContent('twitter_url', url()->current()); ?>">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('twitter_title', 'Dealer Dashboard - SAHIGADI'); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('twitter_description', 'Dealer Dashboard for SAHIGADI'); ?>">
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('twitter_image', asset('images/og-image.png')); ?>">
    <meta name="twitter:site" content="@Sahigadi">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
        }
        .sidebar-brand {
            padding: 24px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .sidebar-brand h5 { font-weight: 700; letter-spacing: 1px; color: #0f172a; }
        .sidebar-nav { padding: 16px 0; }
        .sidebar a {
            color: #475569;
            text-decoration: none;
            padding: 12px 20px;
            margin: 4px 16px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .sidebar a i { margin-right: 12px; font-size: 1.1rem; }
        .sidebar a:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .sidebar a.active {
            background: #3b82f6;
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .sidebar a .badge { font-size: 0.75rem; padding: 0.35em 0.65em; margin-left: auto; }
        .sidebar-divider { height: 1px; background: #e2e8f0; margin: 16px 20px; }
        .content-area {
            padding: 30px;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Mobile Topbar -->
        <nav class="navbar navbar-light bg-white d-md-none px-3 py-2 border-bottom" style="border-color: #e2e8f0!important;">
            <a class="navbar-brand fw-bold d-flex align-items-center text-dark" href="#">
                <div class="bg-primary text-white p-1 rounded-2 me-2 shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-car-front-fill fs-6"></i>
                </div>
                SAHIGADI
            </a>
            <button class="navbar-toggler border-0 shadow-none text-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#dealerSidebar" aria-controls="dealerSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>

        <div class="row g-0">
            <div class="col-md-2 sidebar p-0 offcanvas-md offcanvas-start" tabindex="-1" id="dealerSidebar" aria-labelledby="dealerSidebarLabel">
                <div class="offcanvas-header d-md-none border-bottom bg-light" style="border-color: #e2e8f0!important;">
                    <h5 class="offcanvas-title text-dark fw-bold" id="dealerSidebarLabel"><i class="bi bi-car-front-fill text-primary me-2"></i>SAHIGADI Portal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#dealerSidebar" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0 flex-column">
                    <div class="sidebar-brand text-center d-none d-md-flex flex-column align-items-center">
                        <div class="bg-primary text-white p-2 rounded-3 mb-2 shadow" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-car-front-fill fs-4"></i>
                        </div>
                        <h5 class="mb-0">SAHIGADI</h5>
                        <small class="text-secondary mt-1 fw-medium text-uppercase" style="font-size:0.7rem; letter-spacing:0.5px;">Dealer Portal</small>
                    </div>
                    <nav class="nav flex-column sidebar-nav w-100">
                    <a href="<?php echo e(route('dealer.dashboard')); ?>" class="<?php echo e(request()->routeIs('dealer.dashboard') ? 'active' : ''); ?>">
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                    <a href="<?php echo e(route('dealer.cars.index')); ?>" class="<?php echo e(request()->routeIs('dealer.cars.*') ? 'active' : ''); ?>">
                        <i class="bi bi-car-front"></i> My Inventory
                    </a>
                    <a href="<?php echo e(route('dealer.enquiries.index')); ?>" class="<?php echo e(request()->routeIs('dealer.enquiries.*') ? 'active' : ''); ?>">
                        <i class="bi bi-chat-left-dots"></i> Enquiries
                        <?php
                            $newEnquiries = auth('dealer')->user()->enquiries()->where('status', 'new')->count();
                        ?>
                        <?php if($newEnquiries > 0): ?>
                            <span class="badge bg-danger rounded-pill"><?php echo e($newEnquiries); ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo e(route('dealer.wallet.index')); ?>" class="<?php echo e(request()->routeIs('dealer.wallet.*') ? 'active' : ''); ?>">
                        <i class="bi bi-wallet2"></i> Wallet Info
                    </a>
                    <a href="<?php echo e(route('dealer.plans.index')); ?>" class="<?php echo e(request()->routeIs('dealer.plans.*') ? 'active' : ''); ?>">
                        <i class="bi bi-rocket-takeoff"></i> Active Plans
                    </a>
                    <div class="sidebar-divider"></div>
                    <div class="px-4 mb-2 mt-1"><small class="text-secondary fw-bold text-uppercase" style="font-size:0.7rem;">Utility Services</small></div>
                    <a href="<?php echo e(route('dealer.vehicle-search.index')); ?>" class="<?php echo e(request()->routeIs('dealer.vehicle-search.*') ? 'active' : ''); ?>">
                        <i class="bi bi-card-text"></i> Vahan RC Check
                    </a>
                    <a href="<?php echo e(route('dealer.challan-search.index')); ?>" class="<?php echo e(request()->routeIs('dealer.challan-search.*') ? 'active' : ''); ?>">
                        <i class="bi bi-receipt-cutoff"></i> E-Challans
                    </a>
                    <a href="<?php echo e(route('dealer.service-history.index')); ?>" class="<?php echo e(request()->routeIs('dealer.service-history.*') ? 'active' : ''); ?>">
                        <i class="bi bi-tools"></i> Mahindra History
                    </a>
                    <a href="<?php echo e(route('dealer.maruti-service-history.index')); ?>" class="<?php echo e(request()->routeIs('dealer.maruti-service-history.*') ? 'active' : ''); ?>">
                        <i class="bi bi-wrench-adjustable-circle"></i> Maruti History
                    </a>
                    <div class="sidebar-divider"></div>
                    <a href="<?php echo e(route('dealer.profile.edit')); ?>" class="<?php echo e(request()->routeIs('dealer.profile.*') ? 'active' : ''); ?>">
                        <i class="bi bi-person-circle"></i> My Profile
                    </a>
                    <a href="<?php echo e(route('dealer.logout')); ?>" class="text-danger hover-danger">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </a>
                </nav>
                </div> <!-- End offcanvas-body -->
            </div>
            <div class="col-md-10 content-area">
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if(session('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php echo e(session('warning')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/layouts/dealer.blade.php ENDPATH**/ ?>