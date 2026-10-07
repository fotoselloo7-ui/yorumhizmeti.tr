<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisans Doğrulama Gerekli</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            padding: 20px;
        }
        .license-container {
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        .license-icon {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
        }
        .license-icon svg {
            width: 40px;
            height: 40px;
            stroke: #fff;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        .license-message {
            font-size: 16px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .license-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 28px;
            backdrop-filter: blur(12px);
            margin-bottom: 24px;
        }
        .license-card p {
            font-size: 14px;
            color: #cbd5e1;
            line-height: 1.6;
        }
        .license-card a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
        }
        .license-card a:hover {
            color: #93bbfd;
            text-decoration: underline;
        }
        .license-footer {
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="license-container">
        <div class="license-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0110 0v4"></path>
            </svg>
        </div>
        <h1>Lisans Doğrulama Gerekli</h1>
        <p class="license-message">
            Bu yazılımın çalışabilmesi için geçerli bir lisans anahtarı gereklidir.
            Lütfen yönetim panelinden lisans bilgilerinizi kontrol edin.
        </p>
        <div class="license-card">
            <p>
                Lisans anahtarınızı girmek veya yenilemek için
                <a href="/admin/lisans">Yönetim Paneli &rarr; Lisans Yönetimi</a>
                sayfasını ziyaret edin.
            </p>
        </div>
        <p class="license-footer">&copy; <?= date('Y') ?> — Bu yazılım lisanslı bir üründür.</p>
    </div>
</body>
</html>
