<?php $__env->startSection('title', 'Challan PDF Service Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <!-- Stats -->
        <div class="col-md-4">
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Searches</h5>
                    <h3><?php echo e($totalSearches); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h3>₹<?php echo e(number_format($totalRevenue, 2)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Failed Requests</h5>
                    <h3><?php echo e($failedRequests); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Service Settings</h6>
            <a href="<?php echo e(route('admin.challan-pdf.logs')); ?>" class="btn btn-sm btn-info">View Logs</a>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.challan-pdf.settings')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_challan_pdf_active" name="is_challan_pdf_active" value="1" <?php echo e($settings['is_challan_pdf_active'] ? 'checked' : ''); ?>>
                        <label class="custom-control-label" for="is_challan_pdf_active">Enable Challan PDF Service</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Customer Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="challan_pdf_charge" value="<?php echo e(old('challan_pdf_charge', $settings['challan_pdf_charge'])); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dealer Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="dealer_challan_pdf_charge" value="<?php echo e(old('dealer_challan_pdf_charge', $settings['dealer_challan_pdf_charge'])); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/admin/challan_pdf/index.blade.php ENDPATH**/ ?>