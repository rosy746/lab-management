<?php
/**
 * Dashboard Kelola Kode Absensi
 * File: dashboard_kode.php
 * 
 * Fitur:
 * - View semua kode
 * - Add/Edit/Delete kode
 * - Toggle aktif/nonaktif
 * - Search & filter
 * - Export data
 */

// Load configuration
require_once(__DIR__ . 'config.php');

// Simple authentication (ganti dengan sistem auth yang lebih baik)
session_start();
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Login sederhana (untuk demo)
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // GANTI dengan credentials yang aman!
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $loginError = 'Username atau password salah!';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle AJAX requests
if (isset($_GET['action']) && $isLoggedIn) {
    header('Content-Type: application/json');
    
    try {
        $pdo = getDB();
        
        switch ($_GET['action']) {
            case 'get_all':
                $search = $_GET['search'] ?? '';
                $filter = $_GET['filter'] ?? 'all'; // all, aktif, nonaktif, prioritas
                
                $query = "SELECT * FROM absensi_kode_master WHERE 1=1";
                $params = [];
                
                if ($search) {
                    $query .= " AND (kode LIKE :search OR nama LIKE :search OR nomor_wa LIKE :search)";
                    $params['search'] = "%$search%";
                }
                
                if ($filter === 'aktif') {
                    $query .= " AND aktif = 1";
                } elseif ($filter === 'nonaktif') {
                    $query .= " AND aktif = 0";
                } elseif ($filter === 'prioritas') {
                    $query .= " AND prioritas = 1";
                }
                
                $query .= " ORDER BY prioritas DESC, nama ASC";
                
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode(['success' => true, 'data' => $data]);
                exit;
                
            case 'get_one':
                $id = $_GET['id'] ?? 0;
                $stmt = $pdo->prepare("SELECT * FROM absensi_kode_master WHERE id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode(['success' => true, 'data' => $data]);
                exit;
                
            case 'save':
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (empty($data['kode']) || empty($data['nama']) || empty($data['nomor_wa'])) {
                    echo json_encode(['success' => false, 'error' => 'Kode, nama, dan nomor WA wajib diisi']);
                    exit;
                }
                
                if (isset($data['id']) && $data['id'] > 0) {
                    // Update
                    $stmt = $pdo->prepare("
                        UPDATE absensi_kode_master SET
                            kode = ?, nama = ?, nomor_wa = ?, email = ?,
                            jabatan = ?, divisi = ?, aktif = ?, prioritas = ?, keterangan = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $data['kode'], $data['nama'], $data['nomor_wa'], $data['email'] ?? null,
                        $data['jabatan'] ?? null, $data['divisi'] ?? null,
                        $data['aktif'] ?? 1, $data['prioritas'] ?? 0, $data['keterangan'] ?? null,
                        $data['id']
                    ]);
                    $message = 'Data berhasil diupdate';
                } else {
                    // Insert
                    $stmt = $pdo->prepare("
                        INSERT INTO absensi_kode_master 
                        (kode, nama, nomor_wa, email, jabatan, divisi, aktif, prioritas, keterangan)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $data['kode'], $data['nama'], $data['nomor_wa'], $data['email'] ?? null,
                        $data['jabatan'] ?? null, $data['divisi'] ?? null,
                        $data['aktif'] ?? 1, $data['prioritas'] ?? 0, $data['keterangan'] ?? null
                    ]);
                    $message = 'Data berhasil ditambahkan';
                }
                
                echo json_encode(['success' => true, 'message' => $message]);
                exit;
                
            case 'delete':
                $id = $_GET['id'] ?? 0;
                $stmt = $pdo->prepare("DELETE FROM absensi_kode_master WHERE id = ?");
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus']);
                exit;
                
            case 'toggle_status':
                $id = $_GET['id'] ?? 0;
                $field = $_GET['field'] ?? 'aktif'; // aktif atau prioritas
                
                $stmt = $pdo->prepare("UPDATE absensi_kode_master SET $field = NOT $field WHERE id = ?");
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => 'Status berhasil diubah']);
                exit;
                
            case 'stats':
                $stmt = $pdo->query("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN aktif = 1 THEN 1 ELSE 0 END) as aktif,
                        SUM(CASE WHEN aktif = 0 THEN 1 ELSE 0 END) as nonaktif,
                        SUM(CASE WHEN prioritas = 1 THEN 1 ELSE 0 END) as prioritas
                    FROM absensi_kode_master
                ");
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode(['success' => true, 'data' => $stats]);
                exit;
        }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Show login page if not logged in
if (!$isLoggedIn) {
    include 'login_page.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kelola Kode Absensi - PPNU Risjember</title>
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
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
        
        .stat-card.total .number { color: #667eea; }
        .stat-card.aktif .number { color: #28a745; }
        .stat-card.nonaktif .number { color: #dc3545; }
        .stat-card.prioritas .number { color: #ffc107; }
        
        .main-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .filter-buttons {
            display: flex;
            gap: 8px;
        }
        
        .filter-btn {
            padding: 10px 16px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }
        
        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .modal-header h2 {
            font-size: 22px;
            color: #333;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .checkbox-group {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-buttons {
                flex-wrap: wrap;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>🎯 Dashboard Kelola Kode Absensi</h1>
                <div class="subtitle">Sistem Absensi Otomatis PPNU Risjember</div>
            </div>
            <div class="header-actions">
                <a href="?" class="btn btn-secondary btn-sm">🔄 Refresh</a>
                <a href="?logout" class="btn btn-danger btn-sm">🚪 Logout</a>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-container" id="statsContainer">
            <div class="stat-card total">
                <div class="number" id="totalKode">-</div>
                <div class="label">Total Kode</div>
            </div>
            <div class="stat-card aktif">
                <div class="number" id="totalAktif">-</div>
                <div class="label">Aktif</div>
            </div>
            <div class="stat-card nonaktif">
                <div class="number" id="totalNonaktif">-</div>
                <div class="label">Nonaktif</div>
            </div>
            <div class="stat-card prioritas">
                <div class="number" id="totalPrioritas">-</div>
                <div class="label">Prioritas</div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="🔍 Cari kode, nama, atau nomor WA...">
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <button class="filter-btn" data-filter="aktif">Aktif</button>
                    <button class="filter-btn" data-filter="nonaktif">Nonaktif</button>
                    <button class="filter-btn" data-filter="prioritas">Prioritas</button>
                </div>
                <button class="btn btn-primary" onclick="openModal()">➕ Tambah Kode</button>
            </div>
            
            <!-- Table -->
            <div class="table-container">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Nomor WA</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="8" class="loading">⏳ Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Form -->
    <div class="modal" id="formModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Kode Baru</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="kodeForm" onsubmit="saveData(event)">
                <input type="hidden" id="editId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Kode <span style="color:red">*</span></label>
                        <input type="text" id="kode" required>
                    </div>
                    <div class="form-group">
                        <label>Nama <span style="color:red">*</span></label>
                        <input type="text" id="nama" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor WA <span style="color:red">*</span></label>
                        <input type="text" id="nomor_wa" placeholder="628xxx" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" id="jabatan">
                    </div>
                    <div class="form-group">
                        <label>Divisi</label>
                        <input type="text" id="divisi">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea id="keterangan" rows="3"></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" id="aktif" checked>
                        <span>Aktif</span>
                    </label>
                    <label>
                        <input type="checkbox" id="prioritas">
                        <span>Prioritas</span>
                    </label>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary" style="flex:1">💾 Simpan</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()" style="flex:1">❌ Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let currentFilter = 'all';
        let currentSearch = '';
        
        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadData();
            
            // Search
            document.getElementById('searchInput').addEventListener('input', function(e) {
                currentSearch = e.target.value;
                loadData();
            });
            
            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    loadData();
                });
            });
        });
        
        // Load statistics
        function loadStats() {
            fetch('?action=stats')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalKode').textContent = data.data.total;
                        document.getElementById('totalAktif').textContent = data.data.aktif;
                        document.getElementById('totalNonaktif').textContent = data.data.nonaktif;
                        document.getElementById('totalPrioritas').textContent = data.data.prioritas;
                    }
                });
        }
        
        // Load table data
        function loadData() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading">⏳ Memuat data...</td></tr>';
            
            fetch(`?action=get_all&search=${encodeURIComponent(currentSearch)}&filter=${currentFilter}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <div>📭</div>
                                        <div>Tidak ada data</div>
                                    </td>
                                </tr>
                            `;
                            return;
                        }
                        
                        tbody.innerHTML = data.data.map(item => `
                            <tr>
                                <td><strong>${item.kode}</strong></td>
                                <td>${item.nama}</td>
                                <td>${item.nomor_wa}</td>
                                <td>${item.jabatan || '-'}</td>
                                <td>${item.divisi || '-'}</td>
                                <td>
                                    <span class="badge ${item.aktif == 1 ? 'badge-success' : 'badge-danger'}"
                                          style="cursor: pointer"
                                          onclick="toggleStatus(${item.id}, 'aktif')">
                                        ${item.aktif == 1 ? '✓ Aktif' : '✗ Nonaktif'}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge ${item.prioritas == 1 ? 'badge-warning' : 'badge-secondary'}"
                                          style="cursor: pointer"
                                          onclick="toggleStatus(${item.id}, 'prioritas')">
                                        ${item.prioritas == 1 ? '⭐ Ya' : '- Tidak'}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm" onclick="editData(${item.id})">✏️</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id}, '${item.nama}')">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="8" class="loading" style="color:red">❌ Error: ${err.message}</td></tr>`;
                });
        }
        
        // Open modal
        function openModal(id = null) {
            document.getElementById('modalTitle').textContent = id ? 'Edit Kode' : 'Tambah Kode Baru';
            document.getElementById('editId').value = id || '';
            document.getElementById('kodeForm').reset();
            
            if (id) {
                fetch(`?action=get_one&id=${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const d = data.data;
                            document.getElementById('kode').value = d.kode;
                            document.getElementById('nama').value = d.nama;
                            document.getElementById('nomor_wa').value = d.nomor_wa;
                            document.getElementById('email').value = d.email || '';
                            document.getElementById('jabatan').value = d.jabatan || '';
                            document.getElementById('divisi').value = d.divisi || '';
                            document.getElementById('keterangan').value = d.keterangan || '';
                            document.getElementById('aktif').checked = d.aktif == 1;
                            document.getElementById('prioritas').checked = d.prioritas == 1;
                        }
                    });
            } else {
                document.getElementById('aktif').checked = true;
                document.getElementById('prioritas').checked = false;
            }
            
            document.getElementById('formModal').classList.add('show');
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('formModal').classList.remove('show');
        }
        
        // Save data
        function saveData(e) {
            e.preventDefault();
            
            const formData = {
                id: document.getElementById('editId').value || null,
                kode: document.getElementById('kode').value,
                nama: document.getElementById('nama').value,
                nomor_wa: document.getElementById('nomor_wa').value,
                email: document.getElementById('email').value,
                jabatan: document.getElementById('jabatan').value,
                divisi: document.getElementById('divisi').value,
                keterangan: document.getElementById('keterangan').value,
                aktif: document.getElementById('aktif').checked ? 1 : 0,
                prioritas: document.getElementById('prioritas').checked ? 1 : 0
            };
            
            fetch('?action=save', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(formData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    closeModal();
                    loadStats();
                    loadData();
                } else {
                    alert('❌ Error: ' + data.error);
                }
            })
            .catch(err => alert('❌ Error: ' + err.message));
        }
        
        // Edit data
        function editData(id) {
            openModal(id);
        }
        
        // Delete data
        function deleteData(id, nama) {
            if (confirm(`Hapus kode "${nama}"?\n\nPerhatian: Data absensi terkait kode ini tidak akan terhapus.`)) {
                fetch(`?action=delete&id=${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ ' + data.message);
                            loadStats();
                            loadData();
                        } else {
                            alert('❌ Error: ' + data.error);
                        }
                    })
                    .catch(err => alert('❌ Error: ' + err.message));
            }
        }
        
        // Toggle status
        function toggleStatus(id, field) {
            fetch(`?action=toggle_status&id=${id}&field=${field}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        loadStats();
                        loadData();
                    } else {
                        alert('❌ Error: ' + data.error);
                    }
                })
                .catch(err => alert('❌ Error: ' + err.message));
        }
        
        // Close modal when clicking outside
        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>

<?php
// Login page template
function showLoginPage() {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Absensi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Login Dashboard</h2>
        <?php if (isset($GLOBALS['loginError'])): ?>
            <div class="error"><?= $GLOBALS['loginError'] ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
<?php
}

// Show login page if not logged in
if (!$isLoggedIn) {
    showLoginPage();
    exit;
}
?>