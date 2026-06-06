<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 24px; }
        .card { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ef476f, #d63d5e); color: #fff; padding: 24px; }
        .body { padding: 24px; color: #1a1a2e; line-height: 1.6; }
        .badge { display: inline-block; background: #ffd166; color: #333; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1 style="margin:0;font-size:20px;">COMSATS Hostel</h1>
            <p style="margin:8px 0 0;opacity:0.9;">Urgent broadcast</p>
        </div>
        <div class="body">
            <p><span class="badge">{{ $broadcastType }}</span></p>
            <p>{{ $broadcastMessage }}</p>
            <p style="font-size:12px;color:#666;margin-top:24px;">Log in to the student portal for full details.</p>
        </div>
    </div>
</body>
</html>
