<!DOCTYPE html>
<html>
<head>
    <title>Listing Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Update on Your Car Listing</h2>
    <p>Dear <?php echo e($listing->owner_name ?? 'Customer'); ?>,</p>
    
    <p>Your car listing for <strong><?php echo e($listing->title); ?></strong> has been <strong><?php echo e($status); ?></strong>.</p>
    
    <?php if($status === 'approved'): ?>
        <p>Your car is now visible to buyers on our platform!</p>
        <p><a href="<?php echo e(route('car.detail', $listing->slug)); ?>" style="display: inline-block; padding: 10px 20px; background-color: #e94560; color: white; text-decoration: none; border-radius: 5px;">View Your Listing</a></p>
    <?php elseif($status === 'rejected'): ?>
        <p>Unfortunately, we could not approve your listing.</p>
        <?php if($reason): ?>
            <p><strong>Reason:</strong> <?php echo e($reason); ?></p>
        <?php endif; ?>
        <p>You can try creating a new listing or contact our support team for more details.</p>
    <?php elseif($status === 'deleted'): ?>
        <p>Your listing has been permanently removed from our platform as requested.</p>
    <?php elseif($status === 'updated'): ?>
        <p>Your listing has been successfully updated and is currently pending review by our team.</p>
    <?php endif; ?>
    
    <br>
    <p>Thank you,<br><strong>SAHI GADI Team</strong></p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\emails\user_listing_status.blade.php ENDPATH**/ ?>