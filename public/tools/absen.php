<?php
// ===== KONFIGURASI =====
$JSON_FILE = 'data/kode_config.json';
$PASSWORD = 'password'; // Ganti dengan password yang Anda inginkan

session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Di bagian setelah login berhasil (sekitar line 20)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
        
        // Tampilkan splashscreen sebelum redirect
        if (!isset($_SESSION['splash_shown'])) {
            $_SESSION['splash_shown'] = true;
            ?>
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>SYSTEM BOOT</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    
                    @keyframes glitch {
                        0% { transform: translate(0); }
                        20% { transform: translate(-2px, 2px); }
                        40% { transform: translate(-2px, -2px); }
                        60% { transform: translate(2px, 2px); }
                        80% { transform: translate(2px, -2px); }
                        100% { transform: translate(0); }
                    }
                    
                    @keyframes scanline {
                        0% { transform: translateY(-100%); }
                        100% { transform: translateY(100vh); }
                    }
                    
                    @keyframes blink {
                        0%, 49% { opacity: 1; }
                        50%, 100% { opacity: 0; }
                    }
                    
                    @keyframes typewriter {
                        from { width: 0; }
                        to { width: 100%; }
                    }
                    
                    @keyframes matrix {
                        0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
                        100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
                    }
                    
                    body { 
                        font-family: 'Courier New', monospace;
                        background: #000;
                        color: #0f0;
                        min-height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        overflow: hidden;
                        position: relative;
                    }
                    
                    body::before {
                        content: '';
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: 
                            repeating-linear-gradient(
                                0deg,
                                rgba(0, 255, 0, 0.03) 0px,
                                transparent 1px,
                                transparent 2px,
                                rgba(0, 255, 0, 0.03) 3px
                            );
                        pointer-events: none;
                        z-index: 1;
                    }
                    
                    body::after {
                        content: '';
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 3px;
                        background: linear-gradient(transparent, rgba(0, 255, 0, 0.5), transparent);
                        animation: scanline 3s linear infinite;
                        pointer-events: none;
                        z-index: 2;
                    }
                    
                    .matrix-rain {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        pointer-events: none;
                        z-index: 0;
                    }
                    
                    .matrix-char {
                        position: absolute;
                        color: #0f0;
                        font-size: 14px;
                        animation: matrix 3s linear infinite;
                        text-shadow: 0 0 5px #0f0;
                    }
                    
                    .terminal {
                        background: rgba(0, 0, 0, 0.9);
                        border: 2px solid #0f0;
                        padding: 40px;
                        max-width: 800px;
                        width: 90%;
                        position: relative;
                        z-index: 10;
                        box-shadow: 
                            0 0 30px rgba(0, 255, 0, 0.5),
                            inset 0 0 30px rgba(0, 255, 0, 0.1);
                    }
                    
                    .terminal::before,
                    .terminal::after {
                        content: '';
                        position: absolute;
                        width: 20px;
                        height: 20px;
                        border: 2px solid #0f0;
                    }
                    
                    .terminal::before {
                        top: -2px;
                        left: -2px;
                        border-right: none;
                        border-bottom: none;
                    }
                    
                    .terminal::after {
                        bottom: -2px;
                        right: -2px;
                        border-left: none;
                        border-top: none;
                    }
                    
                    .boot-header {
                        text-align: center;
                        margin-bottom: 40px;
                        animation: glitch 5s infinite;
                    }
                    
                    .system-title {
                        font-size: 32px;
                        font-weight: bold;
                        letter-spacing: 5px;
                        margin-bottom: 10px;
                        text-shadow: 0 0 15px rgba(0, 255, 0, 0.8);
                    }
                    
                    .system-subtitle {
                        font-size: 14px;
                        opacity: 0.7;
                        letter-spacing: 3px;
                    }
                    
                    .boot-log {
                        margin-bottom: 30px;
                        font-size: 12px;
                        line-height: 1.8;
                        max-height: 300px;
                        overflow-y: auto;
                    }
                    
                    .log-entry {
                        margin-bottom: 10px;
                        opacity: 0;
                        animation: fadeIn 0.5s forwards;
                    }
                    
                    .log-entry::before {
                        content: '> ';
                        color: #0f0;
                    }
                    
                    .log-success { color: #0f0; }
                    .log-warning { color: #ff0; }
                    .log-error { color: #f00; }
                    .log-info { color: #0af; }
                    
                    .progress-container {
                        margin: 30px 0;
                    }
                    
                    .progress-label {
                        font-size: 11px;
                        margin-bottom: 10px;
                        letter-spacing: 2px;
                    }
                    
                    .progress-bar {
                        width: 100%;
                        height: 4px;
                        background: rgba(0, 255, 0, 0.1);
                        border: 1px solid #0f0;
                        position: relative;
                        overflow: hidden;
                    }
                    
                    .progress-fill {
                        height: 100%;
                        background: #0f0;
                        width: 0%;
                        animation: progress 3s ease-in-out forwards;
                        box-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
                    }
                    
                    .access-granted {
                        text-align: center;
                        margin-top: 30px;
                        opacity: 0;
                        animation: fadeIn 1s 3s forwards;
                    }
                    
                    .access-text {
                        font-size: 24px;
                        font-weight: bold;
                        letter-spacing: 3px;
                        color: #0f0;
                        text-shadow: 0 0 20px rgba(0, 255, 0, 0.8);
                        margin-bottom: 20px;
                        animation: glitch 2s infinite 3s;
                    }
                    
                    .countdown {
                        font-size: 14px;
                        opacity: 0.7;
                        letter-spacing: 2px;
                    }
                    
                    .cursor {
                        display: inline-block;
                        width: 8px;
                        height: 16px;
                        background: #0f0;
                        animation: blink 1s infinite;
                        margin-left: 5px;
                    }
                    
                    @keyframes fadeIn {
                        to { opacity: 1; }
                    }
                    
                    @keyframes progress {
                        0% { width: 0%; }
                        25% { width: 30%; }
                        50% { width: 65%; }
                        75% { width: 85%; }
                        100% { width: 100%; }
                    }
                </style>
            </head>
            <body>
                <!-- Matrix Rain Effect -->
                <div class="matrix-rain" id="matrixRain"></div>
                
                <div class="terminal">
                    <div class="boot-header">
                        <div class="system-title">SECURE SYSTEM BOOT</div>
                        <div class="system-subtitle">INITIALIZING SECURITY PROTOCOLS</div>
                    </div>
                    
                    <div class="boot-log" id="bootLog">
                        <!-- Log entries will be added by JavaScript -->
                    </div>
                    
                    <div class="progress-container">
                        <div class="progress-label">SYSTEM INITIALIZATION</div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                    
                    <div class="access-granted">
                        <div class="access-text">ACCESS GRANTED</div>
                        <div class="countdown">Redirecting to control panel<span class="cursor"></span></div>
                    </div>
                </div>
                
                <script>
                    // Matrix rain effect
                    function createMatrixRain() {
                        const matrixRain = document.getElementById('matrixRain');
                        const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
                        
                        for (let i = 0; i < 50; i++) {
                            const char = document.createElement('div');
                            char.className = 'matrix-char';
                            char.textContent = chars[Math.floor(Math.random() * chars.length)];
                            char.style.left = Math.random() * 100 + 'vw';
                            char.style.animationDelay = Math.random() * 5 + 's';
                            char.style.animationDuration = (3 + Math.random() * 3) + 's';
                            matrixRain.appendChild(char);
                        }
                    }
                    
                    // Boot log messages
                    const bootMessages = [
                        { text: 'Initializing secure connection...', type: 'info', delay: 500 },
                        { text: 'Establishing encrypted tunnel...', type: 'info', delay: 1000 },
                        { text: 'Authentication protocol engaged...', type: 'info', delay: 1500 },
                        { text: 'Credentials verified...', type: 'success', delay: 2000 },
                        { text: 'Security clearance: LEVEL 5', type: 'warning', delay: 2500 },
                        { text: 'Loading control interface...', type: 'info', delay: 3000 },
                        { text: 'System ready for operation', type: 'success', delay: 3500 }
                    ];
                    
                    function displayBootLog() {
                        const bootLog = document.getElementById('bootLog');
                        
                        bootMessages.forEach((message, index) => {
                            setTimeout(() => {
                                const logEntry = document.createElement('div');
                                logEntry.className = `log-entry log-${message.type}`;
                                logEntry.textContent = message.text;
                                bootLog.appendChild(logEntry);
                                bootLog.scrollTop = bootLog.scrollHeight;
                            }, message.delay);
                        });
                    }
                    
                    // Initialize effects
                    createMatrixRain();
                    displayBootLog();
                    
                    // Redirect after splash screen
                    setTimeout(() => {
                        window.location.href = '<?= $_SERVER['PHP_SELF'] ?>';
                    }, 5000);
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $error = "ACCESS DENIED!";
    }
}

if (isset($_SESSION['logged_in']) && isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 1800) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    $_SESSION['last_activity'] = time();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ACCESS TERMINAL</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            @keyframes glitch {
                0% { transform: translate(0); }
                20% { transform: translate(-2px, 2px); }
                40% { transform: translate(-2px, -2px); }
                60% { transform: translate(2px, 2px); }
                80% { transform: translate(2px, -2px); }
                100% { transform: translate(0); }
            }
            
            @keyframes scanline {
                0% { transform: translateY(-100%); }
                100% { transform: translateY(100vh); }
            }
            
            @keyframes blink {
                0%, 49% { opacity: 1; }
                50%, 100% { opacity: 0; }
            }
            
            body { 
                font-family: 'Courier New', monospace;
                background: #000;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
                overflow: hidden;
            }
            
            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: 
                    repeating-linear-gradient(
                        0deg,
                        rgba(0, 255, 0, 0.03) 0px,
                        transparent 1px,
                        transparent 2px,
                        rgba(0, 255, 0, 0.03) 3px
                    );
                pointer-events: none;
                z-index: 1;
            }
            
            body::after {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background: linear-gradient(transparent, rgba(0, 255, 0, 0.3), transparent);
                animation: scanline 8s linear infinite;
                pointer-events: none;
                z-index: 2;
            }
            
            .login-container {
                background: rgba(0, 0, 0, 0.9);
                border: 2px solid #0f0;
                padding: 40px;
                max-width: 500px;
                width: 100%;
                position: relative;
                z-index: 10;
                box-shadow: 
                    0 0 20px rgba(0, 255, 0, 0.3),
                    inset 0 0 20px rgba(0, 255, 0, 0.05);
            }
            
            .login-container::before,
            .login-container::after {
                content: '';
                position: absolute;
                width: 20px;
                height: 20px;
                border: 2px solid #0f0;
            }
            
            .login-container::before {
                top: -2px;
                left: -2px;
                border-right: none;
                border-bottom: none;
            }
            
            .login-container::after {
                bottom: -2px;
                right: -2px;
                border-left: none;
                border-top: none;
            }
            
            .terminal-header {
                color: #0f0;
                margin-bottom: 30px;
                text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
            }
            
            .terminal-title {
                font-size: 24px;
                font-weight: bold;
                letter-spacing: 3px;
                margin-bottom: 10px;
                animation: glitch 3s infinite;
            }
            
            .terminal-subtitle {
                font-size: 12px;
                opacity: 0.7;
                letter-spacing: 2px;
            }
            
            .cursor {
                display: inline-block;
                width: 8px;
                height: 16px;
                background: #0f0;
                animation: blink 1s infinite;
                margin-left: 5px;
            }
            
            .error {
                background: rgba(255, 0, 0, 0.1);
                border: 1px solid #f00;
                color: #f00;
                padding: 15px;
                margin-bottom: 20px;
                font-size: 12px;
                letter-spacing: 1px;
                text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
                animation: glitch 0.3s infinite;
            }
            
            .error::before {
                content: '> ';
            }
            
            .input-group {
                margin-bottom: 25px;
            }
            
            .input-label {
                color: #0f0;
                font-size: 12px;
                margin-bottom: 10px;
                display: block;
                letter-spacing: 2px;
                text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
            }
            
            .input-label::before {
                content: '> ';
            }
            
            input[type="password"] {
                width: 100%;
                padding: 15px;
                background: rgba(0, 255, 0, 0.05);
                border: 1px solid #0f0;
                color: #0f0;
                font-family: 'Courier New', monospace;
                font-size: 16px;
                letter-spacing: 2px;
                outline: none;
                box-shadow: inset 0 0 10px rgba(0, 255, 0, 0.1);
                transition: all 0.3s;
            }
            
            input[type="password"]:focus {
                box-shadow: 
                    inset 0 0 10px rgba(0, 255, 0, 0.2),
                    0 0 20px rgba(0, 255, 0, 0.3);
                background: rgba(0, 255, 0, 0.08);
            }
            
            button {
                width: 100%;
                padding: 15px;
                background: transparent;
                border: 2px solid #0f0;
                color: #0f0;
                font-family: 'Courier New', monospace;
                font-size: 14px;
                font-weight: bold;
                letter-spacing: 3px;
                cursor: pointer;
                transition: all 0.3s;
                position: relative;
                overflow: hidden;
                text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
            }
            
            button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: rgba(0, 255, 0, 0.2);
                transition: all 0.3s;
            }
            
            button:hover::before {
                left: 0;
            }
            
            button:hover {
                box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
                text-shadow: 0 0 10px rgba(0, 255, 0, 1);
            }
            
            .system-info {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid rgba(0, 255, 0, 0.3);
                font-size: 10px;
                color: rgba(0, 255, 0, 0.5);
                letter-spacing: 1px;
            }
            
            .system-info div {
                margin: 5px 0;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="terminal-header">
                <div class="terminal-title">SECURE ACCESS TERMINAL</div>
                <div class="terminal-subtitle">AUTHENTICATION REQUIRED<span class="cursor"></span></div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label class="input-label">ENTER PASSPHRASE</label>
                    <input type="password" name="password" placeholder="****************" required autocomplete="current-password">
                </div>
                <button type="submit" name="login">[ AUTHENTICATE ]</button>
            </form>
            
            <div class="system-info">
                <div>> System: Attendance Control v2.0</div>
                <div>> Status: Awaiting Authentication</div>
                <div>> Timestamp: <?= date('Y-m-d H:i:s') ?></div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ===== FUNGSI BACA/TULIS JSON =====
function readData() {
    global $JSON_FILE;
    if (!file_exists($JSON_FILE)) {
        return ['kode' => []];
    }
    $content = file_get_contents($JSON_FILE);
    $data = json_decode($content, true);
    return $data ?: ['kode' => []];
}

function writeData($data) {
    global $JSON_FILE;
    $dir = dirname($JSON_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $temp = $JSON_FILE . '.tmp';
    file_put_contents($temp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    rename($temp, $JSON_FILE);
}

function validateKode($kode) {
    return preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $kode);
}

function validateNama($nama) {
    return strlen(trim($nama)) >= 2 && strlen(trim($nama)) <= 100;
}

function validateWA($wa) {
    return preg_match('/^628\d{8,12}$/', $wa);
}

// ===== HANDLE AJAX =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $data = readData();
    
    if ($_POST['action'] === 'toggle') {
        $kode = $_POST['kode'] ?? '';
        $found = false;
        
        foreach ($data['kode'] as &$item) {
            if ($item['kode'] === $kode) {
                $item['aktif'] = !$item['aktif'];
                $found = true;
                break;
            }
        }
        
        if ($found) {
            writeData($data);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Kode tidak ditemukan']);
        }
        exit;
    }
    
    if ($_POST['action'] === 'add') {
        $newKode = trim($_POST['kode'] ?? '');
        $newNama = trim($_POST['nama'] ?? '');
        $newWA = trim($_POST['wa'] ?? '');
        
        if (!validateKode($newKode)) {
            echo json_encode(['success' => false, 'error' => 'Kode tidak valid (3-50 karakter, alfanumerik)']);
            exit;
        }
        
        if (!validateNama($newNama)) {
            echo json_encode(['success' => false, 'error' => 'Nama tidak valid (2-100 karakter)']);
            exit;
        }
        
        if (!validateWA($newWA)) {
            echo json_encode(['success' => false, 'error' => 'Nomor WA tidak valid (harus 628xxx)']);
            exit;
        }
        
        foreach ($data['kode'] as $item) {
            if ($item['kode'] === $newKode) {
                echo json_encode(['success' => false, 'error' => 'Kode sudah ada!']);
                exit;
            }
        }
        
        $data['kode'][] = [
            'kode' => $newKode,
            'nama' => $newNama,
            'wa' => $newWA,
            'aktif' => true,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        writeData($data);
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($_POST['action'] === 'edit') {
        $oldKode = $_POST['old_kode'] ?? '';
        $newKode = trim($_POST['kode'] ?? '');
        $newNama = trim($_POST['nama'] ?? '');
        $newWA = trim($_POST['wa'] ?? '');
        
        if (!validateKode($newKode)) {
            echo json_encode(['success' => false, 'error' => 'Kode tidak valid (3-50 karakter, alfanumerik)']);
            exit;
        }
        
        if (!validateNama($newNama)) {
            echo json_encode(['success' => false, 'error' => 'Nama tidak valid (2-100 karakter)']);
            exit;
        }
        
        if (!validateWA($newWA)) {
            echo json_encode(['success' => false, 'error' => 'Nomor WA tidak valid (harus 628xxx)']);
            exit;
        }
        
        foreach ($data['kode'] as $item) {
            if ($item['kode'] === $newKode && $item['kode'] !== $oldKode) {
                echo json_encode(['success' => false, 'error' => 'Kode sudah digunakan!']);
                exit;
            }
        }
        
        $found = false;
        foreach ($data['kode'] as &$item) {
            if ($item['kode'] === $oldKode) {
                $item['kode'] = $newKode;
                $item['nama'] = $newNama;
                $item['wa'] = $newWA;
                $item['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        
        if ($found) {
            writeData($data);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Kode tidak ditemukan']);
        }
        exit;
    }
    
    if ($_POST['action'] === 'delete') {
        $kode = $_POST['kode'] ?? '';
        $originalCount = count($data['kode']);
        
        $data['kode'] = array_values(array_filter($data['kode'], function($item) use ($kode) {
            return $item['kode'] !== $kode;
        }));
        
        if (count($data['kode']) < $originalCount) {
            writeData($data);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Kode tidak ditemukan']);
        }
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Action tidak valid']);
    exit;
}

$data = readData();
$kodeList = $data['kode'];
$aktifCount = count(array_filter($kodeList, fn($k) => $k['aktif']));
$skipCount = count($kodeList) - $aktifCount;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[ CONTROL PANEL ]</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }
        
        @keyframes scanline {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100vh); }
        }
        
        @keyframes blink {
            0%, 49% { opacity: 1; }
            50%, 100% { opacity: 0; }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        body { 
            font-family: 'Courier New', monospace;
            background: #000;
            color: #0f0;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                repeating-linear-gradient(
                    0deg,
                    rgba(0, 255, 0, 0.03) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(0, 255, 0, 0.03) 3px
                );
            pointer-events: none;
            z-index: 1;
        }
        
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(transparent, rgba(0, 255, 0, 0.3), transparent);
            animation: scanline 8s linear infinite;
            pointer-events: none;
            z-index: 2;
        }
        
        .header {
            background: rgba(0, 0, 0, 0.9);
            border-bottom: 2px solid #0f0;
            padding: 20px;
            position: relative;
            z-index: 10;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            font-size: 28px;
            letter-spacing: 5px;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
            animation: glitch 3s infinite;
        }
        
        .header h1::before {
            content: '[ ';
        }
        
        .header h1::after {
            content: ' ]';
        }
        
        .logout {
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }
        
        .logout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(0, 255, 0, 0.2);
            transition: all 0.3s;
        }
        
        .logout:hover::before {
            left: 0;
        }
        
        .logout:hover {
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .stat-box {
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            padding: 20px;
            position: relative;
            box-shadow: inset 0 0 10px rgba(0, 255, 0, 0.1);
        }
        
        .stat-box::before,
        .stat-box::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            border: 1px solid #0f0;
        }
        
        .stat-box::before {
            top: -1px;
            left: -1px;
            border-right: none;
            border-bottom: none;
        }
        
        .stat-box::after {
            bottom: -1px;
            right: -1px;
            border-left: none;
            border-top: none;
        }
        
        .stat-label {
            font-size: 11px;
            opacity: 0.7;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .stat-label::before {
            content: '> ';
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
            position: relative;
            z-index: 10;
        }
        
        .panel {
            background: rgba(0, 0, 0, 0.9);
            border: 2px solid #0f0;
            padding: 25px;
            margin-bottom: 30px;
            position: relative;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }
        
        .panel::before,
        .panel::after {
            content: '';
            position: absolute;
            width: 15px;
            height: 15px;
            border: 2px solid #0f0;
        }
        
        .panel::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }
        
        .panel::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }
        
        .panel-title {
            font-size: 16px;
            letter-spacing: 3px;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
            border-bottom: 1px solid rgba(0, 255, 0, 0.3);
            padding-bottom: 10px;
        }
        
        .panel-title::before {
            content: '> ';
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            letter-spacing: 1px;
            outline: none;
            transition: all 0.3s;
            box-shadow: inset 0 0 10px rgba(0, 255, 0, 0.1);
        }
        
        .form-group input:focus {
            box-shadow: 
                inset 0 0 10px rgba(0, 255, 0, 0.2),
                0 0 15px rgba(0, 255, 0, 0.3);
            background: rgba(0, 255, 0, 0.08);
        }
        
        .form-group input::placeholder {
            color: rgba(0, 255, 0, 0.4);
        }
        
        .btn {
            padding: 12px 25px;
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(0, 255, 0, 0.2);
            transition: all 0.3s;
        }
        
        .btn:hover::before {
            left: 0;
        }
        
        .btn:hover {
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
            text-shadow: 0 0 10px rgba(0, 255, 0, 1);
        }
        
        .btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .btn-delete {
            border-color: #f00;
            color: #f00;
            text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }
        
        .btn-delete::before {
            background: rgba(255, 0, 0, 0.2);
        }
        
        .btn-delete:hover {
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.5);
            text-shadow: 0 0 10px rgba(255, 0, 0, 1);
        }
        
        .message {
            padding: 12px 20px;
            margin-bottom: 20px;
            display: none;
            border-left: 3px solid;
            font-size: 12px;
            letter-spacing: 1px;
            animation: slideIn 0.3s ease;
        }
        
        .message::before {
            content: '> ';
        }
        
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border-color: #f00;
            color: #f00;
            text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }
        
        .success-message {
            background: rgba(0, 255, 0, 0.1);
            border-color: #0f0;
            color: #0f0;
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }
        
        .data-list {
            display: grid;
            gap: 15px;
        }
        
        .data-row {
            background: rgba(0, 255, 0, 0.03);
            border: 1px solid #0f0;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: center;
            position: relative;
            animation: slideIn 0.5s ease;
            box-shadow: inset 0 0 10px rgba(0, 255, 0, 0.05);
        }
        
        .data-row::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 3px;
            height: 100%;
            background: #0f0;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
        }
        
        .data-info {
            padding-left: 15px;
        }
        
        .data-code {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 3px;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
            font-family: 'Courier New', monospace;
        }
        
        .data-detail {
            font-size: 11px;
            opacity: 0.7;
            margin: 5px 0;
            letter-spacing: 1px;
        }
        
        .data-detail::before {
            content: '> ';
            opacity: 0.5;
        }
        
        .data-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .status-badge {
            padding: 8px 15px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
            border: 1px solid;
            display: inline-block;
        }
        
        .status-active {
            border-color: #0f0;
            color: #0f0;
            background: rgba(0, 255, 0, 0.1);
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }
        
        .status-skip {
            border-color: #f00;
            color: #f00;
            background: rgba(255, 0, 0, 0.1);
            text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            opacity: 0.5;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 20px;
            animation: blink 2s infinite;
        }
        
        .empty-state-text {
            font-size: 14px;
            letter-spacing: 2px;
        }
        
        .empty-state-text::before {
            content: '> ';
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: #000;
            border: 2px solid #0f0;
            margin: 5% auto;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            position: relative;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.5);
            animation: slideIn 0.3s ease;
        }
        
        .modal-content::before,
        .modal-content::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #0f0;
        }
        
        .modal-content::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }
        
        .modal-content::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 255, 0, 0.3);
        }
        
        .modal-title {
            font-size: 18px;
            letter-spacing: 3px;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
        }
        
        .modal-title::before {
            content: '> ';
        }
        
        .close-btn {
            background: transparent;
            border: 1px solid #0f0;
            color: #0f0;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s;
        }
        
        .close-btn:hover {
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.5);
            text-shadow: 0 0 10px rgba(0, 255, 0, 1);
        }
        
        .modal-body input {
            width: 100%;
            padding: 12px;
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            letter-spacing: 1px;
            margin-bottom: 15px;
            outline: none;
            transition: all 0.3s;
            box-shadow: inset 0 0 10px rgba(0, 255, 0, 0.1);
        }
        
        .modal-body input:focus {
            box-shadow: 
                inset 0 0 10px rgba(0, 255, 0, 0.2),
                0 0 15px rgba(0, 255, 0, 0.3);
            background: rgba(0, 255, 0, 0.08);
        }
        
        .modal-body input::placeholder {
            color: rgba(0, 255, 0, 0.4);
        }
        
        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .modal-buttons button {
            flex: 1;
        }
        
        /* Custom Alert */
        .custom-alert {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.2s ease;
        }
        
        .alert-content {
            background: #000;
            border: 2px solid #0f0;
            margin: 15% auto;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            position: relative;
            box-shadow: 0 0 40px rgba(0, 255, 0, 0.6);
            animation: slideIn 0.3s ease;
        }
        
        .alert-content::before,
        .alert-content::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #0f0;
        }
        
        .alert-content::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }
        
        .alert-content::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }
        
        .alert-header {
            font-size: 16px;
            letter-spacing: 3px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 255, 0, 0.3);
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
        }
        
        .alert-header::before {
            content: '⚠ ';
        }
        
        .alert-body {
            font-size: 13px;
            line-height: 1.8;
            margin-bottom: 25px;
            letter-spacing: 1px;
            white-space: pre-line;
        }
        
        .alert-body::before {
            content: '> ';
            opacity: 0.5;
        }
        
        .alert-buttons {
            display: flex;
            gap: 15px;
        }
        
        .alert-buttons button {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: 2px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }
        
        .alert-buttons button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(0, 255, 0, 0.2);
            transition: all 0.3s;
        }
        
        .alert-buttons button:hover::before {
            left: 0;
        }
        
        .alert-buttons button:hover {
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
            text-shadow: 0 0 10px rgba(0, 255, 0, 1);
        }
        
        .alert-btn-danger {
            border-color: #f00 !important;
            color: #f00 !important;
            text-shadow: 0 0 5px rgba(255, 0, 0, 0.5) !important;
        }
        
        .alert-btn-danger::before {
            background: rgba(255, 0, 0, 0.2) !important;
        }
        
        .alert-btn-danger:hover {
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.5) !important;
            text-shadow: 0 0 10px rgba(255, 0, 0, 1) !important;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @media (max-width: 768px) {
            .header h1 { font-size: 20px; letter-spacing: 3px; }
            .stat-value { font-size: 24px; }
            .form-grid { grid-template-columns: 1fr; }
            .data-row { grid-template-columns: 1fr; }
            .data-actions { width: 100%; }
            .modal-buttons { flex-direction: column; }
            .alert-buttons { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-top">
                <h1>ATTENDANCE CONTROL SYSTEM</h1>
                <a href="?logout" class="logout">[ DISCONNECT ]</a>
            </div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">ACTIVE CODES</div>
                    <div class="stat-value"><?= $aktifCount ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">SKIPPED CODES</div>
                    <div class="stat-value"><?= $skipCount ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">TOTAL ENTRIES</div>
                    <div class="stat-value"><?= count($kodeList) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="panel">
            <div class="panel-title">ADD NEW ENTRY</div>
            <div class="error-message" id="errorMsg"></div>
            <div class="success-message" id="successMsg"></div>
            <form id="addForm">
                <div class="form-grid">
                    <div class="form-group">
                        <input type="text" name="kode" placeholder="UNIQUE CODE (3-50 chars)" required pattern="[a-zA-Z0-9_-]{3,50}">
                    </div>
                    <div class="form-group">
                        <input type="text" name="nama" placeholder="FULL NAME" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="wa" placeholder="WHATSAPP (628xxx)" required pattern="628\d{8,12}">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">[ INSERT ]</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="panel">
            <div class="panel-title">DATABASE RECORDS</div>
            <div class="data-list">
                <?php if (empty($kodeList)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">[ X ]</div>
                        <div class="empty-state-text">NO RECORDS FOUND</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($kodeList as $item): ?>
                    <div class="data-row" data-kode="<?= htmlspecialchars($item['kode']) ?>">
                        <div class="data-info">
                            <div class="data-code"><?= htmlspecialchars($item['kode']) ?></div>
                            <div class="data-detail">NAME: <?= htmlspecialchars($item['nama']) ?></div>
                            <div class="data-detail">CONTACT: +<?= htmlspecialchars($item['wa']) ?></div>
                            <?php if (isset($item['created_at'])): ?>
                            <div class="data-detail">TIMESTAMP: <?= htmlspecialchars($item['created_at']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="data-actions">
                            <span class="status-badge <?= $item['aktif'] ? 'status-active' : 'status-skip' ?>">
                                <?= $item['aktif'] ? '[ ACTIVE ]' : '[ SKIP ]' ?>
                            </span>
                            <button class="btn" onclick="toggleStatus('<?= htmlspecialchars($item['kode']) ?>')">
                                <?= $item['aktif'] ? '[ DEACTIVATE ]' : '[ ACTIVATE ]' ?>
                            </button>
                            <button class="btn" onclick="openEditModal('<?= htmlspecialchars($item['kode']) ?>', '<?= htmlspecialchars($item['nama']) ?>', '<?= htmlspecialchars($item['wa']) ?>')">
                                [ MODIFY ]
                            </button>
                            <button class="btn btn-delete" onclick="deleteKode('<?= htmlspecialchars($item['kode']) ?>')">
                                [ DELETE ]
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">MODIFY ENTRY</div>
                <button class="close-btn" onclick="closeEditModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="error-message" id="editErrorMsg"></div>
                <form id="editForm">
                    <input type="hidden" id="edit_old_kode" name="old_kode">
                    <input type="text" id="edit_kode" name="kode" placeholder="UNIQUE CODE" required pattern="[a-zA-Z0-9_-]{3,50}">
                    <input type="text" id="edit_nama" name="nama" placeholder="FULL NAME" required>
                    <input type="text" id="edit_wa" name="wa" placeholder="WHATSAPP (628xxx)" required pattern="628\d{8,12}">
                    <div class="modal-buttons">
                        <button type="submit" class="btn">[ SAVE ]</button>
                        <button type="button" class="btn" onclick="closeEditModal()">[ CANCEL ]</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Custom Alert Dialog -->
    <div id="customAlert" class="custom-alert">
        <div class="alert-content">
            <div class="alert-header" id="alertTitle">SYSTEM MESSAGE</div>
            <div class="alert-body" id="alertMessage"></div>
            <div class="alert-buttons" id="alertButtons"></div>
        </div>
    </div>
    
    <script>
        // Custom Alert Function
        function showAlert(message, title = 'SYSTEM MESSAGE', isConfirm = false, dangerConfirm = false) {
            return new Promise((resolve) => {
                const alertBox = document.getElementById('customAlert');
                const alertTitle = document.getElementById('alertTitle');
                const alertMessage = document.getElementById('alertMessage');
                const alertButtons = document.getElementById('alertButtons');
                
                alertTitle.textContent = title;
                alertMessage.textContent = message;
                alertButtons.innerHTML = '';
                
                if (isConfirm) {
                    const confirmBtn = document.createElement('button');
                    confirmBtn.textContent = '[ CONFIRM ]';
                    if (dangerConfirm) confirmBtn.className = 'alert-btn-danger';
                    confirmBtn.onclick = () => {
                        alertBox.style.display = 'none';
                        resolve(true);
                    };
                    
                    const cancelBtn = document.createElement('button');
                    cancelBtn.textContent = '[ CANCEL ]';
                    cancelBtn.onclick = () => {
                        alertBox.style.display = 'none';
                        resolve(false);
                    };
                    
                    alertButtons.appendChild(confirmBtn);
                    alertButtons.appendChild(cancelBtn);
                } else {
                    const okBtn = document.createElement('button');
                    okBtn.textContent = '[ OK ]';
                    okBtn.onclick = () => {
                        alertBox.style.display = 'none';
                        resolve(true);
                    };
                    alertButtons.appendChild(okBtn);
                }
                
                alertBox.style.display = 'block';
            });
        }
        
        function showMessage(msg, isError = false) {
            const errorDiv = document.getElementById('errorMsg');
            const successDiv = document.getElementById('successMsg');
            
            if (isError) {
                errorDiv.textContent = 'ERROR: ' + msg;
                errorDiv.style.display = 'block';
                successDiv.style.display = 'none';
            } else {
                successDiv.textContent = 'SUCCESS: ' + msg;
                successDiv.style.display = 'block';
                errorDiv.style.display = 'none';
            }
            
            setTimeout(() => {
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';
            }, 5000);
        }
        
        function toggleStatus(kode) {
            showAlert('CONFIRM STATUS CHANGE?', 'CONFIRMATION REQUIRED', true).then((confirmed) => {
                if (!confirmed) return;
                
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=toggle&kode=' + encodeURIComponent(kode)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showAlert('OPERATION FAILED: ' + (data.error || 'Unknown error'), 'ERROR');
                    }
                })
                .catch(err => {
                    showAlert('SYSTEM ERROR: ' + err.message, 'CRITICAL ERROR');
                });
            });
        }
        
        function deleteKode(kode) {
            showAlert('WARNING: DELETE ENTRY?\n\nTHIS ACTION CANNOT BE UNDONE!', 'DELETION WARNING', true, true).then((confirmed) => {
                if (!confirmed) return;
                
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=delete&kode=' + encodeURIComponent(kode)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Entry deleted successfully', false);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('DELETE FAILED: ' + (data.error || 'Unknown error'), 'ERROR');
                    }
                })
                .catch(err => {
                    showAlert('SYSTEM ERROR: ' + err.message, 'CRITICAL ERROR');
                });
            });
        }
        
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.btn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = '[ PROCESSING... ]';
            
            const formData = new FormData(this);
            formData.append('action', 'add');
            
            fetch('', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showMessage('Entry added successfully', false);
                    this.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(data.error || 'Operation failed', true);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(err => {
                showMessage('System error: ' + err.message, true);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
        
        function openEditModal(kode, nama, wa) {
            document.getElementById('edit_old_kode').value = kode;
            document.getElementById('edit_kode').value = kode;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_wa').value = wa;
            document.getElementById('editErrorMsg').style.display = 'none';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
        
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.btn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = '[ PROCESSING... ]';
            
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            fetch('', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    closeEditModal();
                    showMessage('Entry updated successfully', false);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    const errorDiv = document.getElementById('editErrorMsg');
                    errorDiv.textContent = 'ERROR: ' + (data.error || 'Operation failed');
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(err => {
                const errorDiv = document.getElementById('editErrorMsg');
                errorDiv.textContent = 'SYSTEM ERROR: ' + err.message;
                errorDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    </script>
</body>
</html>