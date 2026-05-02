<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rekap Penggunaan Lab – Nuris Jember</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f0f4ef;color:#1A2517;min-height:100vh}
a{text-decoration:none}

/* NAVBAR */
.navbar{background:linear-gradient(135deg,#1A2517,#2a3826);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(26,37,23,.3)}
.brand{display:flex;align-items:center;gap:10px}
.brand-icon{width:34px;height:34px;border-radius:10px;background:rgba(172,200,162,.12);border:1.5px solid rgba(172,200,162,.25);display:flex;align-items:center;justify-content:center}
.brand-name{font-family:Outfit,sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1.2}
.brand-sub{font-size:10px;color:rgba(172,200,162,.4)}
.nav-links{display:flex;align-items:center;gap:4px}
.nav-link{padding:7px 13px;border-radius:8px;font-size:13px;font-weight:600;color:rgba(172,200,162,.55);transition:all .15s}
.nav-link:hover,.nav-link.active{color:#ACC8A2;background:rgba(172,200,162,.1)}
.nav-btn{padding:7px 15px;border-radius:8px;font-size:13px;font-weight:700;color:#ACC8A2;border:1.5px solid rgba(172,200,162,.3);margin-left:6px;transition:all .15s}
.nav-btn:hover{background:rgba(172,200,162,.08)}

/* HERO */
.hero{background:linear-gradient(135deg,#1A2517 0%,#2a3826 60%,#3d5438 100%);padding:34px 24px 30px;text-align:center}
.hero h1{font-family:Outfit,sans-serif;font-weight:800;font-size:24px;color:#fff;margin-bottom:5px}
.hero p{font-size:13px;color:rgba(172,200,162,.5)}

/* FILTER */
.filter-bar{background:#fff;border-bottom:1px solid #e8f0e6;padding:12px 24px;display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;position:sticky;top:60px;z-index:90;box-shadow:0 2px 8px rgba(26,37,23,.04)}
.inp{border:1.5px solid #e5e7eb;border-radius:9px;padding:8px 12px;font-size:13px;background:#fafcf9;outline:none;font-family:inherit;color:#1A2517;transition:border-color .15s}
.inp:focus{border-color:#ACC8A2}
.btn-primary{background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:9px;padding:9px 22px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit}
.period-lbl{font-size:12px;color:#9ca3af;padding:6px 12px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb}

/* CONTAINER */
.wrap{max-width:1060px;margin:0 auto;padding:22px 18px 40px}

/* SUMMARY */
.sum-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
.sum-card{background:#fff;border-radius:13px;padding:16px 18px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05)}
.sum-lbl{font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.sum-val{font-family:Outfit,sans-serif;font-size:24px;font-weight:800;line-height:1}
.sum-sub{font-size:11px;color:#9ca3af;margin-top:4px}
.pbar-wrap{height:6px;background:#f3f4f6;border-radius:999px;overflow:hidden;margin-top:8px}
.pbar{height:6px;border-radius:999px}

/* TABS */
.tabs{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px}
.tab{display:flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:600;cursor:pointer;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-family:inherit;transition:all .15s;white-space:nowrap}
.tab:hover:not(.on){border-color:#ACC8A2;color:#1A2517;transform:translateY(-1px)}
.tab.on{background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border-color:transparent;box-shadow:0 4px 14px rgba(26,37,23,.22);transform:translateY(-1px)}
.tab-pct{font-size:10px;padding:1px 7px;border-radius:999px;background:#f3f4f6;color:#9ca3af;transition:all .15s}
.tab.on .tab-pct{background:rgba(172,200,162,.2);color:#ACC8A2}

/* PANEL */
.panel{display:none}
.panel.on{display:block;animation:fadeUp .22s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

/* LAB CARD */
.lab-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden}

/* HEADER */
.lab-hdr{padding:18px 22px;background:linear-gradient(135deg,#1A2517,#2a3826);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}

/* STATS */
.stat-row{display:grid;grid-template-columns:repeat(4,1fr);border-bottom:1px solid #f0f4ee}
.stat-cell{padding:14px 16px;text-align:center}
.stat-val{font-family:Outfit,sans-serif;font-size:22px;font-weight:800}
.stat-key{font-size:10px;color:#9ca3af;font-weight:600;margin-top:3px}

/* PROGRESS */
.prog{padding:14px 22px;border-bottom:1px solid #f0f4ee}
.prog-row{display:flex;align-items:center;gap:8px;margin-bottom:7px}
.prog-row:last-child{margin-bottom:0}
.prog-lbl{font-size:11px;font-weight:700;color:#6b7280;width:90px;flex-shrink:0}
.prog-track{flex:1;background:#f3f4f6;border-radius:999px;height:9px;overflow:hidden}
.prog-fill{height:9px;border-radius:999px}
.prog-pct{font-size:11px;font-weight:700;width:38px;text-align:right}

/* CALENDAR */
.cal{padding:16px 22px;border-bottom:1px solid #f0f4ee}
.sec-lbl{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px}
.cal-grid{display:flex;flex-wrap:wrap;gap:4px}
.dc{font-size:10px;text-align:center;padding:4px 2px;border-radius:6px;min-width:30px;cursor:default;transition:transform .1s}
.dc:hover{transform:scale(1.15);z-index:5}
.dc-sch {background:rgba(172,200,162,.28);color:#1A2517;font-weight:700}
.dc-book{background:#dbeafe;color:#1e40af;font-weight:700}
.dc-both{background:#f3e8ff;color:#6b21a8;font-weight:700}
.dc-mt  {background:#f9fafb;color:#d1d5db}
.dc-sun {background:#f9fafb;color:#e5e7eb}
.legend{display:flex;gap:12px;margin-top:10px;flex-wrap:wrap}
.leg{display:flex;align-items:center;gap:4px;font-size:10px;color:#9ca3af}
.leg-dot{width:14px;height:14px;border-radius:4px;flex-shrink:0}

/* DETAIL */
.detail{padding:20px 22px}
.detail-heading{font-size:12px;font-weight:700;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.tbl-wrap {
    overflow-x: auto; -webkit-overflow-scrolling: touch;
    max-height: 500px;
    scrollbar-width: thin;
    scrollbar-color: var(--acc) transparent;
}
.tbl-wrap::-webkit-scrollbar { width: 6px; height: 6px; }
.tbl-wrap::-webkit-scrollbar-track { background: transparent; }
.tbl-wrap::-webkit-scrollbar-thumb { background: var(--acc); border-radius: 10px; border: 2px solid #fff; }
.tbl-wrap::-webkit-scrollbar-thumb:hover { background: var(--acc2); }

.tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:12px;margin-bottom:20px}
.tbl thead { position: sticky; top: 0; z-index: 10; }
.tbl thead tr{background:#f8faf7; }
.tbl thead th{padding:12px 12px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap;background:#f8faf7;border-bottom:2px solid #e8f0e6}
.tbl tbody tr{transition:background .1s}
.tbl tbody tr:hover{background:#fafcf9}
.tbl td{padding:10px 12px;vertical-align:middle;border-bottom:1px solid #f5f5f5}
.badge{display:inline-flex;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:700}
.badge-day-0{background:#3b82f622;color:#3b82f6}
.badge-day-1{background:#8b5cf622;color:#8b5cf6}
.badge-day-2{background:#10b98122;color:#10b981}
.badge-day-3{background:#f59e0b22;color:#f59e0b}
.badge-day-4{background:#ef444422;color:#ef4444}
.badge-day-5{background:#06b6d422;color:#06b6d4}
.badge-freq{background:#f0f4ee;color:#1A2517}
.badge-book{background:#eff6ff;color:#2563eb}
.empty{text-align:center;padding:36px;color:#9ca3af;font-size:13px}

@media(max-width:680px){
  .sum-grid{grid-template-columns:1fr 1fr}
  .stat-row{grid-template-columns:1fr 1fr}
  .nav-link{padding:6px 9px;font-size:12px}
}

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
@keyframes navSlideDown{from{transform:translateY(-64px);opacity:0}to{transform:none;opacity:1}}
@media(max-width:600px){.pub-inner{padding:0 1rem}.pub-brand-sub{display:none}.pub-link{padding:6px 9px;font-size:12px}.pub-btn{padding:6px 11px;font-size:12px;margin-left:3px}}






.export-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;align-items:center}
.export-bar-label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-right:4px}
.btn-exp{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;font-size:12px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:transform .15s,filter .15s}
.btn-exp:hover{transform:translateY(-1px);filter:brightness(1.08)}
.btn-exp-xl{background:#16a34a;color:#fff}
.btn-exp-pdf{background:#dc2626;color:#fff}
.btn-exp-csv{background:#0369a1;color:#fff}
.btn-exp-print{background:#1A2517;color:#ACC8A2}
@media print{.pub-navbar,.filter-bar,.export-bar,.tabs,.page-trans{display:none!important}.panel{display:block!important}.lab-card{break-inside:avoid}.wrap{padding:0}}

.page-trans{position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#1A2517,#2d3d29);opacity:0;pointer-events:none;transition:opacity .22s ease}
.page-trans.go{opacity:1;pointer-events:all}
</style>
</head>
<body>

{{-- NAVBAR --}}
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
            <a href="{{ route('rekap.public') }}" class="pub-link on">Rekap</a>
            <a href="{{ route('assignment.public') }}" class="pub-link">Tugas</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>
</nav>

{{-- HERO --}}
<div class="hero">
    <h1>📊 Rekap Penggunaan Laboratorium</h1>
    <p>{{ $months[$month] }} {{ $year }} · Jadwal tetap & booking digabung</p>
</div>

{{-- FILTER --}}
<div class="filter-bar">
    <form method="GET" action="/rekap" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <select name="month" class="inp">
            @foreach($months as $m => $mName)
            <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ $mName }}</option>
            @endforeach
        </select>
        <select name="year" class="inp">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>
    <div class="period-lbl">{{ $startDate->translatedFormat('d M') }} – {{ $endDate->translatedFormat('d M Y') }}</div>
</div>

<div class="wrap">

    {{-- SUMMARY --}}
    <div class="sum-grid">
        <div class="sum-card">
            <div class="sum-lbl">Total Kapasitas</div>
            <div class="sum-val" style="color:#6b7280">{{ $summary['total_capacity'] }}</div>
            <div class="sum-sub">Slot tersedia bulan ini</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Jadwal Tetap</div>
            <div class="sum-val" style="color:#1A2517">{{ $summary['total_scheduled'] }}</div>
            <div class="sum-sub">Slot terisi rutin</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Booking</div>
            <div class="sum-val" style="color:#2563eb">{{ $summary['total_booking'] }}</div>
            <div class="sum-sub">Booking disetujui</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Tingkat Penggunaan</div>
            @php $pc = $summary['total_pct']; $pcColor = $pc>=70?'#16a34a':($pc>=40?'#d97706':'#dc2626'); $barColor = $pc>=70?'#22c55e':($pc>=40?'#f59e0b':'#ef4444'); @endphp
            <div class="sum-val" style="color:{{ $pcColor }}">{{ $pc }}%</div>
            <div class="sum-sub">{{ $summary['total_used'] }} dari {{ $summary['total_capacity'] }} slot</div>
            <div class="pbar-wrap"><div class="pbar" style="width:{{ $pc }}%;background:{{ $barColor }}"></div></div>
        </div>
    </div>


    {{-- EXPORT BAR --}}
    <div class="export-bar">
        <span class="export-bar-label">Export:</span>
        <button class="btn-exp btn-exp-xl" onclick="exportExcel()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 17l1.5-2.5L8.5 12H10l.75 1.5L11.5 12H13l-1.5 2.5L13 17h-1.5l-.75-1.5-.75 1.5H8.5z"/></svg>
            Excel
        </button>
        <button class="btn-exp btn-exp-csv" onclick="exportCSV()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/></svg>
            CSV
        </button>
        <button class="btn-exp btn-exp-pdf" onclick="exportPDF()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/></svg>
            PDF
        </button>
        <button class="btn-exp btn-exp-print" onclick="window.print()">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6v-8z"/></svg>
            Print
        </button>
    </div>

    {{-- TABS --}}
    <div class="tabs">
        @foreach($labData as $i => $lab)
        <button class="tab {{ $i===0?'on':'' }}" onclick="switchTab({{ $i }})">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ $lab['resource']->name }}
            <span class="tab-pct">{{ $lab['percentage'] }}%</span>
        </button>
        @endforeach
    </div>

    {{-- PANELS --}}
    @foreach($labData as $i => $lab)
    @php
        $pct      = $lab['percentage'];
        $pctColor = $pct>=70?'#86efac':($pct>=40?'#fcd34d':'#f87171');
        $ps       = $lab['totalCapacity']>0 ? ($lab['scheduledSlots']/$lab['totalCapacity']*100) : 0;
        $pb       = $lab['totalCapacity']>0 ? ($lab['bookingSlots']/$lab['totalCapacity']*100) : 0;
    @endphp
    <div class="panel {{ $i===0?'on':'' }}" id="panel-{{ $i }}">
        <div class="lab-card">

            {{-- Header --}}
            <div class="lab-hdr">
                <div>
                    <h2 style="font-family:Outfit,sans-serif;font-weight:800;font-size:18px;color:#fff;margin:0">🏫 {{ $lab['resource']->name }}</h2>
                    <p style="font-size:11px;color:rgba(172,200,162,.4);margin-top:4px">{{ $lab['totalCapacity'] }} slot kapasitas · {{ $totalSlotPerDay }} slot/hari</p>
                </div>
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="text-align:right">
                        <div style="font-family:Outfit,sans-serif;font-size:32px;font-weight:800;color:{{ $pctColor }};line-height:1">{{ $pct }}%</div>
                        <div style="font-size:10px;color:rgba(172,200,162,.35);margin-top:2px">Tingkat Penggunaan</div>
                    </div>
                    <svg viewBox="0 0 36 36" style="width:56px;height:56px;transform:rotate(-90deg)">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(172,200,162,.1)" stroke-width="3.5"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $pctColor }}" stroke-width="3.5"
                            stroke-dasharray="{{ $pct }} {{ 100-$pct }}" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- Stats --}}
            <div class="stat-row">
                <div class="stat-cell" style="background:rgba(172,200,162,.05)">
                    <div class="stat-val" style="color:#1A2517">{{ $lab['scheduledSlots'] }}</div>
                    <div class="stat-key">Jadwal Tetap</div>
                </div>
                <div class="stat-cell" style="background:#eff6ff">
                    <div class="stat-val" style="color:#2563eb">{{ $lab['bookingSlots'] }}</div>
                    <div class="stat-key">Booking</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-val" style="color:#1A2517">{{ $lab['totalUsed'] }}</div>
                    <div class="stat-key">Total Terpakai</div>
                </div>
                <div class="stat-cell" style="background:#f9fafb">
                    <div class="stat-val" style="color:#9ca3af">{{ $lab['totalFree'] }}</div>
                    <div class="stat-key">Slot Kosong</div>
                </div>
            </div>

            {{-- Progress --}}
            <div class="prog">
                <div class="prog-row">
                    <span class="prog-lbl">Jadwal Tetap</span>
                    <div class="prog-track"><div class="prog-fill" style="width:{{ $ps }}%;background:linear-gradient(90deg,#ACC8A2,#3d5438)"></div></div>
                    <span class="prog-pct" style="color:#1A2517">{{ round($ps,1) }}%</span>
                </div>
                <div class="prog-row">
                    <span class="prog-lbl">Booking</span>
                    <div class="prog-track"><div class="prog-fill" style="width:{{ $pb }}%;background:linear-gradient(90deg,#93c5fd,#2563eb)"></div></div>
                    <span class="prog-pct" style="color:#2563eb">{{ round($pb,1) }}%</span>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="cal">
                <div class="sec-lbl">Kalender Penggunaan</div>
                <div class="cal-grid">
                    @foreach($lab['dailyData'] as $day)
                    @php
                        $dc = 'dc-mt';
                        if ($day['isSunday']) $dc = 'dc-sun';
                        elseif ($day['schedule']>0 && $day['booking']>0) $dc = 'dc-both';
                        elseif ($day['schedule']>0) $dc = 'dc-sch';
                        elseif ($day['booking']>0) $dc = 'dc-book';
                    @endphp
                    <div class="dc {{ $dc }}"
                         style="{{ $day['isToday']?'outline:2px solid #ACC8A2;outline-offset:1px;':'' }}"
                         title="{{ $day['date']->translatedFormat('d M Y') }} — Jadwal: {{ $day['schedule'] }} | Booking: {{ $day['booking'] }} | Total: {{ $day['total'] }}/{{ $day['capacity'] }} slot">
                        <div>{{ $day['date']->format('d') }}</div>
                        @if(!$day['isSunday'] && $day['total']>0)
                        <div style="font-size:8px;margin-top:1px">{{ $day['total'] }}/{{ $day['capacity'] }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="legend">
                    @foreach([['dc-sch leg-dot','Jadwal Tetap'],['dc-book leg-dot','Booking'],['dc-both leg-dot','Keduanya'],['dc-mt leg-dot','Kosong'],['dc-sun leg-dot','Minggu']] as $l)
                    <div class="leg"><div class="leg-dot dc {{ $l[0] }}" style="padding:0;min-width:14px"></div>{{ $l[1] }}</div>
                    @endforeach
                </div>
            </div>

            {{-- DETAIL --}}
            <div class="detail">

                {{-- Jadwal Tetap --}}
                @if($lab['scheduleDetails']->isNotEmpty())
                <div class="detail-heading" style="color:#3d5438">
                    <span>📅</span> Jadwal Tetap
                    <span style="font-size:11px;font-weight:400;color:#9ca3af">({{ $lab['scheduleDetails']->count() }} entri · {{ $lab['scheduledSlots'] }} slot/bulan)</span>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Slot Waktu</th>
                                <th>Guru / Pengajar</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th style="text-align:center">Frekuensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $dayColorIdx = ['Monday'=>0,'Tuesday'=>1,'Wednesday'=>2,'Thursday'=>3,'Friday'=>4,'Saturday'=>5,'Sunday'=>6]; @endphp
                            @foreach($lab['scheduleDetails'] as $sd)
                            <tr>
                                <td>
                                    <span class="badge badge-day-{{ $dayColorIdx[$sd->day_of_week]??0 }}">
                                        {{ $sd->day_name_id }}
                                    </span>
                                </td>
                                <td style="color:#6b7280;font-weight:600;white-space:nowrap">
                                    {{ $sd->timeSlot?->name ?? '-' }}
                                    @if($sd->timeSlot)
                                    <div style="font-size:10px;color:#9ca3af">{{ \Carbon\Carbon::parse($sd->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($sd->timeSlot->end_time)->format('H:i') }}</div>
                                    @endif
                                </td>
                                <td style="font-weight:700;color:#1A2517">{{ $sd->teacher_name }}</td>
                                <td style="color:#374151">{{ $sd->labClass?->name ?? '-' }}</td>
                                <td style="color:#6b7280">{{ $sd->subject_name ?? '-' }}</td>
                                <td style="text-align:center">
                                    <span class="badge badge-freq">{{ $sd->occurrences }}×/bln</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- Booking --}}
                @if($lab['bookingDetails']->isNotEmpty())
                <div class="detail-heading" style="color:#1d4ed8">
                    <span>📝</span> Booking Disetujui
                    <span style="font-size:11px;font-weight:400;color:#9ca3af">({{ $lab['bookingDetails']->count() }} booking)</span>
                </div>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Slot Waktu</th>
                                <th>Pengajar</th>
                                <th>Kelas</th>
                                <th>Kegiatan / Mapel</th>
                                <th style="text-align:center">Peserta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lab['bookingDetails'] as $bd)
                            <tr>
                                <td style="white-space:nowrap">
                                    <div style="font-weight:700;color:#1A2517">{{ \Carbon\Carbon::parse($bd->booking_date)->translatedFormat('d M Y') }}</div>
                                    <div style="font-size:10px;color:#9ca3af">{{ \Carbon\Carbon::parse($bd->booking_date)->translatedFormat('l') }}</div>
                                </td>
                                <td style="color:#6b7280;font-weight:600;white-space:nowrap">
                                    {{ $bd->timeSlot?->name ?? '-' }}
                                    @if($bd->timeSlot)
                                    <div style="font-size:10px;color:#9ca3af">{{ \Carbon\Carbon::parse($bd->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($bd->timeSlot->end_time)->format('H:i') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:700;color:#1A2517">{{ $bd->teacher_name }}</div>
                                    @if($bd->teacher_phone)<div style="font-size:10px;color:#9ca3af">{{ $bd->teacher_phone }}</div>@endif
                                </td>
                                <td style="color:#374151">{{ $bd->class_name ?? '-' }}</td>
                                <td>
                                    <div style="font-weight:600;color:#1A2517">{{ $bd->title }}</div>
                                    @if($bd->subject_name)<div style="font-size:10px;color:#9ca3af">{{ $bd->subject_name }}</div>@endif
                                </td>
                                <td style="text-align:center">
                                    <span class="badge badge-book">{{ $bd->participant_count ?? '-' }} org</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($lab['scheduleDetails']->isEmpty() && $lab['bookingDetails']->isEmpty())
                <div class="empty">
                    <div style="font-size:36px;margin-bottom:10px">📭</div>
                    Tidak ada penggunaan pada bulan ini
                </div>
                @endif

            </div>{{-- /detail --}}
        </div>{{-- /lab-card --}}
    </div>{{-- /panel --}}
    @endforeach

    <p style="text-align:center;font-size:12px;color:#9ca3af;margin-top:8px">
        Data diperbarui otomatis · Lab Management Nuris Jember
    </p>
</div>

<script>
function switchTab(idx) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('on'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('on'));
    document.getElementById('panel-' + idx).classList.add('on');
    document.querySelectorAll('.tab')[idx].classList.add('on');
    window.scrollTo({top: 120, behavior: 'smooth'});
}
</script>

<script>
function getActiveLabName(){const t=document.querySelector('.tab.on');return t?t.textContent.trim().split('\n')[0].trim():'Rekap'}
function getPeriod(){const l=document.querySelector('.period-lbl');return l?l.textContent.trim():''}
function getActiveTableData(){
    const panel=document.querySelector('.panel.on');
    if(!panel)return{jadwal:[],booking:[]};
    const jadwal=[],booking=[];
    panel.querySelectorAll('table.tbl').forEach(tbl=>{
        const h=tbl.previousElementSibling;
        const isBook=h&&h.textContent.includes('Booking');
        const target=isBook?booking:jadwal;
        const headers=Array.from(tbl.querySelectorAll('thead th')).map(th=>th.textContent.trim());
        if(target.length===0)target.push(headers);
        tbl.querySelectorAll('tbody tr').forEach(row=>{
            target.push(Array.from(row.querySelectorAll('td')).map(td=>td.textContent.trim().replace(/\s+/g,' ')));
        });
    });
    return{jadwal,booking};
}
function getSummaryRows(){
    return Array.from(document.querySelectorAll('.sum-card')).map(c=>([
        c.querySelector('.sum-lbl')?.textContent.trim()||'',
        c.querySelector('.sum-val')?.textContent.trim()||'',
        c.querySelector('.sum-sub')?.textContent.trim()||''
    ]));
}
function exportExcel(){
    const lab=getActiveLabName(),period=getPeriod(),wb=XLSX.utils.book_new();
    const ws1=XLSX.utils.aoa_to_sheet([['REKAP PENGGUNAAN LABORATORIUM'],['Lab: '+lab,'Periode: '+period],[],['Indikator','Nilai','Keterangan'],...getSummaryRows()]);
    ws1['!cols']=[{wch:22},{wch:12},{wch:28}];
    XLSX.utils.book_append_sheet(wb,ws1,'Summary');
    const{jadwal,booking}=getActiveTableData();
    if(jadwal.length>1){const ws2=XLSX.utils.aoa_to_sheet(jadwal);ws2['!cols']=[{wch:12},{wch:14},{wch:22},{wch:14},{wch:18},{wch:12}];XLSX.utils.book_append_sheet(wb,ws2,'Jadwal Tetap');}
    if(booking.length>1){const ws3=XLSX.utils.aoa_to_sheet(booking);ws3['!cols']=[{wch:14},{wch:14},{wch:22},{wch:14},{wch:20},{wch:10}];XLSX.utils.book_append_sheet(wb,ws3,'Booking');}
    XLSX.writeFile(wb,`Rekap_${lab.replace(/\s+/g,'_')}_${period.replace(/[^a-zA-Z0-9]/g,'_')}.xlsx`);
}
function exportCSV(){
    const lab=getActiveLabName(),period=getPeriod();
    const{jadwal,booking}=getActiveTableData();
    const toCSV=rows=>rows.map(r=>r.map(c=>`"${c.replace(/"/g,'""')}"`).join(',')).join('\n');
    let csv=`REKAP PENGGUNAAN LABORATORIUM\nLab: ${lab}\nPeriode: ${period}\n\n`;
    if(jadwal.length>1)csv+='JADWAL TETAP\n'+toCSV(jadwal)+'\n\n';
    if(booking.length>1)csv+='BOOKING DISETUJUI\n'+toCSV(booking)+'\n';
    const blob=new Blob(['\uFEFF'+csv],{type:'text/csv;charset=utf-8;'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');a.href=url;
    a.download=`Rekap_${lab.replace(/\s+/g,'_')}_${period.replace(/[^a-zA-Z0-9]/g,'_')}.csv`;
    a.click();URL.revokeObjectURL(url);
}
function exportPDF(){
    const lab=getActiveLabName(),period=getPeriod(),panel=document.querySelector('.panel.on');
    if(!panel)return;
    const stats=Array.from(panel.querySelectorAll('.stat-cell')).map(c=>({val:c.querySelector('.stat-val')?.textContent.trim()||'',key:c.querySelector('.stat-key')?.textContent.trim()||''}));
    const{jadwal,booking}=getActiveTableData();
    const tblHTML=(rows,title,color)=>{
        if(rows.length<=1)return'';
        const[headers,...body]=rows;
        return`<h3 style="color:${color};font-size:13px;margin:18px 0 8px">${title}</h3><table><thead><tr>${headers.map(h=>`<th>${h}</th>`).join('')}</tr></thead><tbody>${body.map(r=>`<tr>${r.map(c=>`<td>${c}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
    };
    const html=`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Rekap ${lab}</title>
    <style>body{font-family:Arial,sans-serif;font-size:11px;color:#1A2517;padding:20px}h1{font-size:17px;margin-bottom:3px}h2{font-size:13px;color:#6b7280;font-weight:400;margin-bottom:14px}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin:12px 0 18px}.stat{background:#f8faf7;border:1px solid #e8f0e6;border-radius:8px;padding:10px;text-align:center}.stat-v{font-size:20px;font-weight:800}.stat-k{font-size:10px;color:#9ca3af;margin-top:3px}table{width:100%;border-collapse:collapse;margin-bottom:16px}th{background:#1A2517;color:#ACC8A2;padding:7px 9px;text-align:left;font-size:10px}td{padding:6px 9px;border-bottom:1px solid #e8f0e6;font-size:11px}tr:nth-child(even) td{background:#f8faf7}.footer{margin-top:16px;font-size:10px;color:#9ca3af;text-align:center}</style>
    </head><body>
    <h1>📊 Rekap Penggunaan Laboratorium</h1>
    <h2>${lab} &nbsp;·&nbsp; ${period}</h2>
    <div class="stats">${stats.map(s=>`<div class="stat"><div class="stat-v">${s.val}</div><div class="stat-k">${s.key}</div></div>`).join('')}</div>
    ${tblHTML(jadwal,'📅 Jadwal Tetap','#3d5438')}${tblHTML(booking,'📝 Booking Disetujui','#1d4ed8')}
    <div class="footer">Lab Management – Nuris Jember &nbsp;|&nbsp; Dicetak: ${new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'})}</div>
    </body></html>`;
    const win=window.open('','_blank');win.document.write(html);win.document.close();win.focus();
    setTimeout(()=>win.print(),500);
}
</script>
<div class="page-trans" id="pt"></div>
<script>
// SPA-like page transition
document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
        // Skip jika sudah di halaman yang sama
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
// Fade in on back navigation
window.addEventListener('pageshow', () => {
    document.getElementById('pt').classList.remove('go');
});
</script>

</body>
</html>