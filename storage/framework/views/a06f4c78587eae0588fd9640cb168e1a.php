<?php $__env->startSection('title', 'Sell Your Used Car Online In India - Best Market Price | SAHIGADI'); ?>
<?php $__env->startSection('meta_description', 'Looking to sell your car in Bihar, Patna, or anywhere in India? List your second-hand car on SAHIGADI for free valuation, instant payout, and hassle-free RC transfer.'); ?>
<?php $__env->startSection('meta_keywords', 'sell my car in Bihar, sell second hand car online, used car dealer in Bihar, second hand cars in Patna, used cars in Muzaffarpur, sell used car online'); ?>
<?php $__env->startSection('canonical', route('sell-car.index')); ?>

<?php $__env->startPush('json_ld'); ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Sell Used Car Online - Instant Valuation | SAHIGADI",
  "description": "Sell your car quickly for the best market price. Fast inspection, instant payout, and hassle-free RC transfer across Patna, Bihar, and PAN India.",
  "url": "<?php echo e(route('sell-car.index')); ?>"
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="fw-bold mb-3">Sell Your Car Online at the Best Price</h1>
            <p class="lead text-white-50">Get free valuation, doorstep inspection, and connect with verified buyers</p>
        </div>
    </div>
</section>

<!-- SEO Content Block for Local & National Keywords -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-10 mx-auto">
               <h2 class="h3 fw-bold text-dark">Ready to "Sell My Car" in Bihar or Across India?</h2>
               <p class="text-muted lead">
                   Whether you are looking for a reliable <strong>used car dealer in Bihar</strong> or want to sell <strong>second hand cars in Patna</strong> and <strong>used cars in Muzaffarpur</strong>, SAHIGADI is your top trusted platform. We provide a transparent bridge between individual sellers and verified buyers networks across our PAN-India marketplace.
               </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-4 bg-light rounded-4 h-100">
                    <i class="bi bi-cash-coin text-accent mb-3" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-2">Maximum Resale Value</h5>
                    <p class="text-muted small">Our marketplace ensures your listing reaches thousands of active buyers guaranteeing you the highest competitive market price.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 bg-light rounded-4 h-100">
                    <i class="bi bi-shield-check text-accent mb-3" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-2">Hassle-Free RC Transfer</h5>
                    <p class="text-muted small">Skip the complicated RTO paperwork! Verified dealers in our network handle document transfers securely and legally.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 bg-light rounded-4 h-100">
                    <i class="bi bi-lightning-charge text-accent mb-3" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-2">Fast & Instant Payouts</h5>
                    <p class="text-muted small">Once the car inspection completes and the final deal is approved, get your money deposited to your bank account instantly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5">
                        <h4 class="fw-bold mb-4"><i class="bi bi-car-front me-2 text-danger"></i>Car Details</h4>
                        <form action="<?php echo e(route('sell-car.store')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Listing Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., 2020 Maruti Swift VXi" value="<?php echo e(old('title')); ?>" required>
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Brand</label>
                                    <select name="brand_id" class="form-select <?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select Brand</option>
                                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($brand->id); ?>" <?php echo e(old('brand_id') == $brand->id ? 'selected' : ''); ?>>
                                                <?php echo e($brand->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['brand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Model</label>
                                    <input type="text" name="model" class="form-control <?php $__errorArgs = ['model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., Swift VXi" value="<?php echo e(old('model')); ?>">
                                    <?php $__errorArgs = ['model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Year</label>
                                    <input type="number" name="year" class="form-control <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., 2020" value="<?php echo e(old('year')); ?>" min="1900" max="<?php echo e(date('Y')); ?>">
                                    <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Fuel Type</label>
                                    <select name="fuel_type" class="form-select <?php $__errorArgs = ['fuel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = $fuelTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(old('fuel_type') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['fuel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Transmission</label>
                                    <select name="transmission" class="form-select <?php $__errorArgs = ['transmission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = $transmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(old('transmission') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['transmission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kilometers Driven</label>
                                    <input type="number" name="km_driven" class="form-control <?php $__errorArgs = ['km_driven'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., 45000" value="<?php echo e(old('km_driven')); ?>" min="0">
                                    <?php $__errorArgs = ['km_driven'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expected Price (₹)</label>
                                    <input type="number" name="price" class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., 500000" value="<?php echo e(old('price')); ?>" min="0">
                                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City</label>
                                    <input type="text" name="city" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., Patna" value="<?php echo e(old('city')); ?>">
                                    <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <input type="hidden" name="latitude" id="latitude" value="<?php echo e(old('latitude')); ?>">
                                <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude')); ?>">
                                <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="col-12 mt-1">
                                        <div class="text-danger small"><?php echo e($message); ?></div>
                                    </div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const submitBtn = document.getElementById('submitBtn');
                                    
                                    function handleLocationError(error) {
                                        submitBtn.disabled = true;
                                        let errorMsg = 'Location access is strictly required to list your vehicle.';
                                        switch(error.code) {
                                            case error.PERMISSION_DENIED:
                                                errorMsg = 'You denied the request for Geolocation. Location access is strictly required to sell your car. Please allow permissions and refresh the page.';
                                                break;
                                        }
                                        alert(errorMsg);
                                    }

                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function(position) {
                                            document.getElementById('latitude').value = position.coords.latitude;
                                            document.getElementById('longitude').value = position.coords.longitude;
                                            
                                            // Enable submission only if phone is verified
                                            if (!document.getElementById('otpSection').classList.contains('d-none') || document.getElementById('phoneHelp').classList.contains('d-none')) {
                                                submitBtn.disabled = true;
                                            } else {
                                                submitBtn.disabled = false;
                                            }
                                        }, handleLocationError, {
                                            enableHighAccuracy: true,
                                            timeout: 10000,
                                            maximumAge: 0
                                        });
                                    } else {
                                        alert('Geolocation is not supported by your browser.');
                                        submitBtn.disabled = true;
                                    }
                                });
                                </script>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control <?php $__errorArgs = ['registration_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="e.g., BR01AB1234" value="<?php echo e(old('registration_number')); ?>">
                                    <?php $__errorArgs = ['registration_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Number of Owners</label>
                                    <select name="owners" class="form-select <?php $__errorArgs = ['owners'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="1" <?php echo e(old('owners') == 1 ? 'selected' : ''); ?>>1st Owner</option>
                                        <option value="2" <?php echo e(old('owners') == 2 ? 'selected' : ''); ?>>2nd Owner</option>
                                        <option value="3" <?php echo e(old('owners') == 3 ? 'selected' : ''); ?>>3rd Owner</option>
                                        <option value="4" <?php echo e(old('owners') == 4 ? 'selected' : ''); ?>>4th Owner</option>
                                    </select>
                                    <?php $__errorArgs = ['owners'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Car Images <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal">(Minimum 5, Maximum 10 images)</small>
                                    </label>
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="row g-2" id="imagePreviewContainer">
                                        </div>
                                        <div class="mt-3">
                                            <label for="car_images" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus-circle me-1"></i>Select Images
                                            </label>
                                            <input type="file" name="images[]" id="car_images" class="d-none" multiple accept="image/*" onchange="previewImages(this)" required>
                                            <small class="text-muted d-block mt-2">Supported formats: JPG, PNG, JPEG, GIF (Max 2MB each)</small>
                                            <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted" id="imageCount">0 / 10 images selected</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h4 class="fw-bold mb-4"><i class="bi bi-person me-2 text-danger"></i>Owner Details</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Your Name</label>
                                    <input type="text" name="owner_name" class="form-control <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="Enter your name" value="<?php echo e(old('owner_name')); ?>">
                                    <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="owner_phone" name="owner_phone" class="form-control <?php $__errorArgs = ['owner_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               placeholder="10-digit phone number" value="<?php echo e(old('owner_phone')); ?>" required pattern="[0-9]{10}">
                                        <button class="btn btn-outline-primary" type="button" id="btnSendOtp">Send OTP</button>
                                    </div>
                                    <div id="phoneHelp" class="form-text text-success d-none"><i class="bi bi-check-circle-fill"></i> Phone Number Verified</div>
                                    <?php $__errorArgs = ['owner_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <div class="col-md-6 d-none" id="otpSection">
                                    <label class="form-label fw-semibold">Enter OTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="otp_input" class="form-control" placeholder="6-digit OTP" maxlength="6">
                                        <button class="btn btn-primary" type="button" id="btnVerifyOtp">Verify</button>
                                    </div>
                                    <div class="form-text text-muted mt-1" id="otpTimerText">Resend OTP in <span id="timerCount">30</span>s</div>
                                    <button class="btn btn-link btn-sm p-0 text-decoration-none d-none mt-1" type="button" id="btnResendOtp">Resend OTP</button>
                                    <div id="otpMessage" class="small mt-1 text-danger"></div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">WhatsApp Number</label>
                                    <input type="text" name="whatsapp_number" class="form-control <?php $__errorArgs = ['whatsapp_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="Enter your WhatsApp number" value="<?php echo e(old('whatsapp_number')); ?>">
                                    <?php $__errorArgs = ['whatsapp_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-accent btn-lg w-100" disabled>
                                    <i class="bi bi-send me-2"></i>Submit Listing
                                </button>
                            </div>

                            <p class="text-muted small mt-3 text-center">
                                <i class="bi bi-info-circle me-1"></i>
                                Your listing will be reviewed by our team before it goes live. We will contact you for verification.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function previewImages(input) {
    const container = document.getElementById('imagePreviewContainer');
    const countLabel = document.getElementById('imageCount');
    container.innerHTML = '';
    
    if (input.files) {
        const totalFiles = input.files.length;
        countLabel.textContent = totalFiles + ' / 10 images selected';
        
        if (totalFiles > 10) {
            alert('Maximum 10 images allowed');
            input.value = '';
            countLabel.textContent = '0 / 10 images selected';
            return;
        }
        
        if (totalFiles < 5) {
            countLabel.className = 'text-danger';
        } else {
            countLabel.className = 'text-success';
        }
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 col-lg-2 mb-2';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(this, ${index})">
                                <i class="bi bi-x"></i>
                            </button>
                            <small class="d-block text-center mt-1 text-muted">${file.name.substring(0, 10)}...</small>
                        </div>
                    `;
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function removeImage(btn, index) {
    const input = document.getElementById('car_images');
    const dt = new DataTransfer();
    const files = input.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    input.files = dt.files;
    btn.closest('.col-4').remove();
    
    const countLabel = document.getElementById('imageCount');
    const totalFiles = input.files.length;
    countLabel.textContent = totalFiles + ' / 10 images selected';
    
    if (totalFiles < 5) {
        countLabel.className = 'text-danger';
    } else {
        countLabel.className = 'text-success';
    }
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('owner_phone');
        const btnSendOtp = document.getElementById('btnSendOtp');
        const otpSection = document.getElementById('otpSection');
        const otpInput = document.getElementById('otp_input');
        const btnVerifyOtp = document.getElementById('btnVerifyOtp');
        const btnResendOtp = document.getElementById('btnResendOtp');
        const otpTimerText = document.getElementById('otpTimerText');
        const timerCount = document.getElementById('timerCount');
        const otpMessage = document.getElementById('otpMessage');
        const submitBtn = document.getElementById('submitBtn');
        const phoneHelp = document.getElementById('phoneHelp');
        
        // Initial state
        submitBtn.disabled = true; // Disable until OTP verify
        
        let timerInterval;

        function startTimer() {
            let count = 30;
            btnResendOtp.classList.add('d-none');
            otpTimerText.classList.remove('d-none');
            timerCount.textContent = count;
            
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                count--;
                timerCount.textContent = count;
                if(count <= 0) {
                    clearInterval(timerInterval);
                    otpTimerText.classList.add('d-none');
                    btnResendOtp.classList.remove('d-none');
                }
            }, 1000);
        }

        function sendOtpAJAX() {
            const phone = phoneInput.value.trim();
            if(!/^[0-9]{10}$/.test(phone)) {
                alert("Please enter a valid 10-digit phone number.");
                return;
            }

            btnSendOtp.disabled = true;
            btnSendOtp.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            otpMessage.textContent = '';
            otpMessage.className = 'small mt-1 text-info';
            
            fetch("<?php echo e(route('sell-car.send-otp')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(res => res.json())
            .then(data => {
                btnSendOtp.innerHTML = 'Send OTP';
                if(data.success) {
                    otpSection.classList.remove('d-none');
                    phoneInput.readOnly = true;
                    btnSendOtp.classList.add('d-none');
                    startTimer();
                    otpMessage.textContent = data.message;
                    otpMessage.className = 'small mt-1 text-success';
                } else {
                    btnSendOtp.disabled = false;
                    alert(data.message || 'Failed to send OTP.');
                }
            })
            .catch(err => {
                btnSendOtp.innerHTML = 'Send OTP';
                btnSendOtp.disabled = false;
                alert('An error occurred. Please try again.');
            });
        }

        btnSendOtp.addEventListener('click', sendOtpAJAX);
        btnResendOtp.addEventListener('click', sendOtpAJAX);

        btnVerifyOtp.addEventListener('click', function() {
            const phone = phoneInput.value.trim();
            const otp = otpInput.value.trim();

            if(!/^[0-9]{6}$/.test(otp)) {
                otpMessage.textContent = 'Please enter a valid 6-digit OTP.';
                otpMessage.className = 'small mt-1 text-danger';
                return;
            }

            btnVerifyOtp.disabled = true;
            btnVerifyOtp.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch("<?php echo e(route('sell-car.verify-otp')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ phone: phone, otp: otp })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    otpSection.classList.add('d-none');
                    phoneHelp.classList.remove('d-none');
                    submitBtn.disabled = document.getElementById('latitude').value == ''; // Recheck location
                    clearInterval(timerInterval);
                } else {
                    btnVerifyOtp.disabled = false;
                    btnVerifyOtp.innerHTML = 'Verify';
                    otpMessage.textContent = data.message || 'Invalid OTP';
                    otpMessage.className = 'small mt-1 text-danger';
                }
            })
            .catch(err => {
                btnVerifyOtp.disabled = false;
                btnVerifyOtp.innerHTML = 'Verify';
                otpMessage.textContent = 'An error occurred during verification.';
                otpMessage.className = 'small mt-1 text-danger';
            });
        });
        
        // Also enable submitBtn on map location load if phone is already verified (in case old validation failed but phone was verified)
        // Though Laravel session will handle this mostly, in pure client-side we keep submitBtn disabled until OTP verify.
        
        // Let's modify the map load behavior as well, if location takes long.
        // The original location script sets submitBtn.disabled = false, we need to ensure it only sets it if phone is also verified.
        // Since phone isn't verified on page load, we should just disable it.
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/sell-car/index.blade.php ENDPATH**/ ?>