<?php
header("Content-Type: application/json; charset=UTF-8");

// 🔒 Token sederhana untuk otorisasi API
$TOKEN_VALID = "bang-ucup";

// Ambil token dari parameter GET (?token=)
$token = $_GET['token'] ?? '';

if ($token !== $TOKEN_VALID) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: invalid token"]);
    exit;
}

// Path ke file JSON asli
$jsonPath = __DIR__ . "/data/kode_config.json";

if (file_exists($jsonPath)) {
    $jsonData = file_get_contents($jsonPath);
    echo $jsonData;
} else {
    echo json_encode(["error" => "File tidak ditemukan"]);
}
?>
