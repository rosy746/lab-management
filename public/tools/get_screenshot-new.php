<?php
/**
 * Get Screenshot API - SECURE VERSION
 * File: tools/get_screenshot.php
 * 
 * Fungsi:
 * - Mengambil data screenshot berdasarkan ID atau KODE
 * - Return JSON response dengan URL gambar
 * - Log setiap view
 */

// Load configuration
require_once(__DIR__ . 'config.php');

// Set headers
header('Content-Type: application/json; charset=utf-8');

if (CORS_ENABLED) {
    header('Access-Control-Allow-Origin: ' . implode(', ', CORS_ALLOWED_ORIGINS));
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: ' . implode(', ', CORS_ALLOWED_HEADERS));
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// =====================================================
// VALIDASI METHOD
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse([
        'success' => false,
        'error' => 'Method not allowed. Use GET.',
        'allowed_methods' => ['GET']
    ], 405);
}

// =====================================================
// AMBIL PARAMETER
// =====================================================

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$kode = isset($_GET['kode']) ? trim($_GET['kode']) : null;
$includeStats = isset($_GET['stats']) && $_GET['stats'] === 'true';

// Sanitize kode
if ($kode) {
    $kode = preg_replace('/[^a-zA-Z0-9_-]/', '', $kode);
}

// =====================================================
// VALIDASI PARAMETER
// =====================================================

if (!$id && !$kode) {
    jsonResponse([
        'success' => false,
        'error' => 'ID or KODE parameter required',
        'help' => 'Usage: ?id=123 or ?kode=XXX',
        'example' => '?kode=GERBANG001 or ?id=1'
    ], 400);
}

// =====================================================
// QUERY DATABASE
// =====================================================

try {
    $pdo = getDB();
    
    $query = "
        SELECT 
            s.id,
            s.kode,
            s.nama,
            s.status,
            s.detail,
            s.filename,
            s.filepath,
            s.filesize,
            s.mime_type,
            s.timestamp,
            s.tanggal,
            s.jam,
            s.ip_address,
            s.user_agent,
            s.created_at,
            s.updated_at,
            km.nomor_wa,
            km.jabatan,
            km.divisi,
            km.aktif as kode_aktif
        FROM absensi_screenshots s
        LEFT JOIN absensi_kode_master km ON s.kode = km.kode
    ";
    
    if ($kode) {
        $query .= " WHERE s.kode = :param";
        $param = $kode;
        $paramType = 'kode';
    } else {
        $query .= " WHERE s.id = :param";
        $param = $id;
        $paramType = 'id';
    }
    
    $query .= " ORDER BY s.id DESC LIMIT 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['param' => $param]);
    $data = $stmt->fetch();
    
    if (!$data) {
        logActivity("Screenshot not found ($paramType: $param)", 'info');
        jsonResponse([
            'success' => false,
            'error' => 'Screenshot not found',
            'searched_by' => $paramType,
            'searched_value' => $param
        ], 404);
    }
    
    // =====================================================
    // BUILD RESPONSE
    // =====================================================
    
    $imageUrl = APP_BASE_URL . '/tools/uploads/' . $data['filename'];
    $viewLink = APP_BASE_URL . '/tools/view.php?kode=' . urlencode($data['kode']);
    
    // Check if file exists
    $fileExists = file_exists($data['filepath']);
    
    $response = [
        'success' => true,
        'data' => [
            'id' => (int)$data['id'],
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'status' => $data['status'],
            'detail' => $data['detail'],
            'filename' => $data['filename'],
            'filesize' => (int)$data['filesize'],
            'filesize_formatted' => number_format($data['filesize'] / 1024, 2) . ' KB',
            'mime_type' => $data['mime_type'],
            'file_exists' => $fileExists,
            'image_url' => $imageUrl,
            'view_link' => $viewLink,
            'timestamp' => $data['timestamp'],
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ]
    ];
    
    // Tambahkan info kode master jika ada
    if ($data['nomor_wa']) {
        $response['data']['kode_master'] = [
            'nomor_wa' => $data['nomor_wa'],
            'jabatan' => $data['jabatan'],
            'divisi' => $data['divisi'],
            'aktif' => (bool)$data['kode_aktif']
        ];
    }
    
    // =====================================================
    // STATISTIK (jika diminta)
    // =====================================================
    
    if ($includeStats) {
        // Stats untuk kode ini
        $statsStmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_absensi,
                COUNT(DISTINCT tanggal) as total_hari,
                SUM(CASE WHEN status = 'sukses' THEN 1 ELSE 0 END) as total_sukses,
                SUM(CASE WHEN status = 'gagal' THEN 1 ELSE 0 END) as total_gagal,
                MIN(timestamp) as absensi_pertama,
                MAX(timestamp) as absensi_terakhir,
                AVG(filesize) as avg_filesize
            FROM absensi_screenshots
            WHERE kode = :kode
        ");
        
        $statsStmt->execute(['kode' => $data['kode']]);
        $stats = $statsStmt->fetch();
        
        $response['data']['statistics'] = [
            'total_absensi' => (int)$stats['total_absensi'],
            'total_hari' => (int)$stats['total_hari'],
            'total_sukses' => (int)$stats['total_sukses'],
            'total_gagal' => (int)$stats['total_gagal'],
            'success_rate' => $stats['total_absensi'] > 0 
                ? round(($stats['total_sukses'] / $stats['total_absensi']) * 100, 2) 
                : 0,
            'absensi_pertama' => $stats['absensi_pertama'],
            'absensi_terakhir' => $stats['absensi_terakhir'],
            'avg_filesize' => round($stats['avg_filesize'] / 1024, 2) . ' KB'
        ];
    }
    
    // =====================================================
    // LOG VIEW ACTIVITY
    // =====================================================
    
    try {
        $logStmt = $pdo->prepare("
            INSERT INTO absensi_logs (
                screenshot_id, action, description,
                ip_address, user_agent, request_method,
                request_url, response_code, created_at
            ) VALUES (
                :screenshot_id, 'view', 'Screenshot viewed via API',
                :ip_address, :user_agent, 'GET',
                :request_url, 200, NOW()
            )
        ");
        
        $logStmt->execute([
            'screenshot_id' => $data['id'],
            'ip_address' => getClientIP(),
            'user_agent' => getUserAgent(),
            'request_url' => $_SERVER['REQUEST_URI'] ?? ''
        ]);
        
        // Update view history
        $historyStmt = $pdo->prepare("
            INSERT INTO absensi_history (
                screenshot_id, kode, nama, action, 
                status, ip_address, user_agent, timestamp
            ) VALUES (
                :screenshot_id, :kode, :nama, 'view',
                :status, :ip_address, :user_agent, NOW()
            )
        ");
        
        $historyStmt->execute([
            'screenshot_id' => $data['id'],
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'status' => $data['status'],
            'ip_address' => getClientIP(),
            'user_agent' => getUserAgent()
        ]);
        
    } catch(PDOException $e) {
        // Log error tapi jangan stop response
        logActivity("Failed to log view: " . $e->getMessage(), 'warning');
    }
    
    logActivity("Screenshot retrieved: kode={$data['kode']}, id={$data['id']}", 'info');
    
    jsonResponse($response, 200);
    
} catch(PDOException $e) {
    logActivity("Database error: " . $e->getMessage(), 'error');
    
    jsonResponse([
        'success' => false,
        'error' => APP_DEBUG ? 'Database error: ' . $e->getMessage() : 'Database error occurred'
    ], 500);
}