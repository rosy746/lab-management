<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Lab – Lab Management Nuris Jember</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        /* ─── RESET ─────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g9: #1A2517; --g8: #2d3d29; --g7: #3d5438;
            --acc: #ACC8A2; --acc2: #8ab87e;
            --bg: #f4f7f3; --white: #fff;
            --border: #e8f0e6; --text: #1A2517;
            --muted: #9ca3af; --sub: #6b7280;
            --shadow: 0 2px 12px rgba(0,0,0,.07);
            --r: 14px;
        }

        body { font-family: 'DM Sans', system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        /* ─── ANIMATIONS ────────────────────── */
        @keyframes slideDown { from { transform: translateY(-60px); opacity: 0; } to { transform: none; opacity: 1; } }
        @keyframes fadeUp    { from { transform: translateY(18px);  opacity: 0; } to { transform: none; opacity: 1; } }
        @keyframes panelIn   { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        @keyframes shimmer   { 0% { background-position: -600px 0; } 100% { background-position: 600px 0; } }
        @keyframes countUp   { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

        .anim-navbar { animation: slideDown .4s cubic-bezier(.16,1,.3,1) both; }
        .anim-hero   { animation: fadeUp .5s .08s cubic-bezier(.16,1,.3,1) both; }
        .anim-body   { animation: fadeUp .5s .2s  cubic-bezier(.16,1,.3,1) both; }

        /* ─── NAVBAR ────────────────────────── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: linear-gradient(135deg, var(--g9), var(--g8));
            box-shadow: 0 2px 16px rgba(0,0,0,.3);
        }
        .navbar-inner {
            max-width: 1280px; margin: auto; padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between; height: 60px;
        }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: rgba(172,200,162,.15); border: 1.5px solid rgba(172,200,162,.25);
            display: flex; align-items: center; justify-content: center;
            transition: background .18s;
        }
        .brand-icon:hover { background: rgba(172,200,162,.25); }
        .brand-name { font-family: 'Outfit', sans-serif; font-weight: 700; color: #fff; font-size: 15px; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: rgba(172,200,162,.5); }
        .nav-actions { display: flex; align-items: center; gap: 6px; }
        .nav-link {
            font-size: 13px; font-weight: 600; color: rgba(172,200,162,.6);
            text-decoration: none; padding: 6px 12px; border-radius: 8px;
            transition: color .15s, background .15s;
        }
        .nav-link:hover, .nav-link.active { color: var(--acc); background: rgba(172,200,162,.08); }
        .nav-btn {
            font-size: 12px; font-weight: 600; padding: 6px 14px;
            border-radius: 9px; border: 1px solid rgba(172,200,162,.3);
            color: var(--acc); text-decoration: none;
            transition: background .15s, transform .15s;
        }
        .nav-btn:hover { background: rgba(172,200,162,.1); transform: translateY(-1px); }

        /* ─── HERO ──────────────────────────── */
        .hero {
            background: linear-gradient(135deg, var(--g9) 0%, var(--g8) 55%, var(--g7) 100%);
            padding: 2.5rem 1.5rem 3rem; text-align: center;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px; border-radius: 50%;
            border: 1px solid rgba(172,200,162,.06); pointer-events: none;
        }
        .hero-title {
            font-family: 'Outfit', sans-serif; font-weight: 800;
            font-size: clamp(1.4rem, 4vw, 2rem);
            color: #fff; letter-spacing: -.02em; margin-bottom: 6px;
        }
        .hero-desc { font-size: 13px; color: rgba(172,200,162,.6); margin-bottom: 1.5rem; }
        .hero-stats { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .stat-card {
            background: rgba(172,200,162,.1); border: 1px solid rgba(172,200,162,.2);
            border-radius: 12px; padding: 12px 20px; text-align: center;
            transition: background .18s, transform .18s;
            animation: countUp .5s both;
        }
        .stat-card:hover { background: rgba(172,200,162,.18); transform: translateY(-2px); }
        .stat-card:nth-child(1) { animation-delay: .15s; }
        .stat-card:nth-child(2) { animation-delay: .22s; }
        .stat-card:nth-child(3) { animation-delay: .29s; }
        .stat-card:nth-child(4) { animation-delay: .36s; }
        .stat-val { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 22px; color: var(--acc); }
        .stat-val.danger { color: #f87171; }
        .stat-lbl { font-size: 10px; color: rgba(172,200,162,.55); margin-top: 2px; }

        /* ─── CONTAINER ─────────────────────── */
        .container { max-width: 1200px; margin: -20px auto 0; padding: 0 1.5rem 3rem; }

        /* ─── TOOLBAR ───────────────────────── */
        .toolbar {
            background: var(--white); border-radius: var(--r);
            padding: 12px 16px; border: 1px solid var(--border);
            margin-bottom: 14px; display: flex; flex-wrap: wrap;
            gap: 8px; align-items: center;
            box-shadow: var(--shadow);
            animation: fadeUp .5s .25s both;
        }
        .filter-inp {
            border: 1.5px solid #e5e7eb; border-radius: 9px;
            padding: 8px 12px; font-size: 13px; font-family: 'DM Sans', sans-serif;
            background: #fafcf9; outline: none; color: var(--text);
            transition: border-color .15s, box-shadow .15s;
        }
        .filter-inp:focus { border-color: var(--acc); box-shadow: 0 0 0 3px rgba(172,200,162,.12); }
        .filter-inp[type=text] { flex: 1; min-width: 180px; }

        .view-toggle {
            display: flex; gap: 3px; background: #f3f4f6;
            border-radius: 8px; padding: 3px;
        }
        .view-btn {
            background: none; border: none; border-radius: 6px;
            padding: 5px 11px; font-size: 12px; font-weight: 600;
            cursor: pointer; color: var(--sub); font-family: 'DM Sans', sans-serif;
            transition: all .15s;
        }
        .view-btn.active { background: var(--white); color: var(--text); box-shadow: 0 1px 3px rgba(0,0,0,.1); }

        .export-group { display: flex; gap: 5px; margin-left: auto; position: relative; }
        .btn-export {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 700; padding: 7px 12px;
            border-radius: 8px; border: none; cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: transform .15s, filter .15s;
        }
        .btn-export:hover { transform: translateY(-1px); filter: brightness(1.08); }
        .btn-excel { background: #16a34a; color: #fff; position: relative; }
        .btn-pdf   { background: #dc2626; color: #fff; }
        .btn-print { background: var(--g9); color: var(--acc); }

        /* Dropdown Styles */
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content {
            display: none; position: absolute; right: 0; top: 100%;
            background-color: #fff; min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000; border-radius: 8px; margin-top: 5px;
            overflow: hidden; border: 1px solid var(--border);
        }
        .dropdown-content a {
            color: var(--text); padding: 10px 14px;
            text-decoration: none; display: block;
            font-size: 12px; font-weight: 600;
            transition: background .15s;
            border-bottom: 1px solid #f3f4f6;
        }
        .dropdown-content a:last-child { border-bottom: none; }
        .dropdown-content a:hover { background-color: #f9fafb; color: #16a34a; }
        .dropdown-content.show { display: block; animation: panelIn .2s ease; }
        .dropdown-header {
            padding: 8px 14px; font-size: 10px; font-weight: 700;
            color: var(--muted); text-transform: uppercase;
            background: #f8faf7; border-bottom: 1px solid var(--border);
        }

        /* ─── TABS ──────────────────────────── */
        .tab-bar {
            display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 14px;
            animation: fadeUp .5s .3s both;
        }
        .tab-btn {
            background: var(--white); border: 1.5px solid var(--border);
            color: var(--sub); font-size: 12px; font-weight: 700;
            padding: 7px 14px; border-radius: 10px; cursor: pointer;
            transition: all .2s cubic-bezier(.16,1,.3,1);
            font-family: 'DM Sans', sans-serif; white-space: nowrap;
        }
        .tab-btn:hover:not(.active) { border-color: var(--acc); color: var(--g7); transform: translateY(-1px); }
        .tab-btn.active {
            background: linear-gradient(135deg, var(--g9), var(--g8));
            color: var(--acc); border-color: transparent;
            box-shadow: 0 4px 14px rgba(26,37,23,.25);
            transform: translateY(-1px);
        }
        .tab-count {
            background: rgba(172,200,162,.15); color: var(--sub);
            font-size: 10px; padding: 1px 6px; border-radius: 5px; margin-left: 4px;
            transition: all .2s;
        }
        .tab-btn.active .tab-count { background: rgba(172,200,162,.2); color: var(--acc); }

        /* ─── SKELETON ──────────────────────── */
        .skeleton-wrap {
            background: var(--white); border-radius: var(--r);
            border: 1px solid var(--border); overflow: hidden;
            box-shadow: var(--shadow); display: none;
        }
        .skel-head { height: 64px; background: linear-gradient(135deg, var(--g9), var(--g8)); }
        .skel-body { padding: 12px 16px; display: flex; flex-direction: column; gap: 8px; }
        .skel-row  { display: grid; grid-template-columns: 40px 2fr 1fr 1fr 2fr repeat(4,60px) 1fr; gap: 8px; }
        .skel-cell {
            height: 38px; border-radius: 7px;
            background: linear-gradient(90deg, #e8f0e6 25%, #f4f8f3 50%, #e8f0e6 75%);
            background-size: 600px 100%; animation: shimmer 1.4s infinite;
        }
        .skel-head-cell { height: 22px; }

        /* ─── PANEL ─────────────────────────── */
        .lab-panel { display: none; animation: panelIn .28s cubic-bezier(.16,1,.3,1) both; }
        .lab-panel.active { display: block; }

        .panel-wrap { background: var(--white); border-radius: var(--r); border: 1px solid var(--border); overflow: hidden; box-shadow: var(--shadow); }

        .panel-header {
            background: linear-gradient(135deg, var(--g9), var(--g8));
            padding: 14px 20px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .panel-title { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 16px; color: #fff; }
        .panel-info  { font-size: 11px; color: rgba(172,200,162,.5); margin-top: 2px; }
        .broken-badge {
            background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.25);
            border-radius: 10px; padding: 7px 12px; text-align: center;
            flex-shrink: 0;
        }
        .broken-val { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 17px; color: #f87171; }
        .broken-lbl { font-size: 10px; color: rgba(248,113,113,.65); }

        /* ─── TABLE ─────────────────────────── */
        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .tbl-wrap::-webkit-scrollbar { height: 4px; }
        .tbl-wrap::-webkit-scrollbar-thumb { background: var(--acc); border-radius: 4px; }

        .inv-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .inv-table thead tr { background: #f8faf7; border-bottom: 2px solid var(--border); }
        .inv-table th {
            padding: 10px 13px; text-align: left;
            font-size: 10px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .08em; white-space: nowrap;
        }
        .inv-table th.center { text-align: center; }
        .inv-table tbody tr {
            border-top: 1px solid #f5f5f5;
            transition: background .12s, transform .12s;
        }
        .inv-table tbody tr:hover { background: #fafcf9; }
        .inv-table td { padding: 11px 13px; vertical-align: middle; }
        .no-col { color: var(--muted); font-size: 12px; width: 40px; }
        .item-name { font-weight: 700; color: var(--text); }
        .item-brand { color: var(--sub); }
        .item-specs { font-size: 12px; color: var(--muted); font-style: italic; }
        .qty-center { text-align: center; }
        .qty-total  { font-weight: 700; }
        .qty-good   { color: #16a34a; font-weight: 700; text-align: center; }
        .qty-broken { color: #dc2626; font-weight: 700; text-align: center; }
        .qty-backup { color: #0369a1; font-weight: 700; text-align: center; }
        .qty-zero   { color: var(--muted); text-align: center; }

        /* ─── PROGRESS BAR ──────────────────── */
        .progress-wrap { width: 80px; height: 6px; background: #fee2e2; border-radius: 999px; overflow: hidden; margin-top: 4px; }
        .progress-bar  { height: 100%; background: #16a34a; border-radius: 999px; transition: width .6s cubic-bezier(.16,1,.3,1); }
        .progress-text { font-size: 9px; font-weight: 700; color: var(--muted); margin-top: 2px; }

        /* ─── HIGHLIGHT ─────────────────────── */
        mark.search-match { background: #fef08a; color: #854d0e; padding: 0 2px; border-radius: 2px; box-shadow: 0 0 0 1px #fde047; }
        
        /* ─── COUNTDOWN ─────────────────────── */
        .cd-unit { display: inline-flex; flex-direction: column; align-items: center; background: rgba(255,255,255,0.1); padding: 4px 6px; border-radius: 6px; min-width: 32px; }
        .cd-val { font-size: 11px; font-weight: 800; line-height: 1; }
        .cd-lbl { font-size: 7px; text-transform: uppercase; margin-top: 2px; opacity: 0.7; }

        /* ─── CATEGORY ICONS ────────────────── */
        .cat-icon { width: 16px; height: 16px; margin-right: 6px; vertical-align: middle; opacity: .7; }

        /* ─── CARD VIEW ─────────────────────── */
        .cards-grid {
            display: none; padding: 16px;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;
        }
        .cards-grid.show { display: grid; }
        .inv-card {
            border: 1.5px solid var(--border); border-radius: 12px;
            padding: 14px; background: #fafcf9;
            transition: border-color .18s, transform .18s, box-shadow .18s;
            position: relative; overflow: hidden;
        }
        .inv-card::before {
            content: ''; position: absolute; top: 0; left: -70%; width: 45%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.5), transparent);
            transform: skewX(-18deg); transition: left .4s ease; pointer-events: none;
        }
        .inv-card:hover::before { left: 130%; }
        .inv-card:hover {
            border-color: var(--acc);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(26,37,23,.1);
        }
        .card-top    { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; gap: 4px; }
        .card-name   { font-weight: 700; font-size: 13px; margin-bottom: 2px; }
        .card-brand  { font-size: 11px; color: var(--sub); margin-bottom: 5px; }
        .card-specs  { font-size: 11px; color: var(--muted); font-style: italic; margin-bottom: 8px; }
        .card-qtys   { display: flex; gap: 5px; flex-wrap: wrap; }

        /* ─── BADGES ────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 6px;
            font-size: 10px; font-weight: 700;
        }
        .cat-computer   { background: #dbeafe; color: #1d4ed8; }
        .cat-peripheral { background: #f3e8ff; color: #7c3aed; }
        .cat-network    { background: #dcfce7; color: #15803d; }
        .cat-furniture  { background: #fef3c7; color: #92400e; }
        .cat-software   { background: #e0f2fe; color: #0369a1; }
        .cat-other      { background: #f3f4f6; color: #374151; }
        .cond-excellent, .cond-good { background: #dcfce7; color: #15803d; }
        .cond-fair   { background: #fef3c7; color: #92400e; }
        .cond-poor   { background: #fee2e2; color: #dc2626; }
        .cond-broken { background: #f3f4f6; color: #6b7280; }
        .badge-qty   { background: #f0f4ee; color: var(--text); }
        .badge-good  { background: #dcfce7; color: #15803d; }
        .badge-broken { background: #fee2e2; color: #dc2626; }

        /* ─── EMPTY ─────────────────────────── */
        .empty-row td { text-align: center; padding: 48px; color: var(--muted); font-size: 14px; }

        /* ─── FOOTER ────────────────────────── */
        footer { text-align: center; padding: 18px; font-size: 12px; color: var(--muted); }

        /* ─── PRINT ─────────────────────────── */
        @media print {
            .navbar, .toolbar, .tab-bar, .export-group, .view-toggle, .skeleton-wrap { display: none !important; }
            .lab-panel { display: block !important; }
            .panel-header { background: var(--g9) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            body { background: #fff; }
            .container { padding: 0; margin-top: 0; }
        }

        /* ─── RESPONSIVE ────────────────────── */
        @media (max-width: 768px) {
            .export-group { margin-left: 0; }
            .inv-table th:nth-child(5), .inv-table td:nth-child(5) { display: none; }
            .brand-sub { display: none; }
        }
        @media (max-width: 480px) {
            .hero { padding: 1.5rem 1rem 2.5rem; }
            .container { padding: 0 1rem 2rem; }
            .inv-table th:nth-child(4), .inv-table td:nth-child(4),
            .inv-table th:nth-child(11), .inv-table td:nth-child(11) { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
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
</style>
</head>
<body>

{{-- ═══ NAVBAR ═══ --}}
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
            <a href="{{ route('inventory.public') }}" class="pub-link on">Inventaris</a>
            <a href="/rekap" class="pub-link">Rekap</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>
</nav>

{{-- ═══ HERO ═══ --}}
<div class="hero anim-hero">
    <h1 class="hero-title">Inventaris Laboratorium</h1>
    <p class="hero-desc">Data perangkat dan perlengkapan seluruh laboratorium komputer Nuris Jember</p>
    <div class="hero-stats">
        <div class="stat-card">
            <div class="stat-val">{{ $resources->count() }}</div>
            <div class="stat-lbl">Laboratorium</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $totalItems }}</div>
            <div class="stat-lbl">Jenis Barang</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $totalUnits }}</div>
            <div class="stat-lbl">Total Unit</div>
        </div>
        <div class="stat-card">
            <div class="stat-val danger">{{ $totalBroken }}</div>
            <div class="stat-lbl">Unit Rusak</div>
        </div>
    </div>
</div>

{{-- ═══ MAIN ═══ --}}
<div class="container anim-body">

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <input type="text" id="search-inp" class="filter-inp"
               placeholder="🔍 Cari nama barang, merk, spesifikasi..."
               oninput="filterTable()">
        <select id="cat-filter" class="filter-inp" onchange="filterTable()">
            <option value="">Semua Kategori</option>
            <option value="computer">💻 Komputer</option>
            <option value="peripheral">🖱 Peripheral</option>
            <option value="network">🌐 Jaringan</option>
            <option value="furniture">🪑 Furniture</option>
            <option value="software">💿 Software</option>
            <option value="other">📦 Lainnya</option>
        </select>
        <select id="cond-filter" class="filter-inp" onchange="filterTable()">
            <option value="">Semua Kondisi</option>
            <option value="excellent">✨ Sangat Baik</option>
            <option value="good">✓ Baik</option>
            <option value="fair">⚠ Cukup</option>
            <option value="poor">⚠ Buruk</option>
            <option value="broken">✗ Rusak</option>
        </select>
        <div class="view-toggle">
            <button class="view-btn active" id="btn-table" onclick="setView('table')">☰ Tabel</button>
            <button class="view-btn" id="btn-card"  onclick="setView('card')">⊞ Kartu</button>
        </div>
        <div class="export-group">
            <div class="dropdown">
                <button class="btn-export btn-excel" onclick="toggleExcelDropdown(event)">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 17l1.5-2.5L8.5 12H10l.75 1.5L11.5 12H13l-1.5 2.5L13 17h-1.5l-.75-1.5-.75 1.5H8.5z"/></svg>
                    Excel
                </button>
                <div id="excelDropdown" class="dropdown-content">
                    <div class="dropdown-header">Pilihan Export Excel</div>
                    <a href="javascript:void(0)" onclick="exportExcel()">📗 Lab Aktif Saja</a>
                    <a href="javascript:void(0)" onclick="exportAllExcel()" style="color: #16a34a; font-weight: 700;">📊 Semua Lab (Multi-Sheet)</a>
                </div>
            </div>
            <button class="btn-export btn-pdf" onclick="exportPDF()">
                <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/></svg>
                PDF
            </button>
            <button class="btn-export btn-print" onclick="window.print()">
                <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6v-8z"/></svg>
                Print
            </button>
        </div>
    </div>

    {{-- TABS --}}
    <div class="tab-bar">
        @foreach($resources as $i => $lab)
        @php $cnt = $inventories->where('resource_id', $lab->id)->count(); @endphp
        <button class="tab-btn {{ $i === 0 ? 'active' : '' }}"
                onclick="switchLab({{ $lab->id }}, this)">
            {{ $lab->name }}
            @if($cnt > 0)
            <span class="tab-count">{{ $cnt }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- SKELETON --}}
    <div class="skeleton-wrap" id="skeleton">
        <div class="skel-head"></div>
        <div class="skel-body">
            <div class="skel-row">
                @for($c=0;$c<10;$c++)<div class="skel-cell skel-head-cell" style="animation-delay:{{ $c*30 }}ms"></div>@endfor
            </div>
            @for($r=0;$r<6;$r++)
            <div class="skel-row">
                @for($c=0;$c<10;$c++)<div class="skel-cell" style="animation-delay:{{ ($r*10+$c)*18 }}ms"></div>@endfor
            </div>
            @endfor
        </div>
    </div>

    {{-- PANELS --}}
    @php
    $catLabels = ['computer'=>'Komputer','peripheral'=>'Peripheral','network'=>'Jaringan','furniture'=>'Furniture','software'=>'Software','other'=>'Lainnya'];
    $catIcons  = ['computer'=>'💻','peripheral'=>'🖱','network'=>'🌐','furniture'=>'🪑','software'=>'💿','other'=>'📦'];
    $condLabels = ['excellent'=>'Sangat Baik','good'=>'Baik','fair'=>'Cukup','poor'=>'Buruk','broken'=>'Rusak'];
    @endphp

    @foreach($resources as $i => $lab)
    @php
        $labItems    = $inventories->where('resource_id', $lab->id)->values();
        $brokenCount = $labItems->sum('quantity_broken');
    @endphp
    <div class="lab-panel {{ $i === 0 ? 'active' : '' }}" id="lab-{{ $lab->id }}">
        <div class="panel-wrap">

            {{-- Header --}}
            <div class="panel-header">
                <div>
                    <div class="panel-title">{{ $lab->name }}</div>
                    <div class="panel-info">
                        {{ $lab->building ?? '-' }}
                        @if($lab->capacity) · Kapasitas {{ $lab->capacity }} unit @endif
                        · {{ $labItems->count() }} jenis · {{ $labItems->sum('quantity') }} total unit
                    </div>
                </div>
                @if($brokenCount > 0)
                <div class="broken-badge">
                    <div class="broken-val">{{ $brokenCount }}</div>
                    <div class="broken-lbl">unit rusak</div>
                </div>
                @endif
            </div>

            {{-- TABLE VIEW --}}
            <div class="tbl-wrap" id="table-{{ $lab->id }}">
                <table class="inv-table" id="tbl-{{ $lab->id }}">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Merk / Model</th>
                            <th>Spesifikasi</th>
                            <th class="center">Total</th>
                            <th class="center">Baik</th>
                            <th class="center">Rusak</th>
                            <th class="center">Cadangan</th>
                            <th>Kondisi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-{{ $lab->id }}">
                        @forelse($labItems as $idx => $item)
                        <tr data-name="{{ strtolower($item->item_name) }}"
                            data-brand="{{ strtolower($item->brand ?? '') }}"
                            data-specs="{{ strtolower($item->specifications ?? '') }}"
                            data-category="{{ $item->category }}"
                            data-condition="{{ $item->condition }}">
                            <td class="no-col">{{ $idx + 1 }}</td>
                            <td class="item-name">
                                <div style="display:flex;align-items:center">
                                    <span style="font-size:16px;margin-right:8px">{{ $catIcons[$item->category] ?? '' }}</span>
                                    <div>
                                        <div class="search-target">{{ ucwords($item->item_name) }}</div>
                                        @if($item->quantity_broken > 0)
                                        <div class="progress-wrap">
                                            @php $goodPerc = ($item->quantity_good / $item->quantity) * 100; @endphp
                                            <div class="progress-bar" style="width: {{ $goodPerc }}%"></div>
                                        </div>
                                        <div class="progress-text">{{ round($goodPerc) }}% Baik</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge cat-{{ $item->category }}">
                                    {{ $catLabels[$item->category] ?? $item->category }}
                                </span>
                            </td>
                            <td class="item-brand">
                                <div class="search-target">{{ $item->brand ? ucwords($item->brand) : '' }}</div>
                                <div class="search-target" style="font-size:10px;color:var(--muted)">{{ $item->model ? $item->model : '' }}</div>
                                @if(!$item->brand && !$item->model)<span style="color:#e5e7eb">—</span>@endif
                            </td>
                            <td class="item-specs">
                                <div class="search-target">{{ $item->specifications ?: '—' }}</div>
                            </td>
                            <td class="qty-center qty-total">{{ $item->quantity }}</td>
                            <td class="qty-good">{{ $item->quantity_good }}</td>
                            <td class="{{ $item->quantity_broken > 0 ? 'qty-broken' : 'qty-zero' }}">{{ $item->quantity_broken }}</td>
                            <td class="{{ $item->quantity_backup > 0 ? 'qty-backup' : 'qty-zero' }}">{{ $item->quantity_backup }}</td>
                            <td><span class="badge cond-{{ $item->condition }}">{{ $condLabels[$item->condition] ?? $item->condition }}</span></td>
                            <td class="item-specs">{{ $item->notes ?: '—' }}</td>
                        </tr>
                        @empty
                        <tr class="empty-row"><td colspan="11">📦 Belum ada data inventaris untuk lab ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARD VIEW --}}
            <div class="cards-grid" id="cards-{{ $lab->id }}">
                @foreach($labItems as $item)
                <div class="inv-card"
                     data-name="{{ strtolower($item->item_name) }}"
                     data-brand="{{ strtolower($item->brand ?? '') }}"
                     data-specs="{{ strtolower($item->specifications ?? '') }}"
                     data-category="{{ $item->category }}"
                     data-condition="{{ $item->condition }}">
                    <div class="card-top">
                        <span class="badge cat-{{ $item->category }}">{{ $catIcons[$item->category] ?? '' }} {{ $catLabels[$item->category] ?? $item->category }}</span>
                        <span class="badge cond-{{ $item->condition }}">{{ $condLabels[$item->condition] ?? $item->condition }}</span>
                    </div>
                    <div class="card-name">{{ ucwords($item->item_name) }}</div>
                    @if($item->brand || $item->model)
                    <div class="card-brand">{{ $item->brand }} {{ $item->model ? '/ '.$item->model : '' }}</div>
                    @endif
                    @if($item->specifications)
                    <div class="card-specs">{{ $item->specifications }}</div>
                    @endif
                    <div class="card-qtys">
                        <span class="badge badge-qty">Total: {{ $item->quantity }}</span>
                        <span class="badge badge-good">Baik: {{ $item->quantity_good }}</span>
                        @if($item->quantity_broken > 0)
                        <span class="badge badge-broken">Rusak: {{ $item->quantity_broken }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
    @endforeach

</div>

<footer>© {{ date('Y') }} Lab Management – Nuris Jember · Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</footer>

<script>
let currentView = 'table';
let currentLab  = {{ $resources->first()->id ?? 0 }};

// ─── TAB SWITCH WITH SKELETON ────────────────────────────
function switchLab(id, btn) {
    if (currentLab === id) return;
    currentLab = id;

    // Update tab styles
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Show skeleton, hide panels
    document.querySelectorAll('.lab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('skeleton').style.display = 'block';

    setTimeout(() => {
        document.getElementById('skeleton').style.display = 'none';
        const panel = document.getElementById('lab-' + id);
        panel.classList.add('active');
        // Re-trigger animation
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
        filterTable();
    }, 360);
}

// ─── VIEW TOGGLE ─────────────────────────────────────────
function setView(v) {
    currentView = v;
    document.getElementById('btn-table').classList.toggle('active', v === 'table');
    document.getElementById('btn-card').classList.toggle('active',  v === 'card');
    document.querySelectorAll('[id^="table-"]').forEach(el => el.style.display = v === 'table' ? '' : 'none');
    document.querySelectorAll('[id^="cards-"]').forEach(el => el.classList.toggle('show', v === 'card'));
    filterTable();
}

// ─── DROPDOWN ────────────────────────────────────────────
function toggleExcelDropdown(event) {
    event.stopPropagation();
    document.getElementById('excelDropdown').classList.toggle('show');
}

window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('excelDropdown');
    if (dropdown && dropdown.classList.contains('show') && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// ─── FILTER ──────────────────────────────────────────────
function filterTable() {
    const search = document.getElementById('search-inp').value.toLowerCase();
    const cat    = document.getElementById('cat-filter').value;
    const cond   = document.getElementById('cond-filter').value;
    const panel  = document.querySelector('.lab-panel.active');
    if (!panel) return;

    // Clear previous highlights
    panel.querySelectorAll('.search-target').forEach(el => {
        el.innerHTML = el.innerText;
    });

    // Table rows
    let n = 1;
    panel.querySelectorAll('tbody tr:not(.empty-row)').forEach(row => {
        const match = (!search || row.dataset.name.includes(search) || row.dataset.brand.includes(search) || row.dataset.specs.includes(search))
                   && (!cat   || row.dataset.category  === cat)
                   && (!cond  || row.dataset.condition === cond);
        row.style.display = match ? '' : 'none';
        
        if (match) {
            row.querySelector('.no-col').textContent = n++;
            // Apply highlighting if search exists
            if (search && search.length > 1) {
                row.querySelectorAll('.search-target').forEach(el => {
                    const text = el.innerText;
                    const regex = new RegExp(`(${search})`, 'gi');
                    el.innerHTML = text.replace(regex, '<mark class="search-match">$1</mark>');
                });
            }
        }
    });

    // Cards
    panel.querySelectorAll('.inv-card').forEach(card => {
        const match = (!search || card.dataset.name.includes(search) || card.dataset.brand.includes(search) || card.dataset.specs.includes(search))
                   && (!cat   || card.dataset.category  === cat)
                   && (!cond  || card.dataset.condition === cond);
        card.style.display = match ? '' : 'none';
    });
}

// ─── EXCEL EXPORT ─────────────────────────────────────────
function exportExcel() {
    const labName = document.querySelector('.tab-btn.active').textContent.trim().split('\n')[0].trim();
    const rows = [['No','Nama Barang','Kategori','Merk','Model','Spesifikasi','Total','Baik','Rusak','Cadangan','Catatan']];
    document.querySelector('.lab-panel.active tbody').querySelectorAll('tr:not(.empty-row)').forEach(row => {
        if (row.style.display === 'none') return;
        const c = row.querySelectorAll('td');
        
        // Extract clean text (exclude emojis and progress bar)
        const itemName = c[1].querySelector('.search-target')?.innerText || '';
        const category = c[2].querySelector('.badge')?.innerText || '';
        const brandDivs = c[3].querySelectorAll('.search-target');
        const brand = brandDivs[0]?.innerText || '';
        const model = brandDivs[1]?.innerText || '';
        const specs = c[4].querySelector('.search-target')?.innerText || '';

        rows.push([
            parseInt(c[0].textContent) || '',
            itemName.trim(), 
            category.trim(),
            brand.trim(),
            model.trim(),
            specs.trim(),
            parseInt(c[5].textContent)||0, parseInt(c[6].textContent)||0,
            parseInt(c[7].textContent)||0, parseInt(c[8].textContent)||0,
            c[10].textContent.trim(), // Catatan (Kondisi di c[9] dihapus)
        ]);
    });
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!cols'] = [5,25,14,16,14,20,7,7,7,9,20].map(w => ({wch:w}));
    XLSX.utils.book_append_sheet(wb, ws, labName.substring(0,31));
    XLSX.writeFile(wb, `Inventaris_${labName.replace(/\s+/g,'_')}_${new Date().toISOString().slice(0,10)}.xlsx`);
}

function exportAllExcel() {
    const wb = XLSX.utils.book_new();
    const panels = document.querySelectorAll('.lab-panel');
    
    panels.forEach(panel => {
        const labName = panel.querySelector('.panel-title').textContent.trim();
        const rows = [['No','Nama Barang','Kategori','Merk','Model','Spesifikasi','Total','Baik','Rusak','Cadangan','Catatan']];
        
        panel.querySelectorAll('tbody tr:not(.empty-row)').forEach((row, idx) => {
            const c = row.querySelectorAll('td');
            
            // Extract clean text
            const itemName = c[1].querySelector('.search-target')?.innerText || '';
            const category = c[2].querySelector('.badge')?.innerText || '';
            const brandDivs = c[3].querySelectorAll('.search-target');
            const brand = brandDivs[0]?.innerText || '';
            const model = brandDivs[1]?.innerText || '';
            const specs = c[4].querySelector('.search-target')?.innerText || '';

            rows.push([
                idx + 1,
                itemName.trim(), 
                category.trim(),
                brand.trim(),
                model.trim(),
                specs.trim(),
                parseInt(c[5].textContent)||0, parseInt(c[6].textContent)||0,
                parseInt(c[7].textContent)||0, parseInt(c[8].textContent)||0,
                c[10].textContent.trim(), // Catatan (Kondisi di c[9] dihapus)
            ]);
        });
        
        if (rows.length > 1) {
            const ws = XLSX.utils.aoa_to_sheet(rows);
            ws['!cols'] = [5,25,14,16,14,20,7,7,7,9,20].map(w => ({wch:w}));
            XLSX.utils.book_append_sheet(wb, ws, labName.substring(0,31));
        }
    });

    XLSX.writeFile(wb, `Inventaris_Semua_Lab_${new Date().toISOString().slice(0,10)}.xlsx`);
}

// ─── PDF EXPORT ───────────────────────────────────────────
function exportPDF() {
    const labName = document.querySelector('.tab-btn.active').textContent.trim().split('\n')[0].trim();
    const rows = [];
    document.querySelector('.lab-panel.active tbody').querySelectorAll('tr:not(.empty-row)').forEach(row => {
        if (row.style.display === 'none') return;
        const c = row.querySelectorAll('td');
        rows.push([c[0],c[1],c[2],c[3],c[4],c[5],c[6],c[7],c[8],c[9]].map(x => x.textContent.trim()));
    });
    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Inventaris ${labName}</title>
    <style>body{font-family:Arial,sans-serif;font-size:11px;color:#1A2517;padding:20px}
    h2{font-size:16px;margin-bottom:4px}p{font-size:11px;color:#666;margin-bottom:14px}
    table{width:100%;border-collapse:collapse}
    th{background:#1A2517;color:#ACC8A2;padding:7px 8px;text-align:left;font-size:10px}
    td{padding:6px 8px;border-bottom:1px solid #e5e7eb;font-size:11px}
    tr:nth-child(even) td{background:#f8faf7}
    .footer{margin-top:16px;font-size:10px;color:#999;text-align:center}</style></head><body>
    <h2>Inventaris ${labName}</h2>
    <p>Lab Management – Nuris Jember &nbsp;|&nbsp; Dicetak: ${new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'})}</p>
    <table><thead><tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th>Merk/Model</th><th>Spesifikasi</th><th>Total</th><th>Baik</th><th>Rusak</th><th>Cadangan</th><th>Kondisi</th></tr></thead>
    <tbody>${rows.map(r=>'<tr>'+r.map(c=>'<td>'+c+'</td>').join('')+'</tr>').join('')}</tbody></table>
    <div class="footer">Total ${rows.length} jenis barang</div>

</body></html>`;
    const win = window.open('','_blank');
    win.document.write(html); win.document.close(); win.focus();
    setTimeout(() => win.print(), 500);
}
</script>
</body>
</html>