<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Link Tidak Valid – Lab Control</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1A2517 0%,#2a3826 60%,#3d5438 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
.card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);width:100%;max-width:400px;overflow:hidden;animation:fadeUp .4s ease both;text-align:center}
.card-head{padding:40px 28px 32px;background:linear-gradient(135deg,#1A2517,#2a3826)}
.icon{width:64px;height:64px;border-radius:20px;background:rgba(248,113,113,.15);border:1.5px solid rgba(248,113,113,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.card-title{font-family:'Outfit',sans-serif;font-weight:800;font-size:22px;color:#fff;margin-bottom:6px}
.card-sub{font-size:13px;color:rgba(172,200,162,.4)}
.card-body{padding:28px}
.msg{background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;font-size:13px;color:#991b1b;font-weight:600;margin-bottom:20px}
.info{font-size:12px;color:#9ca3af;line-height:1.7}
footer{margin-top:20px;font-size:12px;color:rgba(172,200,162,.3);text-align:center}
</style>
</head>
<body>
<div>
    <div class="card">
        <div class="card-head">
            <div class="icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#f87171" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="card-title">Link Tidak Valid</div>
            <div class="card-sub">Lab Control · Nuris Jember</div>
        </div>
        <div class="card-body">
            <div class="msg">⚠ {{ $message ?? 'Link tidak valid atau sudah expired.' }}</div>
            <div class="info">
                Link akses lab hanya berlaku selama durasi sesi.<br>
                Hubungi admin atau tunggu link baru dikirim via WhatsApp.
            </div>
        </div>
    </div>
    <footer>© {{ date('Y') }} Lab Management System · Nuris Jember</footer>
</div>
</body>
</html>