<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    <!-- Static Pages -->
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('cars.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('sell-car.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Brands -->
    @foreach($brands as $brand)
    <url>
        <loc>{{ route('cars.brand', $brand->slug) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    <!-- Cities -->
    @if(isset($cities))
        @foreach($cities as $city)
        <url>
            <loc>{{ route('cars.city', str_replace(' ', '-', strtolower($city))) }}</loc>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
        @endforeach
    @endif

    <!-- Brand + City Combinations -->
    @if(isset($brandCityCombinations))
        @foreach($brandCityCombinations as $combo)
        <url>
            <loc>{{ route('cars.brand.city', [$combo['brand_slug'], $combo['city']]) }}</loc>
            <changefreq>daily</changefreq>
            <priority>0.7</priority>
        </url>
        @endforeach
    @endif

    <!-- Dealer Cars -->
    @foreach($cars as $car)
    @php
        $carImages = $car->images;
        $firstImage = $carImages->first();
    @endphp
    <url>
        <loc>{{ route('car.detail', $car->slug) }}</loc>
        <lastmod>{{ $car->updated_at->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        @if($firstImage)
        <image:image>
            <image:loc>{{ $firstImage->url }}</image:loc>
            <image:title>{{ $car->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach

    <!-- Customer Listings -->
    @foreach($customerListings as $listing)
    @php
        $listingImages = json_decode($listing->images, true) ?? [];
        $firstListingImage = count($listingImages) > 0 ? $listingImages[0] : null;
    @endphp
    <url>
        <loc>{{ route('car.detail', $listing->slug) }}</loc>
        <lastmod>{{ $listing->updated_at->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        @if($firstListingImage)
        <image:image>
            <image:loc>{{ asset('storage/' . $firstListingImage) }}</image:loc>
            <image:title>{{ $listing->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach

</urlset>
