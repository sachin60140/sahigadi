<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    <!-- Static Pages -->
    <url>
        <loc><?php echo e(url('/')); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo e(route('cars.index')); ?></loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo e(route('sell-car.index')); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Brands -->
    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(route('cars.brand', $brand->slug)); ?></loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Cities -->
    <?php if(isset($cities)): ?>
        <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <url>
            <loc><?php echo e(route('cars.city', str_replace(' ', '-', strtolower($city)))); ?></loc>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Brand + City Combinations -->
    <?php if(isset($brandCityCombinations)): ?>
        <?php $__currentLoopData = $brandCityCombinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $combo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <url>
            <loc><?php echo e(route('cars.brand.city', [$combo['brand_slug'], $combo['city']])); ?></loc>
            <changefreq>daily</changefreq>
            <priority>0.7</priority>
        </url>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Dealer Cars -->
    <?php $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $carImages = $car->images;
        $firstImage = $carImages->first();
    ?>
    <url>
        <loc><?php echo e(route('car.detail', $car->slug)); ?></loc>
        <lastmod><?php echo e($car->updated_at->toIso8601String()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        <?php if($firstImage): ?>
        <image:image>
            <image:loc><?php echo e($firstImage->url); ?></image:loc>
            <image:title><?php echo e($car->title); ?></image:title>
        </image:image>
        <?php endif; ?>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Customer Listings -->
    <?php $__currentLoopData = $customerListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $listingImages = json_decode($listing->images, true) ?? [];
        $firstListingImage = count($listingImages) > 0 ? $listingImages[0] : null;
    ?>
    <url>
        <loc><?php echo e(route('car.detail', $listing->slug)); ?></loc>
        <lastmod><?php echo e($listing->updated_at->toIso8601String()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        <?php if($firstListingImage): ?>
        <image:image>
            <image:loc><?php echo e(asset('storage/' . $firstListingImage)); ?></image:loc>
            <image:title><?php echo e($listing->title); ?></image:title>
        </image:image>
        <?php endif; ?>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</urlset>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\sitemap\index.blade.php ENDPATH**/ ?>