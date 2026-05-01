<?php $__env->startPush('json_ld'); ?>
<script type="application/ld+json">
<?php echo json_encode($homepageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>
<?php $__env->stopPush(); ?>

<?php
$seoTitle = 'SAHI GADI - Trusted Used Car Marketplace in Patna, Bihar';
$seoDescription = 'Find the best verified pre-owned cars in Patna, Bihar. SAHI GADI - Your trusted car marketplace with quality assurance, transparent pricing, and hassle-free transactions.';
$seoKeywords = 'used cars Patna, pre-owned cars Bihar, car marketplace, buy sell cars Patna, verified cars, affordable cars';
?>

<?php $__env->startSection('title', $seoTitle); ?>
<?php $__env->startSection('meta_description', $seoDescription); ?>
<?php $__env->startSection('meta_keywords', $seoKeywords); ?>

<?php $__env->startSection('content'); ?>
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="text-white mb-4">Find Your Perfect <span>Pre-Owned Car</span></h1>
                <p class="text-white-50 mb-4 fs-5">Your trusted marketplace for verified used cars in Bihar. Quality assurance, transparent pricing, and hassle-free transactions.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo e(route('cars.index')); ?>" class="btn btn-accent btn-lg">
                        <i class="bi bi-search me-2"></i>Browse Cars
                    </a>
                    <a href="<?php echo e(route('sell-car.index')); ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-shop me-2"></i>Sell Your Car
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="search-box">
                    <h5 class="mb-4 fw-bold"><i class="bi bi-search me-2"></i>Find Your Car</h5>
                    <form action="<?php echo e(route('cars.index')); ?>" method="GET">
                        <div class="row g-3">
                              <div class="col-md-6">
                                  <input type="text" name="keyword" class="form-control" placeholder="Search keyword..." value="<?php echo e(request('keyword')); ?>">
                              </div>
                              <div class="col-md-6">
                                  <select name="city" class="form-select">
                                      <option value="">Select City</option>
                                      <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                          <option value="<?php echo e($city); ?>" <?php echo e(request('city') == $city ? 'selected' : ''); ?>><?php echo e($city); ?></option>
                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </select>
                              </div>
                              <div class="col-md-4">
                                  <select name="brand" class="form-select">
                                      <option value="">All Brands</option>
                                      <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                          <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand') == $brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </select>
                              </div>
                              <div class="col-md-4">
                                  <select name="fuel_type" class="form-select">
                                      <option value="">Fuel Type</option>
                                      <option value="petrol" <?php echo e(request('fuel_type') == 'petrol' ? 'selected' : ''); ?>>Petrol</option>
                                      <option value="diesel" <?php echo e(request('fuel_type') == 'diesel' ? 'selected' : ''); ?>>Diesel</option>
                                      <option value="electric" <?php echo e(request('fuel_type') == 'electric' ? 'selected' : ''); ?>>Electric</option>
                                      <option value="cng" <?php echo e(request('fuel_type') == 'cng' ? 'selected' : ''); ?>>CNG</option>
                                  </select>
                              </div>
                              <div class="col-md-4">
                                  <select name="transmission" class="form-select">
                                      <option value="">Transmission</option>
                                      <option value="manual" <?php echo e(request('transmission') == 'manual' ? 'selected' : ''); ?>>Manual</option>
                                      <option value="automatic" <?php echo e(request('transmission') == 'automatic' ? 'selected' : ''); ?>>Automatic</option>
                                  </select>
                              </div>
                              <div class="col-md-6">
                                  <input type="number" name="min_price" class="form-control" placeholder="Min Price (₹)" value="<?php echo e(request('min_price')); ?>">
                              </div>
                              <div class="col-md-6">
                                  <input type="number" name="max_price" class="form-control" placeholder="Max Price (₹)" value="<?php echo e(request('max_price')); ?>">
                              </div>          <div class="col-12">
                                <button type="submit" class="btn btn-accent w-100 py-3">
                                    <i class="bi bi-search me-2"></i>Search Cars
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if($allFeatured->count() > 0): ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">Featured Cars</h2>
                <p class="text-muted">Handpicked premium listings just for you</p>
            </div>
            <a href="<?php echo e(route('cars.index')); ?>" class="btn btn-outline-accent">View All <i class="bi bi-arrow-right ms-2"></i></a>
        </div>
        <div class="row">
            <?php $__currentLoopData = $allFeatured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $imageUrl = null;
                if ($item instanceof \App\Models\CustomerCarListing) {
                    $images = json_decode($item->images ?? '[]', true);
                    $imageUrl = count($images) > 0 ? asset('storage/'.$images[0]) : null;
                } elseif ($item->relationLoaded('images')) {
                    $imageUrl = $item->image_url;
                }
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <?php if($imageUrl): ?>
                            <img src="<?php echo e($imageUrl); ?>" class="card-img-top" alt="<?php echo e($item->title); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                        <span class="position-absolute top-0 start-0 badge badge-featured m-2">
                            <i class="bi bi-star-fill me-1"></i> Featured
                        </span>
                        <span class="position-absolute top-0 end-0 badge bg-dark text-white m-2">
                            <?php echo e($item->year ?? 'N/A'); ?>

                        </span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold"><?php echo e(Str::limit($item->title, 28)); ?></h6>
                        <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                            <?php if($item->km_driven): ?>
                            <span><i class="bi bi-speedometer2 me-1"></i><?php echo e(number_format($item->km_driven)); ?> km</span>
                            <?php endif; ?>
                            <span><i class="bi bi-gear me-1"></i><?php echo e(ucfirst($item->transmission ?? 'N/A')); ?></span>
                            <span><i class="bi bi-fuelPump me-1"></i><?php echo e(ucfirst($item->fuel_type ?? 'N/A')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-tag">₹<?php echo e(number_format($item->price ?? 0)); ?></span>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo e($item->city ?? 'N/A'); ?></small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="<?php echo e(route('car.detail', $item->slug)); ?>" class="btn btn-outline-accent btn-sm w-100">
                            <i class="bi bi-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title mx-auto">Latest Cars</h2>
            <p class="text-muted">Fresh listings added every day</p>
        </div>
        <div class="row">
            <?php $__currentLoopData = $allCars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <?php
                            $isCustomerListing = $car instanceof \App\Models\CustomerCarListing;
                            $imageUrl = null;
                            if ($isCustomerListing) {
                                $listingImages = json_decode($car->images ?? '[]', true);
                                $imageUrl = count($listingImages) > 0 ? asset('storage/'.$listingImages[0]) : null;
                            } elseif ($car->relationLoaded('images')) {
                                $imageUrl = $car->image_url;
                            }
                        ?>
                        <?php if($imageUrl): ?>
                            <img src="<?php echo e($imageUrl); ?>" class="card-img-top" alt="<?php echo e($car->title); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-car-front text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                        <?php if($isCustomerListing): ?>
                        <span class="position-absolute top-0 start-0 badge bg-info m-2">
                            <i class="bi bi-person me-1"></i>Owner
                        </span>
                        <?php endif; ?>
                        <span class="position-absolute top-0 end-0 badge bg-dark text-white m-2">
                            <?php echo e($car->year ?? 'N/A'); ?>

                        </span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold"><?php echo e(Str::limit($car->title, 28)); ?></h6>
                        <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                            <?php if($car->km_driven): ?>
                            <span><i class="bi bi-speedometer2 me-1"></i><?php echo e(number_format($car->km_driven)); ?> km</span>
                            <?php endif; ?>
                            <span><i class="bi bi-gear me-1"></i><?php echo e(ucfirst($car->transmission ?? 'N/A')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-tag">₹<?php echo e(number_format($car->price ?? 0)); ?></span>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo e($car->city ?? 'N/A'); ?></small>
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

            <?php if($allCars->count() == 0): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-car-front text-secondary" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-secondary">No Cars Available</h4>
                <p class="text-muted">Be the first to list your car!</p>
                <a href="<?php echo e(route('dealer.register')); ?>" class="btn btn-accent">Register as Dealer</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title mx-auto">Browse by Brand</h2>
            <p class="text-muted">Find cars from your favorite brands</p>
        </div>
        <div class="row g-3">
            <?php $__currentLoopData = $brands->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="<?php echo e(route('cars.brand', $brand->slug)); ?>" class="text-decoration-none">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center py-4">
                            <div class="icon-box mx-auto mb-3">
                                <i class="bi bi-car-front-fill" style="font-size: 2.5rem; color: var(--accent);"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark"><?php echo e($brand->name); ?></h6>
                            <small class="text-muted"><?php echo e($brand->cars_count ?? ''); ?> Cars</small>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title mx-auto">Our Services</h2>
            <p class="text-muted">Premium services for your vehicle needs</p>
        </div>
        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-4">
                                <i class="bi bi-car-front-fill" style="font-size: 2.5rem; color: var(--accent);"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Buy Used Car</h5>
                                <p class="text-muted mb-0">Find your dream car from thousands of verified listings</p>
                                <a href="<?php echo e(route('cars.index')); ?>" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-arrow-right me-1"></i>Browse Cars
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-4">
                                <i class="bi bi-sell" style="font-size: 2.5rem; color: var(--accent);"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Sell Your Car</h5>
                                <p class="text-muted mb-0">Sell your car quickly at best price</p>
                                <a href="<?php echo e(route('sell-car.index')); ?>" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-arrow-right me-1"></i>Sell Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-4">
                                <i class="bi bi-shield-check" style="font-size: 2.5rem; color: var(--accent);"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Verified Dealers</h5>
                                <p class="text-muted mb-0">Verified dealers with authentic listings</p>
                                <a href="<?php echo e(route('verified-dealers')); ?>" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-arrow-right me-1"></i>View Dealers
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if(isset($plans) && $plans->count() > 0): ?>
<section class="py-5 bg-white">
    <div class="container border-top pt-5">
        <div class="text-center mb-5">
            <h2 class="section-title mx-auto">Dealer Subscription Plans</h2>
            <p class="text-muted">Grow your dealership with our premium listing plans</p>
        </div>
        <div class="row justify-content-center g-4">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 position-relative hover-shadow transition">
                    <div class="card-header bg-transparent border-0 text-center pt-5 pb-3">
                        <h4 class="fw-bold text-dark mb-3"><?php echo e($plan->name); ?></h4>
                        <div class="display-5 fw-bold" style="color: var(--accent);">
                            ₹<?php echo e(number_format($plan->price)); ?><span class="fs-6 text-muted fw-normal">/<?php echo e($plan->duration_days); ?> days</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-5">
                        <p class="text-muted text-center mb-4"><?php echo e($plan->description ?? 'Get access to premium features and list your cars instantly.'); ?></p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span><strong><?php echo e($plan->listing_limit); ?></strong> Car Listings</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Dedicated Dealer Page</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Verified Dealer Badge</span>
                            </li>
                        </ul>
                        <div class="text-center mt-auto">
                            <a href="<?php echo e(route('dealer.register')); ?>" class="btn btn-outline-accent btn-lg w-100 rounded-pill fw-semibold">Choose Plan</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="text-center mt-4">
            <p class="text-muted small">Need a custom plan? <a href="<?php echo e(route('contact')); ?>" class="text-decoration-none fw-bold" style="color: var(--accent);">Contact Us</a></p>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="text-white">
                    <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
                    <h3 class="mt-3 fw-bold">100% Verified</h3>
                    <p class="text-white-50 mb-0">All cars are verified by our team</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-white">
                    <i class="bi bi-currency-rupee" style="font-size: 3rem;"></i>
                    <h3 class="mt-3 fw-bold">Best Prices</h3>
                    <p class="text-white-50 mb-0">Competitive and transparent pricing</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-white">
                    <i class="bi bi-headset" style="font-size: 3rem;"></i>
                    <h3 class="mt-3 fw-bold">24/7 Support</h3>
                    <p class="text-white-50 mb-0">We're here to help you anytime</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/home.blade.php ENDPATH**/ ?>