<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-top: 5px solid #0d6efd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #0d6efd;
        }
        .content {
            margin-bottom: 30px;
        }
        .details-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            margin-top: 20px;
        }
        .details-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #888888;
            border-top: 1px solid #eeeeee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>SAHI GADI</h2>
        </div>
        
        <div class="content">
            <p>Dear <?php echo e($customer->name); ?>,</p>
            
            <p>This email is to notify you that your profile has been successfully updated by our administrative team.</p>
            
            <p>Your updated details are as follows:</p>
            
            <div class="details-box">
                <p><strong>Name:</strong> <?php echo e($customer->name); ?></p>
                <p><strong>Email:</strong> <?php echo e($customer->email); ?></p>
                <p><strong>Phone:</strong> <?php echo e($customer->phone); ?></p>
                <p><strong>WhatsApp:</strong> <?php echo e($customer->whatsapp_number ?? 'Not provided'); ?></p>
                <p><strong>Company Name:</strong> <?php echo e($customer->company_name ?? 'Not provided'); ?></p>
                <p><strong>City:</strong> <?php echo e($customer->city ?? 'Not provided'); ?></p>
                <p><strong>State:</strong> <?php echo e($customer->state ?? 'Not provided'); ?></p>
                <p><strong>Pincode:</strong> <?php echo e($customer->pincode ?? 'Not provided'); ?></p>
                <p><strong>GST Number:</strong> <?php echo e($customer->gst_number ?? 'Not provided'); ?></p>
                <p><strong>Full Address:</strong> <?php echo e($customer->address ?? 'Not provided'); ?></p>
            </div>
            
            <p>If you have any questions or notice any incorrect information, please do not hesitate to contact our support team.</p>
            
            <p>Best regards,<br>The SAHI GADI Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo e(date('Y')); ?> SAHI GADI. All rights reserved.</p>
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views/emails/customer_profile_updated.blade.php ENDPATH**/ ?>