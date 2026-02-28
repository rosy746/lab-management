<?php
// api/upload-screenshot.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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

// Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Validasi token (opsional)
$headers = getallheaders();
$api_token = 'bang-ucup'; // Ganti dengan token Anda
if (isset($headers['Authorization'])) {
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    if ($token !== $api_token) {
        echo json_encode(['success' => false, 'error' => 'Invalid token']);
        exit;
    }
}

// Ambil data POST
$kode = $_POST['kode'] ?? '';
$status = $_POST['status'] ?? '';
$detail = $_POST['detail'] ?? '';
$timestamp = $_POST['timestamp'] ?? date('Y-m-d H:i:s');

// Validasi kode (wajib)
if (empty($kode)) {
    echo json_encode(['success' => false, 'error' => 'Kode is required']);
    exit;
}

// Validasi file upload
if (!isset($_FILES['screenshot'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['screenshot'];
$allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
$max_size = 10 * 1024 * 1024; // 10MB

// Validasi tipe file
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Only PNG, JPG, JPEG allowed']);
    exit;
}

// Validasi ukuran file
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'error' => 'File too large. Max 10MB']);
    exit;
}

// Generate nama file unik
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'screenshot_' . $kode . '_' . time() . '.' . $extension;
$upload_dir = '../tools/uploads/';

// Buat folder jika belum ada
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$upload_path = $upload_dir . $filename;

// Upload file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    
    // Cek apakah kode sudah ada di database
    $check_stmt = $pdo->prepare("SELECT id FROM absensi_screenshots WHERE kode = :kode LIMIT 1");
    $check_stmt->execute(['kode' => $kode]);
    $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    try {
        if ($existing) {
            // Update data yang sudah ada
            $stmt = $pdo->prepare("
                UPDATE absensi_screenshots 
                SET status = :status, 
                    detail = :detail, 
                    filename = :filename, 
                    filepath = :filepath, 
                    timestamp = :timestamp,
                    updated_at = NOW()
                WHERE kode = :kode
            ");
            
            $stmt->execute([
                'status' => $status,
                'detail' => $detail,
                'filename' => $filename,
                'filepath' => $upload_path,
                'timestamp' => $timestamp,
                'kode' => $kode
            ]);
            
            $screenshot_id = $existing['id'];
            $message = "Screenshot updated successfully";
            
        } else {
            // Insert data baru
            $stmt = $pdo->prepare("
                INSERT INTO absensi_screenshots 
                (kode, status, detail, filename, filepath, timestamp, created_at) 
                VALUES 
                (:kode, :status, :detail, :filename, :filepath, :timestamp, NOW())
            ");
            
            $stmt->execute([
                'kode' => $kode,
                'status' => $status,
                'detail' => $detail,
                'filename' => $filename,
                'filepath' => $upload_path,
                'timestamp' => $timestamp
            ]);
            
            $screenshot_id = $pdo->lastInsertId();
            $message = "Screenshot uploaded successfully";
        }
        
        // URL untuk akses gambar
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $image_url = $base_url . '/tools/uploads/' . $filename;
        
        // Link untuk view berdasarkan KODE (bukan ID)
        $view_link = $base_url . '/tools/view.php?kode=' . urlencode($kode);
        
        // Response sukses
        echo json_encode([
            'success' => true,
            'message' => $message,
            'id' => $screenshot_id,
            'screenshot_id' => $screenshot_id,
            'kode' => $kode,
            'filename' => $filename,
            'image_url' => $image_url,
            'view_link' => $view_link,
            'status' => $status,
            'timestamp' => $timestamp
        ]);
        
    } catch(PDOException $e) {
        // Hapus file jika gagal simpan ke database
        if (file_exists($upload_path)) {
            unlink($upload_path);
        }
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
}
?>