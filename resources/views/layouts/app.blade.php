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
    <title>@yield('title', 'SAHI GADI - Trusted Used Car Marketplace in Patna, Bihar')</title>
    <meta name="description" content="@yield('meta_description', 'Find the best verified pre-owned cars in Patna, Bihar.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- OpenGraph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', 'SAHI GADI - Used Car Marketplace')">
    <meta property="og:description" content="@yield('og_description', 'Find verified pre-owned cars in Patna, Bihar')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.png'))">
    <meta property="og:site_name" content="SAHI GADI">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', 'SAHI GADI - Used Car Marketplace')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Find verified pre-owned cars in Patna, Bihar')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-image.png'))">
    <meta name="twitter:site" content="@Sahigadi">
    
    <meta name="geo.region" content="IN-BR">
    <meta name="geo.placename" content="Patna">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #e94560;
            --light: #f8f9fa;
            --dark: #0f0f1a;
        }
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f5f6fa; }
        .navbar {
            background: var(--primary) !important;
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .navbar-brand { font-weight: 800; font-size: 1.8rem; letter-spacing: 2px; }
        .navbar-brand span { color: var(--accent); }
        .nav-link { font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { color: var(--accent) !important; }
        .btn-accent {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-accent:hover {
            background: #d63850;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(233, 69, 96, 0.4);
        }
        .btn-outline-accent {
            border: 2px solid var(--accent);
            color: var(--accent);
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-outline-accent:hover {
            background: var(--accent);
            color: white;
        }
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: var(--accent);
            opacity: 0.1;
            border-radius: 50%;
        }
        .hero-section h1 { font-weight: 800; font-size: 3.5rem; line-height: 1.2; }
        .hero-section span { color: var(--accent); }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .card-img-top { height: 200px; object-fit: cover; }
        .price-tag {
            background: var(--accent);
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: 700;
            display: inline-block;
        }
        .badge-featured {
            background: linear-gradient(135deg, #ffd700, #ffb300);
            color: #000;
            font-weight: 600;
        }
        .section-title { font-weight: 800; position: relative; display: inline-block; }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
        }
        footer { background: var(--dark); }
        .footer-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: 2px; }
        .footer-brand span { color: var(--accent); }
        .social-links a {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s;
        }
        .social-links a:hover { background: var(--accent); color: white; transform: translateY(-3px); }
        .search-box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(233, 69, 96, 0.15);
        }
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border-left: 4px solid var(--accent);
        }
        .stats-number { font-size: 2.5rem; font-weight: 800; color: var(--primary); }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="bi bi-car-front-fill"></i> SAHI <span>GADI</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="{{ route('cars.index') }}">Browse Cars</a>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link text-warning fw-semibold" href="{{ route('sell-car.index') }}">
                                <i class="bi bi-plus-circle me-1"></i>Sell Your Car
                            </a>
                        </li>
                        @if(auth('dealer')->check())
                            <li class="nav-item dropdown mx-2">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('dealer.dashboard') }}"><i class="bi bi-grid"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dealer.cars.index') }}"><i class="bi bi-car-front"></i> My Cars</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dealer.enquiries.index') }}"><i class="bi bi-chat-dots"></i> Enquiries</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dealer.wallet.index') }}"><i class="bi bi-wallet2"></i> Wallet</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dealer.plans.index') }}"><i class="bi bi-box-seam"></i> Plans</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="{{ route('dealer.logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item mx-2">
                                <a class="nav-link" href="{{ route('dealer.login') }}">Dealer Login</a>
                            </li>
                            <li class="nav-item mx-2">
                                <a href="{{ route('dealer.register') }}" class="btn btn-accent btn-sm">Register Dealer</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3">
                        <i class="bi bi-car-front-fill"></i> SAHI <span>GADI</span>
                    </div>
                    <p class="text-white-50 mb-4">Your trusted marketplace for verified pre-owned cars.</p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="mx-2"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="me-2"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="{{ route('cars.index') }}" class="text-white-50 text-decoration-none">Browse Cars</a></li>
                        <li class="mb-2"><a href="{{ route('dealer.register') }}" class="text-white-50 text-decoration-none">Become a Dealer</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Popular Brands</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('cars.brand', 'maruti-suzuki') }}" class="text-white-50 text-decoration-none">Maruti Suzuki</a></li>
                        <li class="mb-2"><a href="{{ route('cars.brand', 'hyundai') }}" class="text-white-50 text-decoration-none">Hyundai</a></li>
                        <li class="mb-2"><a href="{{ route('cars.brand', 'tata') }}" class="text-white-50 text-decoration-none">Tata</a></li>
                        <li class="mb-2"><a href="{{ route('cars.brand', 'mahindra') }}" class="text-white-50 text-decoration-none">Mahindra</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Contact Info</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Awani Enterprises</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>A-5, Sector 65, Noida, UP</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@sahigadi.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>+91 98188 23408</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-white-50 mb-0">&copy; {{ date('Y') }} SAHI GADI. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <a href="{{ route('privacy-policy') }}" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="{{ route('terms-of-use') }}" class="text-white-50 text-decoration-none me-3">Terms of Use</a>
                    <a href="{{ route('refund-policy') }}" class="text-white-50 text-decoration-none">Refund Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    @stack('json_ld')
    @stack('scripts')
</body>
</html>
