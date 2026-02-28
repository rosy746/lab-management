<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Scan QR WhatsApp</title>
    <meta http-equiv="refresh" content="30">
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f0fdf4; margin: 0; }
        .card { background: white; border-radius: 20px; padding: 36px; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,.1); max-width: 380px; width: 90%; }
        h2 { color: #166534; }
        p { color: #666; font-size: 13px; line-height: 1.6; }
        .steps { background: #f0fdf4; border-radius: 10px; padding: 12px 16px; text-align: left; margin-top: 16px; font-size: 12px; color: #166534; line-height: 1.8; }
        .note { font-size: 11px; color: #999; margin-top: 10px; }
        .back { display: inline-block; margin-top: 16px; color: #166534; font-size: 12px; text-decoration: none; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="card">
        <div style="font-size:36px">💬</div>
        <h2>Scan QR WhatsApp</h2>
        <p>Buka WA di HP → Perangkat Tertaut → Tautkan Perangkat → Scan QR ini</p>
        <div id="qrcode" style="display:inline-block;margin:16px 0"></div>
        <div class="steps">
            1. Buka WhatsApp di HP<br>
            2. Ketuk ⋮ → Perangkat Tertaut<br>
            3. Ketuk "Tautkan Perangkat"<br>
            4. Arahkan kamera ke QR di atas
        </div>
        <div class="note">Halaman refresh otomatis setiap 30 detik</div>
        <a href="/finance/wa-settings" class="back">← Kembali ke Pengaturan WA</a>
    </div>
    <script>
        new QRCode(document.getElementById('qrcode'), {
            text: @json($qr),
            width: 220, height: 220,
            colorDark: '#000000', colorLight: '#ffffff'
        });
    </script>
</body>
</html>
EOFcat > /home/maikel/lab-management/resources/views/finance/wa-qr.blade.php << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Scan QR WhatsApp</title>
    <meta http-equiv="refresh" content="30">
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f0fdf4; margin: 0; }
        .card { background: white; border-radius: 20px; padding: 36px; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,.1); max-width: 380px; width: 90%; }
        h2 { color: #166534; }
        p { color: #666; font-size: 13px; line-height: 1.6; }
        .steps { background: #f0fdf4; border-radius: 10px; padding: 12px 16px; text-align: left; margin-top: 16px; font-size: 12px; color: #166534; line-height: 1.8; }
        .note { font-size: 11px; color: #999; margin-top: 10px; }
        .back { display: inline-block; margin-top: 16px; color: #166534; font-size: 12px; text-decoration: none; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="card">
        <div style="font-size:36px">💬</div>
        <h2>Scan QR WhatsApp</h2>
        <p>Buka WA di HP → Perangkat Tertaut → Tautkan Perangkat → Scan QR ini</p>
        <div id="qrcode" style="display:inline-block;margin:16px 0"></div>
        <div class="steps">
            1. Buka WhatsApp di HP<br>
            2. Ketuk ⋮ → Perangkat Tertaut<br>
            3. Ketuk "Tautkan Perangkat"<br>
            4. Arahkan kamera ke QR di atas
        </div>
        <div class="note">Halaman refresh otomatis setiap 30 detik</div>
        <a href="/finance/wa-settings" class="back">← Kembali ke Pengaturan WA</a>
    </div>
    <script>
        new QRCode(document.getElementById('qrcode'), {
            text: @json($qr),
            width: 220, height: 220,
            colorDark: '#000000', colorLight: '#ffffff'
        });
    </script>
</body>
</html>
