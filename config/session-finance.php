<?php

// ============================================================
// FILE: config/session-finance.php  (BUAT FILE BARU INI)
// Session khusus finance — terpisah total dari lab_management
// ============================================================

return [
    'driver'          => 'database',
    'connection'      => 'finance',
    'table'           => 'finance_sessions',
    'lifetime'        => 120,
    'expire_on_close' => false,
    'encrypt'         => false,
    'files'           => storage_path('framework/sessions'),
    'cookie'          => 'finance_session',        // ← nama cookie BERBEDA dari default
    'path'            => '/finance',               // ← hanya berlaku di path /finance
    'domain'          => env('SESSION_DOMAIN', null),
    'secure'          => env('SESSION_SECURE_COOKIE', false),
    'http_only'       => true,
    'same_site'       => 'lax',
    'partitioned'     => false,
];
