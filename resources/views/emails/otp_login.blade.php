<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 40px 0; }
        .container { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; padding: 32px; text-align: center; }
        .header h1 { margin: 0 0 4px; font-size: 22px; }
        .header p { margin: 0; opacity: 0.9; font-size: 14px; }
        .body { padding: 32px; text-align: center; }
        .body p { color: #666; font-size: 14px; line-height: 1.6; }
        .otp-code { display: inline-block; letter-spacing: 12px; font-size: 36px; font-weight: 800; color: #1a1a2e; background: #f0f2f5; padding: 16px 32px; border-radius: 16px; margin: 24px 0; border: 2px dashed #4361ee; }
        .warning { font-size: 12px; color: #999; margin-top: 20px; }
        .footer { background: #f8f9fa; padding: 16px 32px; text-align: center; font-size: 11px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Login Verification</h1>
            <p>COMSATS Hostel Management System</p>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>Your one-time verification code is:</p>
            <div class="otp-code">{{ $otpCode }}</div>
            <p>Enter this code on the verification page to complete your login.</p>
            <p class="warning">⚠️ This code expires in <strong>5 minutes</strong>. Do not share it with anyone.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} COMSATS Hostel Management System. This is an automated message.
        </div>
    </div>
</body>
</html>
