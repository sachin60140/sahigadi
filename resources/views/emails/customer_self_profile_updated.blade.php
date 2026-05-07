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
            <p>Dear {{ $customer->name }},</p>
            
            <p>This email is to confirm that you have successfully updated your profile on the SAHI GADI Customer Portal.</p>
            
            <p>Your current profile details are as follows:</p>
            
            <div class="details-box">
                <p><strong>Name:</strong> {{ $customer->name }}</p>
                <p><strong>Email:</strong> {{ $customer->email ?? 'Not provided' }}</p>
                <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                <p><strong>WhatsApp:</strong> {{ $customer->whatsapp_number ?? 'Not provided' }}</p>
                <p><strong>Company Name:</strong> {{ $customer->company_name ?? 'Not provided' }}</p>
                <p><strong>City:</strong> {{ $customer->city ?? 'Not provided' }}</p>
                <p><strong>State:</strong> {{ $customer->state ?? 'Not provided' }}</p>
                <p><strong>Pincode:</strong> {{ $customer->pincode ?? 'Not provided' }}</p>
                <p><strong>GST Number:</strong> {{ $customer->gst_number ?? 'Not provided' }}</p>
                <p><strong>Full Address:</strong> {{ $customer->address ?? 'Not provided' }}</p>
            </div>
            
            <p>If you did not make these changes, please contact our support team immediately to secure your account.</p>
            
            <p>Best regards,<br>The SAHI GADI Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} SAHI GADI. All rights reserved.</p>
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
