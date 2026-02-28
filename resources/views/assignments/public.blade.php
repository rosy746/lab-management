<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengumpulan Tugas – Nuris Jember</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f0f4ef;color:#1A2517;min-height:100vh}
a{text-decoration:none}
@keyframes navSlideDown{from{transform:translateY(-64px);opacity:0}to{transform:none;opacity:1}}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.pub-navbar{position:sticky;top:0;z-index:100;background:linear-gradient(135deg,#1A2517,#2a3826);box-shadow:0 2px 16px rgba(0,0,0,.28);animation:navSlideDown .4s cubic-bezier(.16,1,.3,1) both}
.pub-inner{max-width:1280px;margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;height:60px}
.pub-brand{display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0}
.pub-brand-icon{width:34px;height:34px;border-radius:9px;background:rgba(172,200,162,.12);border:1.5px solid rgba(172,200,162,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .18s}
.pub-brand-icon:hover{background:rgba(172,200,162,.22)}
.pub-brand-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1.2}
.pub-brand-sub{font-size:10px;color:rgba(172,200,162,.4)}
.pub-links{display:flex;align-items:center;gap:4px}
.pub-link{font-size:13px;font-weight:600;color:rgba(172,200,162,.55);text-decoration:none;padding:7px 13px;border-radius:8px;transition:color .15s,background .15s;white-space:nowrap}
.pub-link:hover,.pub-link.on{color:#ACC8A2;background:rgba(172,200,162,.1)}
.pub-btn{font-size:12px;font-weight:700;padding:7px 15px;border-radius:8px;color:#ACC8A2;border:1.5px solid rgba(172,200,162,.3);text-decoration:none;margin-left:6px;transition:background .15s,transform .15s;white-space:nowrap}
.pub-btn:hover{background:rgba(172,200,162,.08);transform:translateY(-1px)}
@media(max-width:600px){.pub-inner{padding:0 1rem}.pub-brand-sub{display:none}.pub-link{padding:6px 9px;font-size:12px}.pub-btn{padding:6px 11px;font-size:12px;margin-left:3px}}
.page-trans{position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#1A2517,#2d3d29);opacity:0;pointer-events:none;transition:opacity .22s ease}
.page-trans.go{opacity:1;pointer-events:all}

/* HERO */
.hero{background:linear-gradient(135deg,#1A2517 0%,#2a3826 60%,#3d5438 100%);padding:36px 24px 32px;text-align:center}
.hero-eyebrow{font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:rgba(172,200,162,.55);margin-bottom:6px}
.hero h1{font-family:'Outfit',sans-serif;font-weight:800;font-size:clamp(1.4rem,4vw,2rem);color:#fff;margin-bottom:6px}
.hero p{font-size:13px;color:rgba(172,200,162,.5)}

/* WRAP */
.wrap{max-width:900px;margin:0 auto;padding:24px 18px 48px;animation:fadeUp .4s ease both}

/* FILTER */
.filter-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;align-items:center}
.inp{border:1.5px solid #e5e7eb;border-radius:9px;padding:8px 12px;font-size:13px;background:#fff;outline:none;font-family:inherit;color:#1A2517;transition:border-color .15s}
.inp:focus{border-color:#ACC8A2}
.filter-label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em}

/* CARDS */
.assignment-grid{display:grid;gap:14px}
.acard{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;transition:transform .18s,box-shadow .18s;display:flex;flex-direction:column}
.acard:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(26,37,23,.1)}
.acard-head{padding:16px 20px;background:linear-gradient(135deg,#1A2517,#2a3826);display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.acard-title{font-family:'Outfit',sans-serif;font-weight:700;font-size:16px;color:#fff}
.acard-subject{font-size:11px;color:rgba(172,200,162,.5);margin-top:3px}
.badge-status{padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;white-space:nowrap}
.badge-open{background:rgba(172,200,162,.2);color:#ACC8A2;border:1px solid rgba(172,200,162,.3)}
.badge-closed{background:rgba(248,113,113,.15);color:#f87171;border:1px solid rgba(248,113,113,.25)}
.acard-body{padding:14px 20px;flex:1;display:flex;flex-direction:column;gap:10px}
.acard-meta{display:flex;flex-wrap:wrap;gap:8px}
.meta-item{display:flex;align-items:center;gap:5px;font-size:12px;color:#6b7280;background:#f8faf7;padding:4px 10px;border-radius:7px;border:1px solid #e8f0e6}
.acard-desc{font-size:12px;color:#9ca3af;line-height:1.6}
.acard-foot{padding:12px 20px;border-top:1px solid #f0f4ee;display:flex;align-items:center;justify-content:space-between}
.sub-count{font-size:11px;color:#9ca3af}
.btn-kumpul{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:700;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;text-decoration:none;transition:transform .15s,box-shadow .15s}
.btn-kumpul:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(26,37,23,.25)}
.btn-kumpul-disabled{background:#f3f4f6;color:#9ca3af;cursor:not-allowed}
.btn-kumpul-disabled:hover{transform:none;box-shadow:none}

/* DEADLINE */
.deadline-urgent{color:#ef4444!important}
.deadline-soon{color:#f59e0b!important}
.deadline-ok{color:#16a34a!important}

/* EMPTY */
.empty{text-align:center;padding:48px 24px;color:#9ca3af}
.empty-icon{font-size:48px;margin-bottom:12px}

/* FLASH */
.flash{padding:11px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
.flash-ok{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.flash-err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
</style>
</head>
<body>

<nav class="pub-navbar">
    <div class="pub-inner">
        <a href="{{ route('home') }}" class="pub-brand">
            <div class="pub-brand-icon">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="pub-brand-name">Lab Management</div>
                <div class="pub-brand-sub">Nuris Jember</div>
            </div>
        </a>
        <div class="pub-links">
            <a href="{{ route('home') }}" class="pub-link">Jadwal</a>
            <a href="{{ route('inventory.public') }}" class="pub-link">Inventaris</a>
            <a href="{{ route('rekap.public') }}" class="pub-link">Rekap</a>
            <a href="{{ route('assignment.public') }}" class="pub-link on">Tugas</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>
</nav>

<div class="hero">
    <p class="hero-eyebrow">Sistem Pengumpulan Tugas</p>
    <h1>📋 Kumpulkan Tugasmu</h1>
    <p>Pilih tugas · Upload file · Tanpa perlu login</p>
</div>

<div class="wrap">

    @if(session('success'))
        <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->has('error'))
        <div class="flash flash-err">⚠ {{ $errors->first('error') }}</div>
    @endif

    {{-- Filter --}}
    <div class="filter-row" style="gap:10px;margin-bottom:24px">
        <div style="display:flex;flex-direction:column;gap:4px">
            <span class="filter-label">Lembaga</span>
            <select id="filter-org" class="inp" style="min-width:180px" onchange="filterByOrg(this.value)">
                <option value="">— Semua Lembaga —</option>
                @foreach($organizations as $org)
                <option value="{{ $org->id }}">{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;flex-direction:column;gap:4px">
            <span class="filter-label">Kelas</span>
            <select id="filter-kelas" class="inp" style="min-width:180px" onchange="filterByKelas(this.value)" disabled>
                <option value="">— Pilih Lembaga Dulu —</option>
            </select>
        </div>
        <div style="display:flex;align-items:flex-end">
            <span id="filter-count" style="font-size:12px;color:#9ca3af;padding-bottom:2px"></span>
        </div>
    </div>

    {{-- Data kelas per org untuk JS --}}
    <script>
    const CLASSES_BY_ORG = @json($classes->groupBy('organization_id'));
    </script>

    {{-- Grid Tugas --}}
    @if($assignments->isEmpty())
        <div class="empty">
            <div class="empty-icon">📭</div>
            <div style="font-weight:700;color:#374151;margin-bottom:4px">Belum ada tugas</div>
            <div style="font-size:13px">Tugas akan muncul di sini setelah guru menambahkan</div>
        </div>
    @else
        <div class="assignment-grid" id="assignment-grid">
            @foreach($assignments as $a)
            @php
                $expired  = $a->isExpired();
                $diffMins = now()->diffInMinutes($a->deadline, false);
                $diffHrs  = now()->diffInHours($a->deadline, false);
                $deadlineClass = $expired ? 'deadline-urgent' : ($diffHrs < 24 ? 'deadline-soon' : 'deadline-ok');
                $deadlineLabel = $expired
                    ? 'Deadline terlewat'
                    : ($diffHrs < 1 ? 'Kurang dari 1 jam!' : ($diffHrs < 24 ? "Sisa {$diffHrs} jam" : $a->deadline->translatedFormat('d M Y, H:i')));
            @endphp
            <div class="acard" data-kelas="{{ strtolower($a->class_name) }}">
                <div class="acard-head">
                    <div>
                        <div class="acard-title">{{ $a->title }}</div>
                        <div class="acard-subject">{{ $a->subject_name }} · {{ $a->teacher->name }}</div>
                    </div>
                    <span class="badge-status {{ $expired ? 'badge-closed' : 'badge-open' }}">
                        {{ $expired ? 'Ditutup' : 'Buka' }}
                    </span>
                </div>
                <div class="acard-body">
                    <div class="acard-meta">
                        <span class="meta-item">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                            {{ $a->class_name }}
                        </span>
                        <span class="meta-item {{ $deadlineClass }}">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $deadlineLabel }}
                        </span>
                        <span class="meta-item">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $a->submissions_count ?? $a->submissions->count() }} dikumpulkan
                        </span>
                    </div>
                    @if($a->description)
                    <div class="acard-desc">{{ Str::limit($a->description, 120) }}</div>
                    @endif
                </div>
                <div class="acard-foot" style="flex-wrap:wrap;gap:8px">
                    <span class="sub-count">PDF, Word, PPT, Excel, ZIP · maks 10MB</span>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        @if($a->attachment_path)
                        <a href="{{ route('assignment.download.attachment', $a) }}" class="btn-kumpul" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#93c5fd">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4 4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            📎 Unduh Soal ({{ $a->attachment_size }})
                        </a>
                        @endif
                        @if(!$expired)
                            <a href="{{ route('assignment.show', $a) }}" class="btn-kumpul">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Kumpulkan
                            </a>
                        @else
                            <span class="btn-kumpul btn-kumpul-disabled">Ditutup</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<div class="page-trans" id="pt"></div>
<script>
function filterByOrg(orgId) {
    const kelasSel = document.getElementById('filter-kelas');
    kelasSel.innerHTML = '<option value="">— Semua Kelas —</option>';
    if (orgId && CLASSES_BY_ORG[orgId]) {
        CLASSES_BY_ORG[orgId].forEach(k => {
            kelasSel.innerHTML += `<option value="${k.name.toLowerCase()}">${k.name}</option>`;
        });
        kelasSel.disabled = false;
    } else {
        kelasSel.innerHTML = '<option value="">— Pilih Lembaga Dulu —</option>';
        kelasSel.disabled = true;
    }
    filterByKelas('');
}

function filterByKelas(val) {
    const cards = document.querySelectorAll('.acard');
    let shown = 0;
    cards.forEach(c => {
        const match = !val || c.dataset.kelas.includes(val.toLowerCase());
        c.style.display = match ? '' : 'none';
        if (match) shown++;
    });
    const total = document.querySelectorAll('.acard').length;
    document.getElementById('filter-count').textContent = val ? `${shown} tugas ditemukan` : (total ? `${total} tugas` : '');
}

// Init count
document.addEventListener('DOMContentLoaded', () => {
    const total = document.querySelectorAll('.acard').length;
    if (total) document.getElementById('filter-count').textContent = `${total} tugas`;
});

document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand, a.btn-kumpul').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
        const current = window.location.pathname;
        try {
            const target = new URL(href, window.location.href).pathname;
            if (target === current) return;
        } catch(err) {}
        e.preventDefault();
        const pt = document.getElementById('pt');
        pt.classList.add('go');
        setTimeout(() => { window.location.href = href; }, 220);
    });
});
window.addEventListener('pageshow', () => {
    document.getElementById('pt').classList.remove('go');
});
</script>
</body>
</html>