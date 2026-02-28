<?php
// absensi/view.php
// Halaman untuk melihat screenshot berdasarkan KODE

// Koneksi Database
$host = 'localhost';
$dbname = 'cbt_system';
$username = 'wazzgroup';
$password = 'uFqzNdsgiX4c0dX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

// Ambil kode dari parameter
$kode = $_GET['kode'] ?? null;

if (!$kode) {
    die("Parameter kode tidak ditemukan");
}

// Query data berdasarkan kode
$stmt = $pdo->prepare("
    SELECT 
        id,
        kode,
        status,
        detail,
        filename,
        filepath,
        timestamp,
        created_at
    FROM absensi_screenshots 
    WHERE kode = :kode
    LIMIT 1
");

$stmt->execute(['kode' => $kode]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan untuk kode: " . htmlspecialchars($kode));
}

// Base URL untuk gambar
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$image_url = $base_url . '/tools/uploads/' . $data['filename'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Absensi - <?php echo htmlspecialchars($data['kode']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .info-card label {
            display: block;
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .info-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
        }
        
        .status-sukses {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .status-gagal {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        
        .screenshot-section {
            margin-top: 40px;
            text-align: center;
        }
        
        .screenshot-section h3 {
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
        }
        
        .screenshot-img {
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .screenshot-img:hover {
            transform: scale(1.02);
        }
        
        .actions {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-3px);
        }
        
        .footer {
            text-align: center;
            padding: 25px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }
        
        /* Modal fullscreen */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal img {
            max-width: 95%;
            max-height: 95%;
            border-radius: 10px;
        }
        
        .modal-close {
            position: absolute;
            top: 30px;
            right: 50px;
            color: white;
            font-size: 50px;
            cursor: pointer;
            z-index: 1001;
            transition: transform 0.3s ease;
        }
        
        .modal-close:hover {
            transform: scale(1.2);
        }
        
        @media print {
            .actions, .footer {
                display: none;
            }
            
            body {
                background: white;
            }
            
            .container {
                box-shadow: none;
            }
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📸 Hasil Absensi</h1>
            <p>Kode: <strong><?php echo htmlspecialchars($data['kode']); ?></strong></p>
        </div>
        
        <div class="content">
            <div class="info-grid">
                <div class="info-card">
                    <label>🔑 Kode Absensi</label>
                    <div class="value"><?php echo htmlspecialchars($data['kode']); ?></div>
                </div>
                
                <div class="info-card">
                    <label>📊 Status</label>
                    <div class="value">
                        <span class="status-badge status-<?php echo $data['status']; ?>">
                            <?php echo $data['status'] == 'sukses' ? '✅ SUKSES' : '❌ GAGAL'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-card">
                    <label>🕐 Waktu Absensi</label>
                    <div class="value">
                        <?php 
                        $date = new DateTime($data['timestamp']);
                        echo $date->format('d M Y, H:i'); 
                        ?>
                    </div>
                </div>
                
                <?php if ($data['detail']): ?>
                <div class="info-card">
                    <label>📝 Detail</label>
                    <div class="value" style="font-size: 16px;">
                        <?php echo htmlspecialchars($data['detail']); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="screenshot-section">
                <h3>📷 Screenshot Hasil Absensi</h3>
                <img src="<?php echo $image_url; ?>" 
                     alt="Screenshot Absensi" 
                     class="screenshot-img" 
                     onclick="openModal()">
                <p style="margin-top: 15px; color: #666; font-size: 14px;">
                    💡 Klik gambar untuk melihat ukuran penuh
                </p>
            </div>
            
            <div class="actions">
                <a href="<?php echo $image_url; ?>" 
                   download="<?php echo $data['filename']; ?>" 
                   class="btn btn-primary">
                    📥 Download Screenshot
                </a>
                <button class="btn btn-secondary" onclick="window.print()">
                    🖨️ Print
                </button>
                <button class="btn btn-secondary" onclick="shareLink()">
                    🔗 Share Link
                </button>
            </div>
        </div>
        
        <div class="footer">
            <p>© <?php echo date('Y'); ?> Sistem Absensi Otomatis</p>
            <p style="margin-top: 5px; font-size: 12px;">
                Generated automatically at <?php echo date('d M Y, H:i:s'); ?>
            </p>
        </div>
    </div>
    
    <!-- Modal Fullscreen -->
    <div class="modal" id="imageModal" onclick="closeModal()">
        <span class="modal-close">&times;</span>
        <img src="<?php echo $image_url; ?>" alt="Screenshot Fullscreen">
    </div>
    
    <script>
        function openModal() {
            document.getElementById('imageModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('imageModal').classList.remove('active');
        }
        
        // Close modal dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        function shareLink() {
            const url = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Hasil Absensi - <?php echo htmlspecialchars($data['kode']); ?>',
                    text: 'Lihat hasil absensi saya',
                    url: url
                }).then(() => {
                    console.log('Shared successfully');
                }).catch((error) => {
                    copyToClipboard(url);
                });
            } else {
                copyToClipboard(url);
            }
        }
        
        function copyToClipboard(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            alert('✅ Link berhasil dicopy ke clipboard!\n\n' + text);
        }
    </script>
</body>
</html>