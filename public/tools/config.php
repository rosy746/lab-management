<?php
/**
 * Database Configuration File
 * File: config/db_config.php
 * 
 * SECURITY NOTES:
 * - File ini HARUS di-exclude dari version control (.gitignore)
 * - Set permission 600 (hanya owner yang bisa read/write)
 * - Simpan di luar document root jika memungkinkan
 */

// =====================================================
// DATABASE CONFIGURATION
// =====================================================

// Database baru untuk sistem absensi
define('DB_HOST', 'localhost');
define('DB_NAME', 'absensi_ppnu');
define('DB_USER', 'maikel');  // Ganti dengan user database Anda
define('DB_PASS', 'vGtUCGuPvYvhIU0');  // ⚠️ WAJIB DIGANTI!
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// =====================================================
// API CONFIGURATION
// =====================================================

// API Token - Generate random token yang kuat
// Cara generate: openssl rand -hex 32
define('API_TOKEN', 'YOUR_SECURE_API_TOKEN_HERE');  // ⚠️ WAJIB DIGANTI!

// Alternatif: Ambil dari environment variable (lebih aman)
// define('API_TOKEN', getenv('ABSENSI_API_TOKEN'));

// =====================================================
// UPLOAD CONFIGURATION
// =====================================================

define('UPLOAD_DIR', __DIR__ . 'uploads/');  // Relative path
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024);  // 10MB
define('UPLOAD_ALLOWED_TYPES', ['image/png', 'image/jpeg', 'image/jpg']);
define('UPLOAD_ALLOWED_EXTENSIONS', ['png', 'jpg', 'jpeg']);

// =====================================================
// WHATSAPP GATEWAY CONFIGURATION
// =====================================================

define('WA_ENABLED', true);
define('WA_GATEWAY', 'fonnte');  // fonnte, custom, atau disabled

// Fonnte
define('FONNTE_TOKEN', 'YOUR_FONNTE_TOKEN_HERE');  // ⚠️ GANTI!
define('FONNTE_URL', 'https://api.fonnte.com/send');

// Custom Gateway
define('CUSTOM_GATEWAY_URL', 'http://your-wa-gateway.com/send');
define('CUSTOM_GATEWAY_TOKEN', 'your_custom_token');

// =====================================================
// APPLICATION SETTINGS
// =====================================================

define('APP_ENV', 'production');  // development, staging, production
define('APP_DEBUG', false);  // Set false di production!
define('APP_TIMEZONE', 'Asia/Jakarta');
define('APP_BASE_URL', 'https://maikel.wazzgroup.com');

// Error reporting (disable di production)
if (APP_DEBUG && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// =====================================================
// SECURITY SETTINGS
// =====================================================

define('ENABLE_RATE_LIMIT', true);
define('RATE_LIMIT_REQUESTS', 100);  // Max requests
define('RATE_LIMIT_PERIOD', 3600);  // Per hour

define('ENABLE_IP_WHITELIST', false);
define('IP_WHITELIST', [
    '127.0.0.1',
    '::1',
    // Tambahkan IP yang diizinkan
]);

// CORS Settings
define('CORS_ENABLED', true);
define('CORS_ALLOWED_ORIGINS', ['*']);  // Ganti dengan domain spesifik di production
define('CORS_ALLOWED_METHODS', ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']);
define('CORS_ALLOWED_HEADERS', ['Content-Type', 'Authorization']);

// =====================================================
// LOGGING SETTINGS
// =====================================================

define('LOG_ENABLED', true);
define('LOG_DIR', __DIR__ . '/../logs/');
define('LOG_LEVEL', 'info');  // debug, info, warning, error
define('LOG_MAX_FILES', 30);  // Keep logs for 30 days

// =====================================================
// DATABASE CONNECTION CLASS
// =====================================================

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATE
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch(PDOException $e) {
            $this->logError('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    private function logError($message) {
        if (LOG_ENABLED) {
            error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, LOG_DIR . 'db_errors.log');
        }
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// =====================================================
// HELPER FUNCTIONS
// =====================================================

/**
 * Get database connection
 * @return PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Validate API token
 * @return bool
 */
function validateApiToken() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        return false;
    }
    
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    return hash_equals(API_TOKEN, $token);
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Log activity
 */
function logActivity($message, $level = 'info') {
    if (!LOG_ENABLED) return;
    
    $logFile = LOG_DIR . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    
    return $ip;
}

/**
 * Get user agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
}

/**
 * Check rate limit
 */
function checkRateLimit($identifier) {
    if (!ENABLE_RATE_LIMIT) return true;
    
    // Implementasi rate limiting
    // Bisa pakai Redis atau database untuk tracking
    
    return true;  // Placeholder
}

// =====================================================
// AUTO-CREATE DIRECTORIES
// =====================================================

$directories = [
    UPLOAD_DIR,
    LOG_DIR,
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// =====================================================
// RETURN TRUE IF CONFIG LOADED SUCCESSFULLY
// =====================================================

return true;