<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $pageData = is_array($page ?? null) ? $page : [];
        $component = (string) data_get($pageData, 'component', '');
        $props = (array) data_get($pageData, 'props', []);
        $service = (array) data_get($props, 'service', []);
        $seo = (array) data_get($props, 'seo', []);
        $car = (array) data_get($props, 'car', []);
        $dealer = (array) data_get($props, 'dealer', []);

        $seoTitle = match ($component) {
            'Public/Home' => 'SahiGadi - Verified Used Cars in Bihar | Buy & Sell Second Hand Cars',
            'Public/Cars/Index' => data_get($props, 'seoTitle', 'Used Cars for Sale - Buy Pre-owned Cars | SahiGadi'),
            'Public/Cars/Show' => data_get($seo, 'title', data_get($car, 'title', 'Used Car Details').' | SahiGadi'),
            'Public/DealerCatalog' => data_get($props, 'seoTitle', (data_get($dealer, 'name', 'Verified Dealer').' - Car Listings | SahiGadi')),
            'Public/VerifiedDealers' => data_get($props, 'seoTitle', 'Verified Used Car Dealers | SahiGadi'),
            'Public/Contact' => data_get($props, 'seoTitle', 'Contact SahiGadi - Used Car Support in Bihar'),
            'Public/LegalPage' => data_get($props, 'title', 'Legal Information').' - SahiGadi',
            'Public/SellCar' => 'Sell Your Car in Bihar | List Your Used Car - SahiGadi',
            'Public/Services/Lookup' => data_get($service, 'seoTitle', data_get($service, 'title', 'Vehicle Report').' | SahiGadi'),
            'Public/Customer/Login', 'Auth/CustomerLogin' => 'Customer Login - SahiGadi',
            'Auth/DealerLogin' => 'Dealer Login - SahiGadi',
            'Auth/DealerForgotPassword' => 'Dealer Password Recovery - SahiGadi',
            'Dealer/Register' => 'Dealer Registration - SahiGadi',
            'Admin/Auth/Login' => 'Admin Login - SahiGadi',
            default => 'SahiGadi',
        };

        $seoDescription = match ($component) {
            'Public/Home' => 'Buy, sell and compare pre-owned cars in Bihar. Explore verified used cars and trusted local sellers with SahiGadi.',
            'Public/Cars/Index' => data_get($props, 'seoDescription', 'Browse verified used cars for sale and compare pre-owned car listings across Bihar with SahiGadi.'),
            'Public/Cars/Show' => data_get($seo, 'description', 'Review this verified used car listing, specifications, price and seller details on SahiGadi.'),
            'Public/DealerCatalog' => data_get($props, 'seoDescription', 'Browse verified pre-owned cars from '.data_get($dealer, 'name', 'this trusted dealer').'.'),
            'Public/VerifiedDealers' => data_get($props, 'seoDescription', 'Browse verified car dealers and trusted local used-car inventory across Bihar.'),
            'Public/Contact' => data_get($props, 'seoDescription', 'Contact SahiGadi for used car buying, selling, dealer onboarding and marketplace support.'),
            'Public/LegalPage' => data_get($props, 'description', 'Read SahiGadi legal and marketplace policy information.'),
            'Public/SellCar' => 'Sell your used car in Bihar with SahiGadi and connect with genuine local buyers and verified dealers.',
            'Public/Services/Lookup' => data_get($service, 'seoDescription', data_get($service, 'description', 'Search trusted vehicle information with SahiGadi.')),
            default => 'SahiGadi connects used-car buyers, sellers and verified dealers across Bihar.',
        };

        $indexable = str_starts_with($component, 'Public/')
            && ! str_contains($component, '/Payment')
            && ! str_contains($component, 'Result')
            && ! str_contains($component, '/Customer/Login');
        $seoRobots = $indexable
            ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'
            : 'noindex, nofollow';
        $seoCanonical = data_get($props, 'canonical')
            ?? data_get($service, 'canonical')
            ?? request()->url();
        $seoImage = data_get($props, 'ogImage')
            ?? data_get($car, 'main_image_url')
            ?? asset('images/og-image.png');
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ $seoTitle }}</title>
    <meta data-inertia="description" name="description" content="{{ $seoDescription }}">
    <meta data-inertia="robots" name="robots" content="{{ $seoRobots }}">
    @if($indexable)
        <link data-inertia="canonical" rel="canonical" href="{{ $seoCanonical }}">
        <meta data-inertia="og-title" property="og:title" content="{{ $seoTitle }}">
        <meta data-inertia="og-description" property="og:description" content="{{ $seoDescription }}">
        <meta data-inertia="og-image" property="og:image" content="{{ $seoImage }}">
        <meta data-inertia="og-type" property="og:type" content="website">
        <meta data-inertia="twitter-card" name="twitter:card" content="summary_large_image">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    @inertia
</body>
</html>
