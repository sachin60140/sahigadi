<?php $__env->startPush('styles'); ?>
<style>
.text-accent { color: #e94560 !important; }
.spec-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}
.spec-item i {
    font-size: 1.5rem;
    color: #e94560;
}
.img-thumbnail {
    transition: all 0.3s;
}
.img-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
</style>
<?php $__env->stopPush(); ?>

<?php
$item = $car ?? $customerListing;
$totalImages = count($allImages);
?>

<?php $__env->startSection('title', $seo['seo_title']); ?>
<?php $__env->startSection('meta_description', $seo['meta_description']); ?>
<?php $__env->startSection('meta_keywords', $seo['meta_keywords']); ?>
<?php $__env->startSection('canonical', route('car.detail', $item->slug)); ?>
<?php $__env->startSection('og_type', 'product'); ?>
<?php $__env->startSection('og_url', route('car.detail', $item->slug)); ?>
<?php $__env->startSection('og_title', $seo['og_title']); ?>
<?php $__env->startSection('og_description', $seo['og_description']); ?>
<?php $__env->startSection('og_image', $firstImage); ?>
<?php $__env->startSection('twitter_title', $seo['og_title']); ?>
<?php $__env->startSection('twitter_description', $seo['og_description']); ?>

<?php if (! $__env->hasRenderedOnce('32fc6465-6d52-4dfe-a67a-3762f8ba4acc')): $__env->markAsRenderedOnce('32fc6465-6d52-4dfe-a67a-3762f8ba4acc'); ?>
<?php $__env->startSection('json_ld'); ?>
<script type="application/ld+json">
<?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>
<script type="application/ld+json">
<?php echo json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>
<?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<section class="py-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('cars.index')); ?>" class="text-white text-decoration-none">Cars</a></li>
                <li class="breadcrumb-item text-white-50 active" aria-current="page"><?php echo e(Str::limit($item->title, 30)); ?></li>
            </ol>
        </nav>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <?php if($totalImages > 0): ?>
                <div id="carCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                    <div class="carousel-inner">
                        <?php $__currentLoopData = $allImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="carousel-item <?php echo e($index === 0 ? 'active' : ''); ?>">
                            <img src="<?php echo e($image); ?>" 
                                 class="d-block w-100" 
                                 style="max-height: 500px; object-fit: cover;" 
                                 alt="Car Image <?php echo e($index + 1); ?>">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($totalImages > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php if($totalImages > 1): ?>
                <div class="card-body pb-0">
                    <div class="d-flex flex-wrap gap-2">
                        <?php $__currentLoopData = $allImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <img src="<?php echo e($image); ?>" 
                             class="img-thumbnail <?php echo e($index === 0 ? 'border-primary' : ''); ?>" 
                             style="width: 100px; height: 70px; object-fit: cover; cursor: pointer;" 
                             alt="Thumbnail <?php echo e($index + 1); ?>"
                             onclick="setActiveSlide(<?php echo e($index); ?>)">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="bi bi-car-front text-secondary" style="font-size: 8rem;"></i>
                </div>
                <?php endif; ?>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"><?php echo e($item->title); ?></h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt me-1"></i><?php echo e($item->city ?? 'N/A'); ?>

                            <?php if($item->brand): ?>
                                | <i class="bi bi-tag me-1"></i><?php echo e($item->brand->name); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if($isCustomerListing): ?>
                        <span class="badge bg-info"><i class="bi bi-person me-1"></i>Owner Sale</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Car Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-currency-rupee"></i>
                                <div>
                                    <small class="text-muted">Price</small>
                                    <h5 class="mb-0 text-accent fw-bold">₹<?php echo e(number_format($item->price ?? 0)); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-calendar3"></i>
                                <div>
                                    <small class="text-muted">Year</small>
                                    <h6 class="mb-0"><?php echo e($item->year ?? 'N/A'); ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-speedometer2"></i>
                                <div>
                                    <small class="text-muted">KM Driven</small>
                                    <h6 class="mb-0"><?php echo e($item->km_driven ? number_format($item->km_driven) . ' km' : 'N/A'); ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-fuelPump"></i>
                                <div>
                                    <small class="text-muted">Fuel Type</small>
                                    <h6 class="mb-0"><?php echo e(ucfirst($item->fuel_type ?? 'N/A')); ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-gear"></i>
                                <div>
                                    <small class="text-muted">Transmission</small>
                                    <h6 class="mb-0"><?php echo e(ucfirst($item->transmission ?? 'N/A')); ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-people"></i>
                                <div>
                                    <small class="text-muted">Owners</small>
                                    <h6 class="mb-0"><?php echo e($item->owners ?? 1); ?> Owner(s)</h6>
                                </div>
                            </div>
                        </div>
                        <?php if($item->registration_number): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-card-text"></i>
                                <div>
                                    <small class="text-muted">Reg. Number</small>
                                    <h6 class="mb-0"><?php echo e(substr($item->registration_number, 0, 4)); ?>****</h6>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($item->model): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="spec-item">
                                <i class="bi bi-car-front"></i>
                                <div>
                                    <small class="text-muted">Model</small>
                                    <h6 class="mb-0"><?php echo e($item->model); ?></h6>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?php if($isCustomerListing): ?>
            <div class="card mb-4 sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header text-white" style="background: #e94560;">
                    <h4 class="mb-0"><i class="bi bi-currency-rupee me-2"></i><?php echo e(number_format($item->price ?? 0)); ?></h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-person me-2"></i>Owner Information</h5>
                    <div class="dealer-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="bi bi-person-fill" style="font-size: 2rem; color: #e94560;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo e($item->owner_name ?? 'Owner'); ?></h6>
                            </div>
                        </div>
                        <?php if($item->owner_phone): ?>
                        <p class="mb-2"><i class="bi bi-telephone me-2" style="color: #e94560;"></i><?php echo e($item->owner_phone); ?></p>
                        <?php endif; ?>
                        <?php if($item->whatsapp_number): ?>
                        <p class="mb-2"><i class="bi bi-whatsapp me-2 text-success"></i><?php echo e($item->whatsapp_number); ?></p>
                        <?php endif; ?>
                        <?php if($item->city): ?>
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" style="color: #e94560;"></i><?php echo e($item->city); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if($item->owner_phone): ?>
                    <a href="tel:<?php echo e($item->owner_phone); ?>" class="btn w-100 py-3 mb-3" style="background: #e94560; color: white;">
                        <i class="bi bi-telephone me-2"></i>Call Owner
                    </a>
                    <?php endif; ?>
                    <?php if($item->whatsapp_number): ?>
                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $item->whatsapp_number)); ?>" target="_blank" class="btn btn-success w-100 py-3">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="card mb-4 sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header text-white" style="background: #e94560;">
                    <h4 class="mb-0"><i class="bi bi-currency-rupee me-2"></i><?php echo e(number_format($car->price ?? 0)); ?></h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-person-badge me-2"></i>Dealer Information</h5>
                    <div class="dealer-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="bi bi-person-fill" style="font-size: 2rem; color: #e94560;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo e($car->dealer->name); ?></h6>
                                <?php if($car->dealer->company_name): ?>
                                <small class="text-muted"><?php echo e($car->dealer->company_name); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($car->dealer->phone): ?>
                        <p class="mb-2"><i class="bi bi-telephone me-2" style="color: #e94560;"></i><?php echo e($car->dealer->phone); ?></p>
                        <?php endif; ?>
                        <?php if($car->dealer->city): ?>
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" style="color: #e94560;"></i><?php echo e($car->dealer->city); ?></p>
                        <?php endif; ?>
                    </div>
                    <button class="btn w-100 py-3 mb-3" style="background: #e94560; color: white;" data-bs-toggle="modal" data-bs-target="#enquiryModal">
                        <i class="bi bi-chat-dots me-2"></i>Send Enquiry
                    </button>
                    <a href="tel:<?php echo e($car->dealer->phone); ?>" class="btn btn-outline-secondary w-100 py-3">
                        <i class="bi bi-telephone me-2"></i>Call Dealer
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check" style="font-size: 3rem; color: #e94560;"></i>
                    <h6 class="mt-2 mb-2">SAHIGADI Verified</h6>
                    <small class="text-muted">This listing is verified by SAHIGADI</small>
                </div>
            </div>
        </div>
    </div>

    <?php if($relatedCars->count() > 0): ?>
    <section class="mt-5">
        <h4 class="mb-4"><i class="bi bi-grid me-2"></i>Related Cars</h4>
        <div class="row">
            <?php $__currentLoopData = $relatedCars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedCar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <?php if($relatedCar->image_url): ?>
                            <img src="<?php echo e($relatedCar->image_url); ?>" class="card-img-top" alt="<?php echo e($relatedCar->title); ?>">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="bi bi-car-front text-secondary" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title fw-bold"><?php echo e(Str::limit($relatedCar->title, 25)); ?></h6>
                        <span style="font-size: 1.1rem; font-weight: bold; color: #e94560;">₹<?php echo e(number_format($relatedCar->price ?? 0)); ?></span>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="<?php echo e(route('car.detail', $relatedCar->slug)); ?>" class="btn btn-sm w-100" style="background: #e94560; color: white;">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/cars/show.blade.php ENDPATH**/ ?>