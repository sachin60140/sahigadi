<?php $__env->startSection('title', 'Contact Us - SAHI GADI'); ?>
<?php $__env->startSection('meta_description', 'Get in touch with SAHI GADI for any queries regarding pre-owned cars in Patna, Bihar.'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .contact-hero {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        padding: 80px 0 60px;
        position: relative;
        overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute;
        top: -30%;
        left: -10%;
        width: 500px;
        height: 500px;
        background: var(--accent);
        opacity: 0.05;
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    .fade-up {
        animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    
    .contact-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        border-radius: 20px;
        padding: 40px;
        transition: transform 0.3s;
    }
    .contact-card:hover {
        transform: translateY(-5px);
    }
    
    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .icon-box {
        width: 50px;
        height: 50px;
        min-width: 50px;
        background: rgba(233, 69, 96, 0.1);
        color: var(--accent);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 20px;
        transition: all 0.3s;
    }
    .info-item:hover .icon-box {
        background: var(--accent);
        color: white;
        transform: scale(1.1);
    }
    
    .form-group label {
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 8px;
    }
    .form-control {
        background: #f8f9fa;
        border: 1px solid transparent;
        padding: 15px 20px;
        font-size: 1rem;
        border-radius: 12px;
        transition: all 0.3s;
    }
    .form-control:focus {
        background: white;
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(233, 69, 96, 0.1);
    }
    .btn-submit {
        background: var(--accent);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(233, 69, 96, 0.4);
    }
    .btn-submit::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    .btn-submit:hover::after {
        left: 100%;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="contact-hero text-white text-center">
    <div class="container fade-up">
        <h1 class="display-4 fw-bold mb-3">Get in <span>Touch</span></h1>
        <p class="lead opacity-75">We're here to help and answer any question you might have.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5" style="margin-top: -50px; position: relative; z-index: 10;">
    <div class="container fade-up" style="animation-delay: 0.2s;">
        <div class="row g-5">
            
            <!-- Contact Information -->
            <div class="col-lg-5">
                <div class="contact-card h-100">
                    <h3 class="fw-bold mb-4">Contact Information</h3>
                    <p class="text-muted mb-5">Have questions about buying or selling a car? Our team is ready to assist you. Reach out to us through any of the following methods.</p>
                    
                    <div class="info-item">
                        <div class="icon-box">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Our Location</h5>
                            <p class="text-muted mb-0">Awani Enterprises<br>A-5, Sector 65, Noida, UP</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="icon-box">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Phone Number</h5>
                            <p class="text-muted mb-0">+91 98188 23408</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="icon-box">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Email Address</h5>
                            <p class="text-muted mb-0">support@sahigadi.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-card">
                    <h3 class="fw-bold mb-4">Send us a Message</h3>
                    <form action="<?php echo e(url()->current()); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row g-4">
                            <div class="col-md-6 form-group">
                                <label for="name">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="email">Your Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12 form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="How can we help you?" required>
                            </div>
                            <div class="col-12 form-group">
                                <label for="message">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn-submit">
                                    Send Message <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/pages/contact.blade.php ENDPATH**/ ?>