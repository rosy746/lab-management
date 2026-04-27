<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Tugas – {{ $teacher->name ?? 'Panel Guru' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f0f4ef;color:#1A2517;min-height:100vh}
a{text-decoration:none}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* NAVBAR */
.navbar{background:linear-gradient(135deg,#1A2517,#2a3826);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(26,37,23,.3)}
.brand{display:flex;align-items:center;gap:10px}
.brand-icon{width:34px;height:34px;border-radius:10px;background:rgba(172,200,162,.12);border:1.5px solid rgba(172,200,162,.25);display:flex;align-items:center;justify-content:center}
.brand-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
.brand-sub{font-size:10px;color:rgba(172,200,162,.4)}
.nav-right{display:flex;align-items:center;gap:8px}
.teacher-badge{display:flex;align-items:center;gap:7px;padding:6px 13px;border-radius:9px;background:rgba(172,200,162,.1);border:1px solid rgba(172,200,162,.2);font-size:12px;font-weight:600;color:#ACC8A2}
.nav-btn{padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;color:#ACC8A2;border:1.5px solid rgba(172,200,162,.3);background:none;cursor:pointer;font-family:inherit;transition:background .15s}
.nav-btn:hover{background:rgba(172,200,162,.08)}

/* HERO */
.hero{background:linear-gradient(135deg,#1A2517 0%,#2a3826 60%,#3d5438 100%);padding:28px 24px 24px;text-align:center}
.hero h1{font-family:'Outfit',sans-serif;font-weight:800;font-size:22px;color:#fff;margin-bottom:4px}
.hero p{font-size:13px;color:rgba(172,200,162,.5)}

/* WRAP */
.wrap{max-width:1060px;margin:0 auto;padding:22px 18px 48px;animation:fadeUp .4s ease both}

/* FLASH */
.flash{padding:11px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
.flash-ok{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.flash-err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}

/* CREATE CARD */
.create-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;margin-bottom:22px}
.create-head{padding:14px 20px;background:linear-gradient(135deg,#1A2517,#2a3826);display:flex;align-items:center;justify-content:space-between;cursor:pointer;user-select:none}
.create-head-title{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
.create-head-sub{font-size:11px;color:rgba(172,200,162,.4);margin-top:2px}
.create-toggle{color:#ACC8A2;transition:transform .2s}
.create-toggle.open{transform:rotate(180deg)}
.create-body{padding:20px;display:none}
.create-body.open{display:block}
.field{margin-bottom:14px}
.field-label{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.inp{width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:13px;font-family:inherit;background:#fafcf9;outline:none;transition:border-color .15s}
.inp:focus{border-color:#ACC8A2;box-shadow:0 0 0 3px rgba(172,200,162,.1)}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.field-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
@media(max-width:600px){.field-row,.field-row-3{grid-template-columns:1fr}}
.btn-create{padding:11px 24px;border-radius:10px;border:none;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;transition:transform .15s,box-shadow .15s}
.btn-create:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(26,37,23,.25)}

/* PROGRESS UI */
.prog-wrap{width:100%;height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden;margin:10px 0 5px}
.prog-bar{height:100%;background:linear-gradient(90deg,#ACC8A2,#3d5438);border-radius:99px;transition:width .6s ease}
.prog-text{font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em}

/* TAB STATUS */
.status-tabs{display:flex;gap:8px;margin-bottom:18px;border-bottom:1px solid #e8f0e6;padding-bottom:10px}
.s-tab{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;color:#9ca3af;transition:all .15s}
.s-tab:hover{background:#fff;color:#1A2517}
.s-tab.active{background:#1A2517;color:#ACC8A2}

/* ASSIGNMENT CARDS */
.section-title{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px}
.acard{background:#fff;border-radius:16px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.05);overflow:hidden;margin-bottom:20px;transition:transform .2s}
.acard:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(26,37,23,.08)}
.acard-head{padding:16px 20px;background:#fcfdfb;border-bottom:1px solid #f0f4ee;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.acard-title{font-family:'Outfit',sans-serif;font-weight:800;font-size:16px;color:#1A2517}
.acard-meta-head{font-size:12px;color:#9ca3af;margin-top:4px;display:flex;align-items:center;gap:8px}
.meta-dot{width:4px;height:4px;border-radius:50%;background:#e5e7eb}
.acard-actions{display:flex;gap:6px;flex-shrink:0}
.btn-del{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:9px;border:1px solid #fecaca;background:#fff;color:#ef4444;cursor:pointer;transition:all .15s}
.btn-del:hover{background:#fef2f2;transform:scale(1.05)}
.acard-stats{display:grid;grid-template-columns:repeat(4,1fr);background:#fff}
.stat-cell{padding:14px 16px;text-align:center;border-right:1px solid #f9fafb}
.stat-cell:last-child{border-right:none}
.stat-val{font-family:'Outfit',sans-serif;font-size:22px;font-weight:800;color:#1A2517}
.stat-key{font-size:10px;color:#9ca3af;margin-top:3px;font-weight:700;text-transform:uppercase}

/* SUBMISSIONS TABLE */
.subs-wrap{overflow-x:auto}
.subs-table{width:100%;border-collapse:collapse;font-size:12px;min-width:620px}
.subs-table thead tr{background:#f8faf7;border-bottom:2px solid #e8f0e6}
.subs-table th{padding:9px 12px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap}
.subs-table tbody tr{border-top:1px solid #f5f5f5;transition:background .1s}
.subs-table tbody tr:hover{background:#fafcf9}
.subs-table td{padding:10px 12px;vertical-align:middle}
.sub-ext{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;font-size:9px;font-weight:700}
.ext-pdf{background:#fef2f2;color:#ef4444}
.ext-doc{background:#eff6ff;color:#2563eb}
.ext-ppt{background:#fff7ed;color:#ea580c}
.ext-xls{background:#f0fdf4;color:#16a34a}
.ext-zip{background:#faf5ff;color:#7c3aed}
.ext-other{background:#f9fafb;color:#6b7280}
.badge-submitted{background:#fffbeb;color:#d97706;border:1px solid #fde68a;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700}
.badge-graded{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700}
.btn-download{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;text-decoration:none;transition:background .15s}
.btn-download:hover{background:#dcfce7}
.grade-form{display:flex;align-items:center;gap:6px}
.grade-inp{width:60px;border:1.5px solid #e5e7eb;border-radius:7px;padding:5px 8px;font-size:12px;font-family:inherit;text-align:center;outline:none}
.grade-inp:focus{border-color:#ACC8A2}
.btn-grade{padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;border:none;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;cursor:pointer;font-family:inherit}

/* EMPTY */
.empty{text-align:center;padding:28px;color:#9ca3af;font-size:12px}
</style>
</head>
<body>

<div class="navbar">
    <div class="brand">
        <div class="brand-icon">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">Panel Guru – Tugas</div>
            <div class="brand-sub">Lab Management Nuris Jember</div>
        </div>
    </div>
    <div class="nav-right">
        @if($teacher)
        <div class="teacher-badge">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            {{ $teacher->name }} · {{ $teacher->token }}
        </div>
        @endif
        <a href="{{ route('assignment.public') }}" class="nav-btn">← Halaman Publik</a>
        @auth <a href="{{ route('dashboard') }}" class="nav-btn">Dashboard</a> @endauth
    </div>
</div>

<div class="hero">
    <h1>📋 Panel Guru – Kelola Tugas</h1>
    <p>Buat tugas baru · Lihat submission · Download & beri nilai</p>
</div>

<div class="wrap">

    @if(session('success'))
        <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="flash flash-err">⚠ {{ $errors->first() }}</div>
    @endif

    {{-- FORM BUAT TUGAS --}}
    <div class="create-card">
        <div class="create-head" onclick="toggleCreate()">
            <div>
                <div class="create-head-title">➕ Buat Tugas Baru</div>
                <div class="create-head-sub">Klik untuk membuka form</div>
            </div>
            <svg class="create-toggle" id="create-toggle" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div class="create-body" id="create-body">
            <form method="POST" action="{{ route('assignment.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="teacher_token" value="{{ $teacher->token ?? '' }}">

                <div class="field">
                    <label class="field-label">Judul Tugas *</label>
                    <input name="title" type="text" class="inp" placeholder="Contoh: Laporan Praktikum Excel" required value="{{ old('title') }}">
                </div>

                <div class="field-row-3">
                    <div class="field">
                        <label class="field-label">Lembaga *</label>
                        <select name="organization_id" id="adm_org" class="inp" required onchange="loadAdmKelas(this.value)" style="appearance:auto">
                            <option value="">— Pilih Lembaga —</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="grid-column: span 2">
                        <label class="field-label">Kelas * (Bisa pilih lebih dari satu)</label>
                        <div id="kelas_checkbox_container" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(120px, 1fr));gap:8px;background:#fafcf9;border:1.5px solid #e5e7eb;border-radius:9px;padding:12px;max-height:150px;overflow-y:auto">
                            <div style="color:#9ca3af;font-size:12px;grid-column:1/-1">Pilih lembaga terlebih dahulu</div>
                        </div>
                    </div>
                </div>
                <div class="field-row-3">
                    <div class="field">
                        <label class="field-label">Mata Pelajaran *</label>
                        <input name="subject_name" type="text" class="inp" placeholder="Contoh: TIK" required value="{{ old('subject_name') }}">
                    </div>
                    <div class="field" style="grid-column: span 2">
                        <label class="field-label">Deadline *</label>
                        <input name="deadline" type="datetime-local" class="inp" required value="{{ old('deadline') }}">
                    </div>
                </div>
                <script>
                const ADM_CLASSES = @json($classes->groupBy('organization_id'));
                function loadAdmKelas(orgId) {
                    const container = document.getElementById('kelas_checkbox_container');
                    container.innerHTML = '';
                    
                    if (orgId && ADM_CLASSES[orgId]) {
                        ADM_CLASSES[orgId].forEach(k => {
                            container.innerHTML += `
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:#1A2517;padding:4px">
                                    <input type="checkbox" name="class_names[]" value="${k.name}" style="width:16px;height:16px;accent-color:#1A2517">
                                    ${k.name}
                                </label>
                            `;
                        });
                    } else {
                        container.innerHTML = '<div style="color:#9ca3af;font-size:12px;grid-column:1/-1">Pilih lembaga terlebih dahulu</div>';
                    }
                }
                </script>

                <div class="field">
                    <label class="field-label">Keterangan</label>
                    <textarea name="description" class="inp" rows="2" placeholder="Instruksi atau catatan untuk siswa (opsional)">{{ old('description') }}</textarea>
                </div>

                <div class="field">
                    <label class="field-label">📎 File Lampiran (opsional)</label>
                    <input name="attachment" type="file" class="inp" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.png">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px">PDF, Word, PPT, Excel, ZIP, Gambar · maks 20MB · File ini bisa didownload siswa</div>
                </div>
                <button type="submit" class="btn-create">✓ Buat Tugas</button>
            </form>
        </div>
    </div>

    {{-- DAFTAR TUGAS --}}
    <div class="status-tabs">
        <div class="s-tab active" onclick="filterStatus('all')">Semua</div>
        <div class="s-tab" onclick="filterStatus('active')">Aktif (Buka)</div>
        <div class="s-tab" onclick="filterStatus('expired')">Arsip (Tutup)</div>
    </div>

    @forelse($assignments as $a)
    @php
        $total     = $a->submissions->count();
        $graded    = $a->submissions->where('status','graded')->count();
        $submitted = $a->submissions->where('status','submitted')->count();
        $expired   = $a->isExpired();
        
        // Dapatkan estimasi jumlah siswa (bisa dari model LabClass jika relasi ada)
        // Untuk sementara kita gunakan angka statis atau biarkan dinamis
        $estimatedStudents = 36; 
        $progress = ($total / $estimatedStudents) * 100;
    @endphp
    <div class="acard assignment-item" data-expired="{{ $expired ? '1' : '0' }}">
        <div class="acard-head">
            <div style="flex:1">
                <div class="acard-title">{{ $a->title }}</div>
                <div class="acard-meta-head">
                    <span>{{ $a->subject_name }}</span>
                    <div class="meta-dot"></div>
                    <span>{{ $a->class_name }}</span>
                    <div class="meta-dot"></div>
                    <span style="color:{{ $expired ? '#ef4444' : '#16a34a' }};font-weight:700">
                        {{ $expired ? 'Selesai' : 'Deadline: ' . $a->deadline->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>
                
                {{-- PROGRESS BAR --}}
                <div style="max-width:300px;margin-top:12px">
                    <div class="prog-text">Progress Pengumpulan: {{ $total }} Siswa</div>
                    <div class="prog-wrap">
                        <div class="prog-bar" style="width: {{ min($progress, 100) }}%"></div>
                    </div>
                </div>
            </div>
            <div class="acard-actions">
                <form method="POST" action="{{ route('assignment.destroy', $a) }}" onsubmit="return confirm('Hapus tugas ini beserta semua file submission?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del" title="Hapus Tugas">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="acard-stats">
            <div class="stat-cell">
                <div class="stat-val" style="color:#1A2517">{{ $total }}</div>
                <div class="stat-key">Total</div>
            </div>
            <div class="stat-cell" style="background:#fffbeb">
                <div class="stat-val" style="color:#d97706">{{ $submitted }}</div>
                <div class="stat-key">Belum Dinilai</div>
            </div>
            <div class="stat-cell" style="background:#f0fdf4">
                <div class="stat-val" style="color:#16a34a">{{ $graded }}</div>
                <div class="stat-key">Sudah Dinilai</div>
            </div>
            <div class="stat-cell" style="background:{{ $expired ? '#fef2f2' : 'rgba(172,200,162,.05)' }}">
                <div class="stat-val" style="color:{{ $expired ? '#ef4444' : '#ACC8A2' }}">{{ $expired ? 'Tutup' : 'Buka' }}</div>
                <div class="stat-key">Status</div>
            </div>
        </div>

        @if($a->submissions->isNotEmpty())
        <div class="subs-wrap">
            <table class="subs-table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Waktu Kumpul</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($a->submissions->sortByDesc('submitted_at') as $sub)
                    @php
                        $ext = strtolower($sub->file_ext ?? 'other');
                        $extClass = match($ext) {
                            'pdf'        => 'ext-pdf',
                            'doc','docx' => 'ext-doc',
                            'ppt','pptx' => 'ext-ppt',
                            'xls','xlsx' => 'ext-xls',
                            'zip','rar'  => 'ext-zip',
                            default      => 'ext-other'
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="sub-ext {{ $extClass }}">{{ strtoupper($ext) }}</span>
                                <span style="font-size:11px;color:#6b7280;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $sub->file_name }}">{{ $sub->file_name }}</span>
                            </div>
                        </td>
                        <td style="font-weight:700">{{ $sub->student_name }}</td>
                        <td style="color:#6b7280">{{ $sub->student_class }}</td>
                        <td style="color:#6b7280;white-space:nowrap">{{ $sub->submitted_at->translatedFormat('d M, H:i') }}</td>
                        <td>
                            @if($sub->status === 'graded')
                                <span class="badge-graded">Dinilai</span>
                            @else
                                <span class="badge-submitted">Belum dinilai</span>
                            @endif
                        </td>
                        <td style="font-weight:700;color:{{ $sub->grade !== null ? '#16a34a' : '#9ca3af' }}">
                            {{ $sub->grade !== null ? $sub->grade : '-' }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                                <a href="{{ route('assignment.download', $sub) }}" class="btn-download">
                                    ↓ Download
                                </a>
                                <form method="POST" action="{{ route('assignment.grade', $sub) }}" class="grade-form">
                                    @csrf
                                    <input type="number" name="grade" class="grade-inp" min="0" max="100" step="0.5" placeholder="0-100" value="{{ $sub->grade }}">
                                    <button type="submit" class="btn-grade">Simpan</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="empty">📭 Belum ada yang mengumpulkan</div>
        @endif
    </div>
    @empty
        <div class="empty" style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:48px">
            <div style="font-size:48px;margin-bottom:12px">📋</div>
            <div style="font-weight:700;color:#374151;margin-bottom:4px">Belum ada tugas</div>
            <div>Buat tugas baru dengan klik tombol di atas</div>
        </div>
    @endforelse
</div>

<script>
function toggleCreate() {
    const body    = document.getElementById('create-body');
    const toggle  = document.getElementById('create-toggle');
    const isOpen  = body.classList.toggle('open');
    toggle.classList.toggle('open', isOpen);
}

function filterStatus(status) {
    // Update tabs UI
    document.querySelectorAll('.s-tab').forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');

    // Filter items
    const items = document.querySelectorAll('.assignment-item');
    items.forEach(item => {
        const isExpired = item.dataset.expired === '1';
        if (status === 'all') {
            item.style.display = 'block';
        } else if (status === 'active') {
            item.style.display = isExpired ? 'none' : 'block';
        } else if (status === 'expired') {
            item.style.display = isExpired ? 'block' : 'none';
        }
    });
}

// Auto buka form jika ada error validasi
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('create-body').classList.add('open');
    document.getElementById('create-toggle').classList.add('open');
});
@endif
</script>
</body>
</html>