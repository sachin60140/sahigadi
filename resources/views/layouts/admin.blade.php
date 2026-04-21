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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - SAHIGADI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #e94560;
            --success: #00bf63;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #0dcaf0;
                    --light: #f8f9fa;
            --dark: #0f0f23;
        }
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #f0f2f5;
        }
        .sidebar {
            min-height: 100vh;
            background: #0f172a;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
        }
        .sidebar-brand {
            padding: 18px 20px;
            background: rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
        }
        .sidebar-brand h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #fff;
        }
        .sidebar-brand span {
            color: var(--accent);
        }
        .sidebar-nav {
            padding: 15px 0;
            max-height: calc(100vh - 70px);
            overflow-y: auto;
            overflow-x: hidden;
            font-size: 0.85rem;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        
        .sidebar-nav .nav-link {
            color: #94a3b8;
            padding: 10px 16px;
            margin: 4px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            font-weight: 500;
            text-decoration: none;
        }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #f8fafc;
            transform: translateX(3px);
        }
        .sidebar-nav .nav-link.active {
            background: linear-gradient(90deg, var(--accent) 0%, #db3451 100%);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(233, 69, 96, 0.3);
            border-left: 3px solid #ff8fa3;
        }
        .sidebar-nav .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            transition: transform 0.2s;
        }
        .sidebar-nav .nav-link.active i {
            transform: scale(1.1);
        }
        .sidebar-nav .menu-header {
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 20px 20px 8px;
            margin-top: 5px;
        }
        .sidebar-nav .menu-header:first-child {
            padding-top: 5px;
            margin-top: 0;
        }
        .sidebar-nav .nav-item { position: relative; }
        .sidebar-nav .has-submenu > .nav-link { justify-content: space-between; gap: 0; }
        .sidebar-nav .has-submenu > .nav-link > span { display: flex; align-items: center; gap: 12px; }
        .sidebar-nav .has-submenu > .nav-link > i:last-child {
            font-size: 0.75rem;
            transition: transform 0.3s ease;
            width: auto;
        }
        .sidebar-nav .has-submenu.active > .nav-link > i:last-child,
        .sidebar-nav .has-submenu.show > .nav-link > i:last-child {
            transform: rotate(90deg);
        }
        .sidebar-nav .submenu {
            display: none;
            padding-left: 12px;
            background: rgba(0,0,0,0.15);
            border-radius: 8px;
            margin: 2px 12px;
            border-left: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-nav .has-submenu.active .submenu,
        .sidebar-nav .has-submenu.show .submenu { display: block; }
        .sidebar-nav .submenu .nav-link {
            padding: 8px 12px;
            font-size: 0.8rem;
            margin: 2px 0;
            border-radius: 6px;
            color: #64748b;
        }
        .sidebar-nav .submenu .nav-link:hover {
            color: #f8fafc;
            background: transparent;
            transform: translateX(2px);
        }
        .sidebar-nav .submenu .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
            font-weight: 500;
            border-left: none;
            box-shadow: none;
        }
        .sidebar-nav .submenu .nav-link i { font-size: 0.9rem; }
        .nav-link .badge {
            font-size: 0.65rem;
            padding: 3px 6px;
            border-radius: 8px;
        }
        .main-content {
            margin-left: 260px;
            padding: 25px;
            min-height: 100vh;
        }
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar h4 {
            margin: 0;
            font-weight: 600;
        }
        .kpi-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: none;
            height: 100%;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .kpi-icon.primary { background: rgba(233, 69, 96, 0.1); color: var(--accent); }
        .kpi-icon.success { background: rgba(0, 191, 99, 0.1); color: var(--success); }
        .kpi-icon.warning { background: rgba(255, 193, 7, 0.1); color: var(--warning); }
        .kpi-icon.info { background: rgba(13, 202, 240, 0.1); color: var(--info); }
        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 10px;
        }
        .kpi-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .table-modern {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .table-modern thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }
        .table-modern thead th {
            font-weight: 600;
            padding: 15px 20px;
            border: none;
        }
        .table-modern tbody td {
            padding: 15px 20px;
            vertical-align: middle;
            border-color: #f0f0f0;
        }
        .table-modern tbody tr:hover {
            background: #f8f9fa;
        }
        .badge-modern {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        .btn-modern {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <button class="btn btn-primary d-lg-none position-fixed" style="z-index:1001; top: 10px; left: 10px;" onclick="document.querySelector('.sidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
    </button>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-car-front-fill"></i> SAHI<span>GADI</span></h4>
            <small class="text-white-50">Admin Panel</small>
        </div>
        <nav class="sidebar-nav">
            <div class="menu-header">Main Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="{{ route('admin.dealers.index') }}" class="nav-link {{ request()->routeIs('admin.dealers.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Dealers
            </a>
            <a href="{{ route('admin.cars.index') }}" class="nav-link {{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">
                <i class="bi bi-car-front-fill"></i> Cars
            </a>
            <a href="{{ route('admin.customer-listings.index') }}" class="nav-link {{ request()->routeIs('admin.customer-listings.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Customer Listings
            </a>
            <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i> Plans
            </a>
            <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Brands
            </a>
            <a href="{{ route('admin.enquiries.index') }}" class="nav-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots-fill"></i> Enquiries
            </a>
            <a href="{{ route('admin.contact-enquiries.index') }}" class="nav-link {{ request()->routeIs('admin.contact-enquiries.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-open-fill"></i> Contact Enquiries
            </a>

            <div class="menu-header">Services</div>
            <div class="nav-item has-submenu {{ request()->routeIs('admin.vehicle-searches.*') || request()->routeIs('admin.service-tracking.vehicle-search') ? 'active' : '' }}">
                <a href="#" class="nav-link" onclick="toggleSubmenu(this); return false;">
                    <span><i class="bi bi-car"></i> RC Search</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('admin.vehicle-searches.index') }}" class="nav-link {{ request()->routeIs('admin.vehicle-searches.index') ? 'active' : '' }}">
                        <i class="bi bi-list"></i> All Searches
                    </a>
                    <a href="{{ route('admin.service-tracking.vehicle-search') }}" class="nav-link {{ request()->routeIs('admin.service-tracking.vehicle-search') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Tracking
                    </a>
                </div>
            </div>
            <div class="nav-item has-submenu {{ request()->routeIs('admin.service-histories.*') || request()->routeIs('admin.service-tracking.service-history') ? 'active' : '' }}">
                <a href="#" class="nav-link" onclick="toggleSubmenu(this); return false;">
                    <span><i class="bi bi-wrench"></i> Service History</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('admin.service-histories.index') }}" class="nav-link {{ request()->routeIs('admin.service-histories.index') ? 'active' : '' }}">
                        <i class="bi bi-list"></i> Mahindra Service History
                    </a>
                    <a href="{{ route('admin.maruti-service-histories.index') }}" class="nav-link {{ request()->routeIs('admin.maruti-service-histories.*') ? 'active' : '' }}">
                        <i class="bi bi-car-front"></i> Maruti Service History
                    </a>
                    <a href="{{ route('admin.service-tracking.service-history') }}" class="nav-link {{ request()->routeIs('admin.service-tracking.service-history') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Tracking
                    </a>
                </div>
            </div>
            <div class="nav-item has-submenu {{ request()->routeIs('admin.challan-searches.*') || request()->routeIs('admin.service-tracking.challan-search') ? 'active' : '' }}">
                <a href="#" class="nav-link" onclick="toggleSubmenu(this); return false;">
                    <span><i class="bi bi-receipt"></i> E-Challan</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('admin.challan-searches.index') }}" class="nav-link {{ request()->routeIs('admin.challan-searches.index') ? 'active' : '' }}">
                        <i class="bi bi-list"></i> All Searches
                    </a>
                    <a href="{{ route('admin.service-tracking.challan-search') }}" class="nav-link {{ request()->routeIs('admin.service-tracking.challan-search') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Tracking
                    </a>
                </div>
            </div>

            <div class="menu-header">Dealer Finance</div>
            <a href="{{ route('admin.wallet-recharges.index') }}" class="nav-link {{ request()->routeIs('admin.wallet-recharges.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Wallet Recharges
            </a>

            <div class="menu-header">Customer Finance</div>
            <a href="{{ route('admin.customer-transactions.index') }}" class="nav-link {{ request()->routeIs('admin.customer-transactions.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Payments & Refunds
            </a>

            <div class="menu-header">Settings</div>
            <a href="{{ route('admin.payment-settings.index') }}" class="nav-link {{ request()->routeIs('admin.payment-settings.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Payment Settings
            </a>
            <a href="{{ route('admin.change-password') }}" class="nav-link {{ request()->routeIs('admin.change-password') ? 'active' : '' }}">
                <i class="bi bi-key"></i> Change Password
            </a>
            <hr class="border-secondary mx-3 my-3">
            <a href="{{ route('admin.logout') }}" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSubmenu(element) {
            const parent = element.closest('.has-submenu');
            parent.classList.toggle('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeLink = document.querySelector('.sidebar-nav .nav-link.active');
            if (activeLink) {
                const parentSubmenu = activeLink.closest('.submenu');
                if (parentSubmenu) {
                    parentSubmenu.parentElement.classList.add('active');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
