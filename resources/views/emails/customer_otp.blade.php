<!DOCTYPE html>
<html>
<head>
    <title>SAHI GADI Verification OTP</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
        <h2 style="color: #e94560; text-align: center;">SAHI GADI</h2>
        <p>Hello,</p>
        <p>Your One Time Password (OTP) for <strong>{{ $purpose }}</strong> is:</p>
        <div style="text-align: center; margin: 20px 0;">
            <span style="font-size: 24px; font-weight: bold; background: #f4f4f4; padding: 10px 20px; border-radius: 4px; letter-spacing: 5px;">{{ $otp }}</span>
        </div>
        <p>This OTP is valid for 10 minutes. Please do not share it with anyone.</p>
        <p>If you did not request this, please ignore this email.</p>
        <br>
        <p>Best Regards,<br><strong>SAHI GADI Team</strong></p>
    </div>
</body>
</html>
