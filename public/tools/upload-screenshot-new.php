<?php
/**
 * Upload Screenshot API - SECURE VERSION
 * File: tools/upload-screenshot.php
 * 
 * Fungsi:
 * - Menerima upload screenshot dari bot absensi
 * - Validasi token dan file
 * - Simpan ke database absensi_ppnu
 * - Return JSON response
 */

// Load configuration
require_once(__DIR__ . 'config.php');

// Set headers
header('Content-Type: application/json; charset=utf-8');

if (CORS_ENABLED) {
    header('Access-Control-Allow-Origin: ' . implode(', ', CORS_ALLOWED_ORIGINS));
    header('Access-Control-Allow-Methods: ' . implode(', ', CORS_ALLOWED_METHODS));
    header('Access-Control-Allow-Headers: ' . implode(', ', CORS_ALLOWED_HEADERS));
    
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// =====================================================
// VALIDASI METHOD
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logActivity('Invalid method: ' . $_SERVER['REQUEST_METHOD'], 'warning');
    jsonResponse([
        'success' => false,
        'error' => 'Method not allowed. Use POST.',
        'allowed_methods' => ['POST']
    ], 405);
}

// =====================================================
// VALIDASI API TOKEN
// =====================================================

if (!validateApiToken()) {
    logActivity('Unauthorized access attempt from IP: ' . getClientIP(), 'warning');
    jsonResponse([
        'success' => false,
        'error' => 'Unauthorized. Invalid or missing API token.'
    ], 401);
}

// =====================================================
// VALIDASI IP WHITELIST (jika diaktifkan)
// =====================================================

if (ENABLE_IP_WHITELIST) {
    $clientIP = getClientIP();
    if (!in_array($clientIP, IP_WHITELIST)) {
        logActivity("IP not whitelisted: $clientIP", 'warning');
        jsonResponse([
            'success' => false,
            'error' => 'Access denied. IP not whitelisted.'
        ], 403);
    }
}

// =====================================================
// AMBIL DATA POST
// =====================================================

$kode = trim($_POST['kode'] ?? '');
$status = trim($_POST['status'] ?? 'tidak_diketahui');
$detail = trim($_POST['detail'] ?? '');
$timestamp = $_POST['timestamp'] ?? date('Y-m-d H:i:s');

// Validasi timestamp format
if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp)) {
    $timestamp = date('Y-m-d H:i:s');
}

// =====================================================
// VALIDASI KODE (WAJIB)
// =====================================================

if (empty($kode)) {
    logActivity('Upload failed: Kode is empty', 'error');
    jsonResponse([
        'success' => false,
        'error' => 'Kode is required',
        'required_fields' => ['kode', 'screenshot']
    ], 400);
}

// Sanitize kode (hanya alphanumeric dan underscore)
$kode = preg_replace('/[^a-zA-Z0-9_-]/', '', $kode);

if (strlen($kode) < 3 || strlen($kode) > 100) {
    jsonResponse([
        'success' => false,
        'error' => 'Invalid kode format. Length must be between 3-100 characters.'
    ], 400);
}

// =====================================================
// VALIDASI FILE UPLOAD
// =====================================================

if (!isset($_FILES['screenshot'])) {
    logActivity("Upload failed for kode $kode: No file uploaded", 'error');
    jsonResponse([
        'success' => false,
        'error' => 'No file uploaded',
        'help' => 'Send file with parameter name: screenshot'
    ], 400);
}

$file = $_FILES['screenshot'];

// Check upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
    ];
    
    $errorMessage = $uploadErrors[$file['error']] ?? 'Unknown upload error';
    
    logActivity("Upload error for kode $kode: $errorMessage", 'error');
    jsonResponse([
        'success' => false,
        'error' => $errorMessage,
        'error_code' => $file['error']
    ], 400);
}

// =====================================================
// VALIDASI TIPE FILE
// =====================================================

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
    logActivity("Invalid file type for kode $kode: $mimeType", 'warning');
    jsonResponse([
        'success' => false,
        'error' => 'Invalid file type',
        'detected_type' => $mimeType,
        'allowed_types' => UPLOAD_ALLOWED_TYPES
    ], 400);
}

// =====================================================
// VALIDASI UKURAN FILE
// =====================================================

if ($file['size'] > UPLOAD_MAX_SIZE) {
    $maxSizeMB = UPLOAD_MAX_SIZE / (1024 * 1024);
    $fileSizeMB = $file['size'] / (1024 * 1024);
    
    logActivity("File too large for kode $kode: " . number_format($fileSizeMB, 2) . "MB", 'warning');
    jsonResponse([
        'success' => false,
        'error' => 'File too large',
        'file_size' => number_format($fileSizeMB, 2) . ' MB',
        'max_size' => number_format($maxSizeMB, 2) . ' MB'
    ], 400);
}

// =====================================================
// VALIDASI DIMENSI GAMBAR (opsional, untuk keamanan)
// =====================================================

$imageInfo = @getimagesize($file['tmp_name']);
if ($imageInfo === false) {
    logActivity("Invalid image file for kode $kode", 'warning');
    jsonResponse([
        'success' => false,
        'error' => 'Invalid image file'
    ], 400);
}

// =====================================================
// GENERATE NAMA FILE YANG AMAN
// =====================================================

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validasi extension sekali lagi
if (!in_array($extension, UPLOAD_ALLOWED_EXTENSIONS)) {
    $extension = 'png';  // Default to PNG
}

// Generate unique filename dengan format: screenshot_KODE_TIMESTAMP.ext
$filename = sprintf(
    'screenshot_%s_%s.%s',
    $kode,
    time(),
    $extension
);

// Ensure upload directory exists
if (!file_exists(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        logActivity("Failed to create upload directory: " . UPLOAD_DIR, 'error');
        jsonResponse([
            'success' => false,
            'error' => 'Failed to create upload directory'
        ], 500);
    }
}

$uploadPath = UPLOAD_DIR . $filename;

// =====================================================
// UPLOAD FILE
// =====================================================

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    logActivity("Failed to move uploaded file for kode $kode", 'error');
    jsonResponse([
        'success' => false,
        'error' => 'Failed to upload file'
    ], 500);
}

// Set proper permissions
chmod($uploadPath, 0644);

$fileSize = filesize($uploadPath);

logActivity("File uploaded successfully: $filename (Size: " . number_format($fileSize/1024, 2) . " KB)", 'info');

// =====================================================
// GET NAMA FROM MASTER (jika ada)
// =====================================================

$nama = null;

try {
    $pdo = getDB();
    
    // Ambil nama dari master
    $stmtMaster = $pdo->prepare("SELECT nama FROM absensi_kode_master WHERE kode = :kode LIMIT 1");
    $stmtMaster->execute(['kode' => $kode]);
    $master = $stmtMaster->fetch();
    
    if ($master) {
        $nama = $master['nama'];
    }
    
} catch(PDOException $e) {
    logActivity("Database error saat ambil nama: " . $e->getMessage(), 'error');
    // Continue without nama
}

// =====================================================
// SIMPAN KE DATABASE
// =====================================================

try {
    $pdo = getDB();
    
    // Cek apakah kode sudah ada di database
    $checkStmt = $pdo->prepare("
        SELECT id, filename, filepath 
        FROM absensi_screenshots 
        WHERE kode = :kode 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $checkStmt->execute(['kode' => $kode]);
    $existing = $checkStmt->fetch();
    
    $screenshotId = null;
    $action = 'insert';
    
    if ($existing) {
        // UPDATE existing record
        $action = 'update';
        
        $updateStmt = $pdo->prepare("
            UPDATE absensi_screenshots 
            SET 
                nama = :nama,
                status = :status,
                detail = :detail,
                filename = :filename,
                filepath = :filepath,
                filesize = :filesize,
                mime_type = :mime_type,
                timestamp = :timestamp,
                tanggal = DATE(:timestamp),
                jam = TIME(:timestamp),
                ip_address = :ip_address,
                user_agent = :user_agent,
                updated_at = NOW()
            WHERE kode = :kode
        ");
        
        $updateStmt->execute([
            'nama' => $nama,
            'status' => $status,
            'detail' => $detail,
            'filename' => $filename,
            'filepath' => $uploadPath,
            'filesize' => $fileSize,
            'mime_type' => $mimeType,
            'timestamp' => $timestamp,
            'ip_address' => getClientIP(),
            'user_agent' => getUserAgent(),
            'kode' => $kode
        ]);
        
        $screenshotId = $existing['id'];
        
        // Hapus file lama jika berbeda
        if ($existing['filepath'] !== $uploadPath && file_exists($existing['filepath'])) {
            @unlink($existing['filepath']);
            logActivity("Old file deleted: {$existing['filename']}", 'info');
        }
        
        logActivity("Screenshot updated for kode $kode (ID: $screenshotId)", 'info');
        
    } else {
        // INSERT new record
        $insertStmt = $pdo->prepare("
            INSERT INTO absensi_screenshots (
                kode, nama, status, detail, filename, filepath, 
                filesize, mime_type, timestamp, tanggal, jam,
                ip_address, user_agent, created_at
            ) VALUES (
                :kode, :nama, :status, :detail, :filename, :filepath,
                :filesize, :mime_type, :timestamp, DATE(:timestamp), TIME(:timestamp),
                :ip_address, :user_agent, NOW()
            )
        ");
        
        $insertStmt->execute([
            'kode' => $kode,
            'nama' => $nama,
            'status' => $status,
            'detail' => $detail,
            'filename' => $filename,
            'filepath' => $uploadPath,
            'filesize' => $fileSize,
            'mime_type' => $mimeType,
            'timestamp' => $timestamp,
            'ip_address' => getClientIP(),
            'user_agent' => getUserAgent()
        ]);
        
        $screenshotId = $pdo->lastInsertId();
        
        logActivity("New screenshot inserted for kode $kode (ID: $screenshotId)", 'info');
    }
    
    // Log ke absensi_logs
    $logStmt = $pdo->prepare("
        INSERT INTO absensi_logs (
            screenshot_id, action, description, 
            ip_address, user_agent, request_method, 
            response_code, created_at
        ) VALUES (
            :screenshot_id, :action, :description,
            :ip_address, :user_agent, :request_method,
            200, NOW()
        )
    ");
    
    $logStmt->execute([
        'screenshot_id' => $screenshotId,
        'action' => 'upload',
        'description' => "Screenshot uploaded via API ($action)",
        'ip_address' => getClientIP(),
        'user_agent' => getUserAgent(),
        'request_method' => 'POST'
    ]);
    
    // =====================================================
    // BUILD RESPONSE URLs
    // =====================================================
    
    $imageUrl = APP_BASE_URL . '/tools/uploads/' . $filename;
    $viewLink = APP_BASE_URL . '/tools/view.php?kode=' . urlencode($kode);
    
    // =====================================================
    // SUCCESS RESPONSE
    // =====================================================
    
    $response = [
        'success' => true,
        'message' => $action === 'update' ? 'Screenshot updated successfully' : 'Screenshot uploaded successfully',
        'action' => $action,
        'data' => [
            'id' => $screenshotId,
            'screenshot_id' => $screenshotId,  // Backward compatibility
            'kode' => $kode,
            'nama' => $nama,
            'status' => $status,
            'detail' => $detail,
            'filename' => $filename,
            'filesize' => $fileSize,
            'filesize_formatted' => number_format($fileSize / 1024, 2) . ' KB',
            'mime_type' => $mimeType,
            'image_url' => $imageUrl,
            'url' => $imageUrl,  // Backward compatibility
            'view_link' => $viewLink,
            'timestamp' => $timestamp,
            'uploaded_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    logActivity("Upload success for kode $kode: $filename", 'info');
    
    jsonResponse($response, 200);
    
} catch(PDOException $e) {
    // Rollback: Hapus file yang sudah diupload
    if (file_exists($uploadPath)) {
        @unlink($uploadPath);
    }
    
    $errorMessage = 'Database error: ' . $e->getMessage();
    logActivity("Database error for kode $kode: " . $e->getMessage(), 'error');
    
    jsonResponse([
        'success' => false,
        'error' => APP_DEBUG ? $errorMessage : 'Database error occurred',
        'debug_info' => APP_DEBUG ? [
            'sql_error' => $e->getMessage(),
            'sql_code' => $e->getCode()
        ] : null
    ], 500);
}

// =====================================================
// CLEANUP OLD SCREENSHOTS (background task)
// =====================================================

// Jalankan cleanup setiap 100 request (probability 1%)
if (rand(1, 100) === 1) {
    try {
        $pdo = getDB();
        
        // Ambil setting cleanup days
        $cleanupStmt = $pdo->query("
            SELECT setting_value 
            FROM absensi_settings 
            WHERE setting_key = 'auto_cleanup_days' 
            LIMIT 1
        ");
        
        $cleanupDays = 30;  // Default
        if ($cleanupRow = $cleanupStmt->fetch()) {
            $cleanupDays = (int)$cleanupRow['setting_value'];
        }
        
        // Delete old records
        $deleteStmt = $pdo->prepare("
            DELETE FROM absensi_screenshots 
            WHERE tanggal < DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ");
        
        $deleteStmt->execute(['days' => $cleanupDays]);
        $deletedRows = $deleteStmt->rowCount();
        
        if ($deletedRows > 0) {
            logActivity("Auto cleanup: Deleted $deletedRows old screenshots (older than $cleanupDays days)", 'info');
        }
        
    } catch(PDOException $e) {
        logActivity("Cleanup error: " . $e->getMessage(), 'error');
    }
}