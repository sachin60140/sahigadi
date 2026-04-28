<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RC Details - <?php echo e($vehicleSearch->registration_number); ?> - SAHIGADI</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding: 20px; background: #1a1a2e; color: white; }
        .header h1 { margin: 0; }
        .reg-number { font-size: 24px; font-weight: bold; }
        .section { margin: 20px 0; }
        .section-title { background: #e94560; color: white; padding: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { border: 1px solid #ddd; padding: 8px; }
        .label { font-weight: bold; background: #f8f9fa; width: 40%; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        .badge { padding: 3px 10px; border-radius: 3px; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHIGADI - RC Details</h1>
        <div class="reg-number"><?php echo e($vehicleSearch->registration_number); ?></div>
        <?php if($vehicleSearch->is_success): ?>
            <span class="badge badge-success">Verified</span>
        <?php else: ?>
            <span class="badge badge-danger">Failed</span>
        <?php endif; ?>
    </div>

    <?php if($vehicleSearch->is_success && $dealerSearch): ?>
    <div class="section">
        <div class="section-title">Owner Details</div>
        <table>
            <tr><td class="label">Owner Name</td><td><?php echo e($dealerSearch->owner_name ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Father's Name</td><td><?php echo e($dealerSearch->father_name ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Address</td><td><?php echo e($dealerSearch->address ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Mobile Number</td><td><?php echo e($dealerSearch->mobile_number ?? 'N/A'); ?></td></tr>
            <tr><td class="label">RTO Location</td><td><?php echo e($dealerSearch->rto_location ?? 'N/A'); ?></td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Vehicle Details</div>
        <table>
            <tr><td class="label">Category</td><td><?php echo e($dealerSearch->vehicle_category ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Make</td><td><?php echo e($dealerSearch->make ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Model</td><td><?php echo e($dealerSearch->model ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Color</td><td><?php echo e($dealerSearch->color ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Fuel Type</td><td><?php echo e($dealerSearch->fuel_type ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Seating Capacity</td><td><?php echo e($dealerSearch->seats ?? 'N/A'); ?></td></tr>
            <tr><td class="label">RC Status</td><td><?php echo e($dealerSearch->rc_status ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Engine Number</td><td><?php echo e($dealerSearch->engine_number ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Chassis Number</td><td><?php echo e($dealerSearch->chassis_number ?? 'N/A'); ?></td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Documents & Status</div>
        <table>
            <tr><td class="label">Insurance Provider</td><td><?php echo e($dealerSearch->insurance_provider ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Insurance Valid Till</td><td><?php echo e($dealerSearch->insurance_date ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Fitness Valid Till</td><td><?php echo e($dealerSearch->fitness_date ?? 'N/A'); ?></td></tr>
            <tr><td class="label">PUC Valid Till</td><td><?php echo e($dealerSearch->puc_validity ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Blacklist Status</td><td><?php echo e($dealerSearch->blacklist_status ?? 'Clean'); ?></td></tr>
            <tr><td class="label">Financed</td><td><?php echo e($dealerSearch->financed ? 'Yes - ' . $dealerSearch->lender_name : 'No'); ?></td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Search Summary</div>
        <table>
            <tr><td class="label">Dealer</td><td><?php echo e($vehicleSearch->dealer->name ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Search Date</td><td><?php echo e($vehicleSearch->created_at->format('d M Y, h:i A')); ?></td></tr>
            <tr><td class="label">Amount Charged</td><td>Rs. <?php echo e(number_format($vehicleSearch->charge_amount, 2)); ?></td></tr>
        </table>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="section-title">Error</div>
        <p><?php echo e($vehicleSearch->error_message ?? 'Search failed'); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>SAHIGADI - Vehicle Marketplace | Generated on: <?php echo e(date('d M Y, h:i A')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\vehicle-searches\single-pdf.blade.php ENDPATH**/ ?>