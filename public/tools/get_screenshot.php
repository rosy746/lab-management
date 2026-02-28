<?php
// api/get-screenshot.php
// API untuk mengambil data screenshot berdasarkan ID atau KODE

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Koneksi Database
$host = 'localhost';
$dbname = 'cbt_system';
$username = 'wazzgroup';
$password = 'uFqzNdsgiX4c0dX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get parameter (bisa ID atau KODE)
$id = $_GET['id'] ?? null;
$kode = $_GET['kode'] ?? null;

if (!$id && !$kode) {
    echo json_encode(['success' => false, 'error' => 'ID or KODE parameter required']);
    exit;
}

// Query data
try {
    if ($kode) {
        // Query by KODE
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
    } else {
        // Query by ID
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
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
    }
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        // Base URL untuk gambar
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        
        // Response
        echo json_encode([
            'success' => true,
            'id' => $data['id'],
            'kode' => $data['kode'],
            'status' => $data['status'],
            'detail' => $data['detail'],
            'filename' => $data['filename'],
            'image_url' => $base_url . '/tools/uploads/' . $data['filename'],
            'view_link' => $base_url . '/tools/view.php?kode=' . urlencode($data['kode']),
            'timestamp' => $data['timestamp'],
            'created_at' => $data['created_at']
        ]);
        
        // Log view (opsional)
        $stmt_log = $pdo->prepare("
            INSERT INTO absensi_logs (screenshot_id, action, description, ip_address, user_agent)
            VALUES (:screenshot_id, 'view', 'Screenshot viewed via API', :ip, :user_agent)
        ");
        $stmt_log->execute([
            'screenshot_id' => $data['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Data not found']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>