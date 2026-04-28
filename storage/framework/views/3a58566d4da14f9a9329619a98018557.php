<?php $__env->startSection('title', $seoTitle); ?>
<?php $__env->startSection('meta_description', $seoDescription); ?>
<?php $__env->startSection('og_type', 'website'); ?>
<?php $__env->startSection('og_url', url()->current()); ?>
<?php $__env->startSection('og_title', $seoTitle); ?>
<?php $__env->startSection('og_description', $seoDescription); ?>
<?php $__env->startSection('twitter_title', $seoTitle); ?>
<?php $__env->startSection('twitter_description', $seoDescription); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="mb-1">Used Cars in <?php echo e($cityName); ?></h1>
    <p class="text-muted mb-4"><?php echo e($cars->total()); ?> cars available</p>

    <div class="row">
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Filter Cars</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('cars.city', $city)); ?>" method="GET">
                        <?php if(request('brand')): ?><input type="hidden" name="brand" value="<?php echo e(request('brand')); ?>"><?php endif; ?>
                        <?php if(request('min_price')): ?><input type="hidden" name="min_price" value="<?php echo e(request('min_price')); ?>"><?php endif; ?>
                        <?php if(request('max_price')): ?><input type="hidden" name="max_price" value="<?php echo e(request('max_price')); ?>"><?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <select name="brand" class="form-select">
                                <option value="">All Brands</option>
                                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($b->id); ?>" <?php echo e(request('brand') == $b->id ? 'selected' : ''); ?>><?php echo e($b->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo e(request('min_price')); ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo e(request('max_price')); ?>">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <?php if($car->image_url): ?>
                                <img src="<?php echo e($car->image_url); ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo e($car->title); ?>">
                            <?php else: ?>
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="bi bi-car-front text-white" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><?php echo e(Str::limit($car->title, 35)); ?></h6>
                            <p class="card-text text-muted small mb-1">
                                <?php if($car->year): ?><?php echo e($car->year); ?> | <?php endif; ?>
                                <?php if($car->km_driven): ?><?php echo e(number_format($car->km_driven)); ?> km | <?php endif; ?>
                                <?php echo e(ucfirst($car->fuel_type ?? '')); ?>

                            </p>
                            <h5 class="text-primary fw-bold">₹<?php echo e(number_format($car->price ?? 0)); ?></h5>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="<?php echo e(route('car.detail', $car->slug)); ?>" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-car-front" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No cars found in <?php echo e($cityName); ?>.</p>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($cars->withQueryString()->links()); ?>

            </div>

            <!-- SEO Content Block -->
            <div class="mt-5 p-4 bg-light rounded shadow-sm border">
                <h2 class="h4 fw-bold text-dark mb-3">Buy Used Cars in <?php echo e($cityName); ?></h2>
                <p class="text-muted">
                    Looking for the best deals on <strong>used cars in <?php echo e($cityName); ?></strong>? At SAHIGADI, we offer a wide range of verified second hand cars tailored to your budget and lifestyle. Whether you need a compact hatchback for daily commutes, a premium sedan, or a sturdy SUV for family trips, our extensive inventory has you covered. 
                </p>
                <p class="text-muted mb-0">
                    Buying a <strong>second hand car in <?php echo e($cityName); ?></strong> has never been easier. We ensure every vehicle passes stringent quality checks. Enjoy transparent pricing, easy financing options, and top-notch customer support. Start browsing to find the perfect pre-owned car that matches your needs today!
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\frontend\cars\city.blade.php ENDPATH**/ ?>