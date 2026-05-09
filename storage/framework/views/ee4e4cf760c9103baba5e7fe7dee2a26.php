<?php $__env->startSection('title', 'Challan PDF Service'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <?php echo $__env->make('frontend.customer.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Search Challan PDF</h5>
                    <a href="<?php echo e(route('customer.challan-pdf.history')); ?>" class="btn btn-sm btn-light text-primary">View History</a>
                </div>
                <div class="card-body p-4">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Charge per search is ₹<?php echo e(\App\Models\Setting::getChallanPdfCharge()); ?>. 
                        Amount will be deducted from your wallet only on successful PDF generation.
                    </div>

                    <form action="<?php echo e(route('customer.challan-pdf.search')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label for="vehicle_number" class="form-label fw-bold">Challan Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="vehicle_number" name="vehicle_number" placeholder="Enter Challan Number (e.g. HR464162310XXXXXXXX)" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Search & Generate PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/frontend/customer/challan_pdf/index.blade.php ENDPATH**/ ?>