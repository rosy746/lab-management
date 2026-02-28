<?php
/**
 * Get Kode API - SECURE VERSION
 * File: tools/get_kode.php
 * 
 * Fungsi:
 * - Return daftar kode absensi yang aktif
 * - Untuk digunakan oleh bot absensi Python
 * - Support filtering by status (aktif/nonaktif)
 */

// Load configuration
require_once(__DIR__ . '/../config/db_config.php');

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
        'error' => 'Method not allowed. Use GET.'
    ], 405);
}

// =====================================================
// VALIDASI TOKEN (query parameter untuk backward compatibility)
// =====================================================

$token = $_GET['token'] ?? '';

if ($token !== API_TOKEN) {
    logActivity('Unauthorized access to get_kode from IP: ' . getClientIP(), 'warning');
    jsonResponse([
        'success' => false,
        'error' => 'Unauthorized. Invalid or missing token.'
    ], 401);
}

// =====================================================
// PARAMETER OPSIONAL
// =====================================================

$aktifOnly = isset($_GET['aktif']) ? (bool)$_GET['aktif'] : true;
$includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] === 'true';
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'prioritas';  // prioritas, nama, kode
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

// =====================================================
// QUERY DATABASE
// =====================================================

try {
    $pdo = getDB();
    
    // Build query
    $query = "
        SELECT 
            id,
            kode,
            nama,
            nomor_wa,
            email,
            jabatan,
            divisi,
            aktif,
            prioritas,
            keterangan,
            created_at,
            updated_at
        FROM absensi_kode_master
    ";
    
    $conditions = [];
    $params = [];
    
    // Filter aktif/nonaktif
    if (!$includeInactive) {
        $conditions[] = "aktif = 1";
    }
    
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
    
    // Order by
    switch ($orderBy) {
        case 'prioritas':
            $query .= " ORDER BY prioritas DESC, nama ASC";
            break;
        case 'nama':
            $query .= " ORDER BY nama ASC";
            break;
        case 'kode':
            $query .= " ORDER BY kode ASC";
            break;
        default:
            $query .= " ORDER BY prioritas DESC, nama ASC";
    }
    
    // Limit
    if ($limit && $limit > 0) {
        $query .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($query);
    
    if ($limit && $limit > 0) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // =====================================================
    // PROCESS RESULTS
    // =====================================================
    
    $kodeList = [];
    $prioritasList = [];
    $normalList = [];
    
    foreach ($results as $row) {
        $kodeData = [
            'kode' => $row['kode'],
            'nama' => $row['nama'],
            'wa' => $row['nomor_wa'],  // Format lama untuk backward compatibility
            'nomor_wa' => $row['nomor_wa'],
            'email' => $row['email'],
            'jabatan' => $row['jabatan'],
            'divisi' => $row['divisi'],
            'aktif' => (bool)$row['aktif'],
            'prioritas' => (bool)$row['prioritas']
        ];
        
        $kodeList[] = $kodeData;
        
        // Pisahkan prioritas dan normal untuk info
        if ($row['prioritas']) {
            $prioritasList[] = $kodeData;
        } else {
            $normalList[] = $kodeData;
        }
    }
    
    // =====================================================
    // GET STATISTICS
    // =====================================================
    
    $statsStmt = $pdo->query("
        SELECT 
            COUNT(*) as total_kode,
            SUM(CASE WHEN aktif = 1 THEN 1 ELSE 0 END) as total_aktif,
            SUM(CASE WHEN aktif = 0 THEN 1 ELSE 0 END) as total_nonaktif,
            SUM(CASE WHEN prioritas = 1 THEN 1 ELSE 0 END) as total_prioritas
        FROM absensi_kode_master
    ");
    
    $stats = $statsStmt->fetch();
    
    // =====================================================
    // BUILD RESPONSE
    // =====================================================
    
    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => [
            'kode' => $kodeList,  // Format lama untuk backward compatibility
            'kode_list' => $kodeList,
            'prioritas' => $prioritasList,
            'normal' => $normalList
        ],
        'statistics' => [
            'total' => (int)$stats['total_kode'],
            'total_returned' => count($kodeList),
            'total_aktif' => (int)$stats['total_aktif'],
            'total_nonaktif' => (int)$stats['total_nonaktif'],
            'total_prioritas' => (int)$stats['total_prioritas']
        ],
        'meta' => [
            'filter' => [
                'aktif_only' => !$includeInactive,
                'order_by' => $orderBy,
                'limit' => $limit
            ]
        ]
    ];
    
    // =====================================================
    // LOG ACCESS
    // =====================================================
    
    try {
        $logStmt = $pdo->prepare("
            INSERT INTO absensi_logs (
                action, description, ip_address, user_agent,
                request_method, request_url, response_code, created_at
            ) VALUES (
                'get_kode', :description, :ip_address, :user_agent,
                'GET', :request_url, 200, NOW()
            )
        ");
        
        $logStmt->execute([
            'description' => "Kode list retrieved: " . count($kodeList) . " kode(s)",
            'ip_address' => getClientIP(),
            'user_agent' => getUserAgent(),
            'request_url' => $_SERVER['REQUEST_URI'] ?? ''
        ]);
        
    } catch(PDOException $e) {
        // Silent fail untuk logging
        logActivity("Failed to log get_kode access: " . $e->getMessage(), 'warning');
    }
    
    logActivity("Kode list retrieved: " . count($kodeList) . " kode(s)", 'info');
    
    jsonResponse($response, 200);
    
} catch(PDOException $e) {
    logActivity("Database error in get_kode: " . $e->getMessage(), 'error');
    
    jsonResponse([
        'success' => false,
        'error' => APP_DEBUG ? 'Database error: ' . $e->getMessage() : 'Database error occurred'
    ], 500);
}