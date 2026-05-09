<?php $__env->startSection('title', 'Browse Used Cars - SAHI GADI'); ?>
<?php $__env->startSection('meta_title', 'Browse Used Cars - Pre-Owned Cars Marketplace in Bihar | SAHI GADI'); ?>
<?php $__env->startSection('meta_description', 'Browse thousands of verified pre-owned cars in Patna and Bihar. Filter by brand, price, km driven, fuel type. Quality assured used cars from trusted dealers.'); ?>
<?php $__env->startSection('meta_keywords', 'browse used cars, pre-owned cars, car marketplace, filter cars, used cars Patna, used cars Bihar'); ?>
<?php $__env->startSection('canonical', route('cars.index')); ?>

<?php $__env->startSection('og_type', 'website'); ?>
<?php $__env->startSection('og_url', route('cars.index')); ?>
<?php $__env->startSection('og_title', 'Browse Used Cars - Pre-Owned Cars Marketplace in Bihar | SAHI GADI'); ?>
<?php $__env->startSection('og_description', 'Browse thousands of verified pre-owned cars in Patna and Bihar. Filter by brand, price, km driven, fuel type.'); ?>
<?php $__env->startSection('twitter_title', 'Browse Used Cars - SAHI GADI'); ?>
<?php $__env->startSection('twitter_description', 'Browse thousands of verified pre-owned cars in Patna and Bihar.'); ?>

<?php if(isset($ogImage) && $ogImage): ?>
    <?php $__env->startSection('og_image', $ogImage); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<section class="py-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-2">Browse Used Cars</h1>
            <p class="mb-0">Find your perfect pre-owned car from our verified listings</p>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="filter-section">
                <h5 class="fw-bold mb-4"><i class="bi bi-funnel me-2"></i>Filter Cars</h5>
                <form action="<?php echo e(route('cars.index')); ?>" method="GET">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="keyword" class="form-control" value="<?php echo e(request('keyword')); ?>" placeholder="Car name, model...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">City</label>
                        <select name="city" class="form-select">
                            <option value="">All Cities</option>
                            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($city); ?>" <?php echo e(request('city') == $city ? 'selected' : ''); ?>><?php echo e($city); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Brand</label>
                        <select name="brand" class="form-select">
                            <option value="">All Brands</option>
                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand') == $brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Fuel Type</label>
                        <select name="fuel_type" class="form-select">
                            <option value="">All</option>
                            <option value="petrol" <?php echo e(request('fuel_type') == 'petrol' ? 'selected' : ''); ?>>Petrol</option>
                            <option value="diesel" <?php echo e(request('fuel_type') == 'diesel' ? 'selected' : ''); ?>>Diesel</option>
                            <option value="electric" <?php echo e(request('fuel_type') == 'electric' ? 'selected' : ''); ?>>Electric</option>
                            <option value="hybrid" <?php echo e(request('fuel_type') == 'hybrid' ? 'selected' : ''); ?>>Hybrid</option>
                            <option value="cng" <?php echo e(request('fuel_type') == 'cng' ? 'selected' : ''); ?>>CNG</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Price Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo e(request('min_price')); ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo e(request('max_price')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Transmission</label>
                        <select name="transmission" class="form-select">
                            <option value="">All</option>
                            <option value="manual" <?php echo e(request('transmission') == 'manual' ? 'selected' : ''); ?>>Manual</option>
                            <option value="automatic" <?php echo e(request('transmission') == 'automatic' ? 'selected' : ''); ?>>Automatic</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-accent w-100 mb-2">
                        <i class="bi bi-search me-2"></i>Apply Filters
                    </button>
                    <a href="<?php echo e(route('cars.index')); ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Clear Filters
                    </a>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-dark px-3 py-2"><?php echo e($cars->total() + $customerListings->count()); ?> Cars Found</span>
                </div>
                <select class="form-select w-auto" onchange="window.location.href=this.value" style="min-width: 200px;">
                    <option value="<?php echo e(route('cars.index', request()->except('sort'))); ?>" <?php echo e(!request('sort') || request('sort') == 'relevance' ? 'selected' : ''); ?>>Sort by: Relevance</option>
                    <option value="<?php echo e(route('cars.index', array_merge(request()->except('sort'), ['sort' => 'newest']))); ?>" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>Newest First</option>
                    <option value="<?php echo e(route('cars.index', array_merge(request()->except('sort'), ['sort' => 'price_low']))); ?>" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>Price: Low to High</option>
                    <option value="<?php echo e(route('cars.index', array_merge(request()->except('sort'), ['sort' => 'price_high']))); ?>" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>Price: High to Low</option>
                    <option value="<?php echo e(route('cars.index', array_merge(request()->except('sort'), ['sort' => 'km_low']))); ?>" <?php echo e(request('sort') == 'km_low' ? 'selected' : ''); ?>>KMs: Low to High</option>
                </select>
            </div>

            <div class="row">
                <?php $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <?php if($car->image_url): ?>
                                <img src="<?php echo e($car->image_url); ?>" class="card-img-top" alt="<?php echo e($car->title); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <?php if($car->isFeatured()): ?>
                                <span class="position-absolute top-0 start-0 badge badge-featured m-2">
                                    <i class="bi bi-star-fill me-1"></i>Featured
                                </span>
                            <?php endif; ?>
                            <span class="position-absolute top-0 end-0 badge bg-dark text-white m-2">
                                <?php echo e($car->year ?? 'N/A'); ?>

                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title fw-bold"><?php echo e(Str::limit($car->title, 30)); ?></h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                                <?php if($car->km_driven): ?>
                                <span><i class="bi bi-speedometer2 me-1"></i><?php echo e(number_format($car->km_driven)); ?> km</span>
                                <?php endif; ?>
                                <span><i class="bi bi-gear me-1"></i><?php echo e(ucfirst($car->transmission ?? 'N/A')); ?></span>
                                <span><i class="bi bi-fuelPump me-1"></i><?php echo e(ucfirst($car->fuel_type ?? 'N/A')); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">₹<?php echo e(number_format($car->price ?? 0)); ?></span>
                                <div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo e($car->city ?? 'N/A'); ?></small>
                                    <?php if($car->latitude && $car->longitude): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo e($car->latitude); ?>,<?php echo e($car->longitude); ?>" target="_blank" class="text-decoration-none small ms-1" title="View on Map">
                                        <i class="bi bi-map"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="<?php echo e(route('car.detail', $car->slug)); ?>" class="btn btn-outline-accent btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = $customerListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <?php
                                $images = json_decode($listing->images, true) ?? [];
                            ?>
                            <?php if(count($images) > 0): ?>
                                <img src="<?php echo e(asset('storage/' . $images[0])); ?>" class="card-img-top" alt="<?php echo e($listing->title); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <span class="position-absolute top-0 start-0 badge bg-info m-2">
                                <i class="bi bi-person me-1"></i>Owner Sale
                            </span>
                            <?php if($listing->isFeatured()): ?>
                                <span class="position-absolute top-0 start-0 badge badge-featured m-2" style="margin-left: 100px !important;">
                                    <i class="bi bi-star-fill me-1"></i>Featured
                                </span>
                            <?php endif; ?>
                            <span class="position-absolute top-0 end-0 badge bg-dark text-white m-2">
                                <?php echo e($listing->year ?? 'N/A'); ?>

                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title fw-bold"><?php echo e(Str::limit($listing->title, 30)); ?></h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                                <?php if($listing->km_driven): ?>
                                <span><i class="bi bi-speedometer2 me-1"></i><?php echo e(number_format($listing->km_driven)); ?> km</span>
                                <?php endif; ?>
                                <span><i class="bi bi-gear me-1"></i><?php echo e(ucfirst($listing->transmission ?? 'N/A')); ?></span>
                                <span><i class="bi bi-fuelPump me-1"></i><?php echo e(ucfirst($listing->fuel_type ?? 'N/A')); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">₹<?php echo e(number_format($listing->price ?? 0)); ?></span>
                                <div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo e($listing->city ?? 'N/A'); ?></small>
                                    <?php if($listing->latitude && $listing->longitude): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo e($listing->latitude); ?>,<?php echo e($listing->longitude); ?>" target="_blank" class="text-decoration-none small ms-1" title="View on Map">
                                        <i class="bi bi-map"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <a href="<?php echo e(route('car.detail', $listing->slug)); ?>" class="btn btn-outline-accent btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($cars->total() + $customerListings->count() == 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-car-front text-secondary" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-secondary">No Cars Found</h4>
                <p class="text-muted">Try adjusting your filters</p>
                <a href="<?php echo e(route('cars.index')); ?>" class="btn btn-accent">Clear All Filters</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/cars/index.blade.php ENDPATH**/ ?>