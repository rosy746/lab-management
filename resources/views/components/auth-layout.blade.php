<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ─── ANIMATIONS ─────────────────────── */
        @keyframes slideInLeft {
            from { transform: translateX(-40px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes slideInRight {
            from { transform: translateX(40px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes fadeUp {
            from { transform: translateY(16px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes blink {
            0%, 100% { opacity: 1; box-shadow: 0 0 8px rgba(172,200,162,.9); }
            50%       { opacity: .35; box-shadow: 0 0 3px rgba(172,200,162,.3); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-8px); }
        }
        @keyframes shimmerLine {
            0%   { transform: translateX(-100%) skewX(-15deg); }
            100% { transform: translateX(300%)  skewX(-15deg); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ─── BASE ───────────────────────────── */
        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #1A2517;
        }

        /* ─── LEFT PANEL ─────────────────────── */
        .left-panel {
            flex: 1.1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px 52px;
            overflow: hidden;
            background: linear-gradient(155deg, #1A2517 0%, #243320 45%, #2d3d29 100%);
            animation: slideInLeft .6s cubic-bezier(.16,1,.3,1) both;
        }

        /* Grid background */
        .grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(172,200,162,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(172,200,162,.04) 1px, transparent 1px);
            background-size: 52px 52px;
        }

        /* Glow orbs */
        .glow-top {
            position: absolute; top: -120px; right: -80px;
            width: 480px; height: 480px; border-radius: 50%;
            background: radial-gradient(circle, rgba(172,200,162,.11) 0%, transparent 65%);
            animation: float 8s ease-in-out infinite;
        }
        .glow-bottom {
            position: absolute; bottom: -100px; left: -60px;
            width: 380px; height: 380px; border-radius: 50%;
            background: radial-gradient(circle, rgba(172,200,162,.07) 0%, transparent 65%);
            animation: float 11s ease-in-out infinite reverse;
        }

        /* Brand */
        .brand {
            position: relative; z-index: 1;
            display: flex; align-items: center; gap: 12px;
            animation: fadeUp .5s .1s both;
        }
        .brand-icon {
            width: 42px; height: 42px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(172,200,162,.12);
            border: 1.5px solid rgba(172,200,162,.25);
            transition: background .2s, transform .2s;
        }
        .brand-icon:hover { background: rgba(172,200,162,.22); transform: rotate(-4deg) scale(1.05); }

        /* Hero content */
        .hero-content {
            position: relative; z-index: 1;
            animation: fadeUp .6s .2s both;
        }

        .live-badge {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 100px;
            background: rgba(172,200,162,.08);
            border: 1px solid rgba(172,200,162,.18);
            color: rgba(172,200,162,.75);
            font-size: 11px; font-weight: 600;
            letter-spacing: .07em; text-transform: uppercase;
            margin-bottom: 24px;
            transition: background .2s, border-color .2s;
        }
        .live-badge:hover { background: rgba(172,200,162,.14); border-color: rgba(172,200,162,.3); }
        .live-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #ACC8A2;
            animation: blink 2s infinite;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: clamp(28px, 3vw, 46px);
            line-height: 1.08;
            color: #fff;
            letter-spacing: -.025em;
            margin-bottom: 18px;
        }
        .hero-title .accent {
            color: #ACC8A2;
            position: relative;
            display: inline-block;
        }
        /* Shimmer on accent word */
        .hero-title .accent::after {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 35%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.25), transparent);
            animation: shimmerLine 3.5s ease-in-out infinite 1s;
        }

        .hero-desc {
            font-size: 14px;
            color: rgba(172,200,162,.5);
            line-height: 1.7;
            max-width: 360px;
            animation: fadeUp .6s .35s both;
        }

        /* Stat cards */
        .stats-row {
            position: relative; z-index: 1;
            display: flex; gap: 14px;
            animation: fadeUp .6s .45s both;
        }
        .stat-card {
            flex: 1; padding: 16px 18px;
            border-radius: 14px;
            background: rgba(172,200,162,.05);
            border: 1px solid rgba(172,200,162,.10);
            transition: background .2s, border-color .2s, transform .2s;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: -70%; width: 40%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(172,200,162,.08), transparent);
            transform: skewX(-18deg);
            transition: left .5s ease;
        }
        .stat-card:hover::before { left: 130%; }
        .stat-card:hover {
            background: rgba(172,200,162,.09);
            border-color: rgba(172,200,162,.2);
            transform: translateY(-2px);
        }
        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-weight: 700; font-size: 26px;
            color: #ACC8A2; line-height: 1; margin-bottom: 5px;
        }
        .stat-label {
            font-size: 11px;
            color: rgba(172,200,162,.4);
            font-weight: 500;
        }

        /* ─── RIGHT PANEL ────────────────────── */
        .right-panel {
            width: 440px; flex-shrink: 0;
            display: flex; flex-direction: column;
            justify-content: center;
            padding: 52px 44px;
            background: #fff;
            position: relative;
            overflow-y: auto;
            animation: slideInRight .6s cubic-bezier(.16,1,.3,1) both;
        }
        /* Accent line */
        .right-panel::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 3px; height: 100%;
            background: linear-gradient(180deg, transparent, #ACC8A2, transparent);
            opacity: .4;
        }

        /* Back link */
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 600;
            color: rgba(172,200,162,.5); text-decoration: none;
            transition: color .15s, gap .15s;
            margin-bottom: 40px;
        }
        .back-link:hover { color: #ACC8A2; gap: 9px; }

        /* Header */
        .login-header {
            margin-bottom: 32px;
            animation: fadeUp .5s .25s both;
        }
        .login-eyebrow {
            font-size: 11px; font-weight: 700;
            color: #ACC8A2; letter-spacing: .1em;
            text-transform: uppercase; margin-bottom: 8px;
        }
        .login-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800; font-size: 28px;
            color: #1A2517; line-height: 1.15;
            letter-spacing: -.02em;
        }
        .login-sub {
            font-size: 13px; color: #9ca3af;
            margin-top: 6px; line-height: 1.5;
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
            animation: fadeUp .5s both;
        }
        .form-group:nth-child(1) { animation-delay: .3s; }
        .form-group:nth-child(2) { animation-delay: .38s; }

        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            color: #4b5563; text-transform: uppercase;
            letter-spacing: .07em; margin-bottom: 7px;
        }
        .form-input {
            width: 100%;
            border: 1.5px solid #e5e7eb; border-radius: 12px;
            padding: 11px 16px;
            font-size: 14px; font-family: 'DM Sans', sans-serif;
            color: #1A2517; background: #f9fafb;
            outline: none;
            transition: border-color .18s, box-shadow .18s, background .18s, transform .15s;
        }
        .form-input:focus {
            border-color: #ACC8A2;
            box-shadow: 0 0 0 3px rgba(172,200,162,.18);
            background: #fff;
            transform: translateY(-1px);
        }
        .form-input::placeholder { color: #c0c7d0; }

        .input-wrap { position: relative; }
        .toggle-pass {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; color: #c0c7d0;
            display: flex; align-items: center;
            padding: 4px; border-radius: 6px;
            transition: color .15s, background .15s;
        }
        .toggle-pass:hover { color: #ACC8A2; background: rgba(172,200,162,.08); }

        /* Error box */
        .error-box {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #dc2626; padding: 11px 14px;
            border-radius: 10px; font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
            animation: fadeUp .3s both;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #1A2517, #2d3d29);
            color: #ACC8A2; font-weight: 700; font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            padding: 13px; border: none; border-radius: 12px;
            cursor: pointer; letter-spacing: .01em;
            box-shadow: 0 4px 18px rgba(26,37,23,.25);
            transition: transform .18s, box-shadow .18s, filter .18s;
            margin-top: 8px;
            position: relative; overflow: hidden;
            animation: fadeUp .5s .45s both;
        }
        .btn-login::after {
            content: '';
            position: absolute; top: 0; left: -80%; width: 50%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(172,200,162,.15), transparent);
            transform: skewX(-18deg);
            transition: left .5s ease;
        }
        .btn-login:hover::after { left: 150%; }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(26,37,23,.35);
            filter: brightness(1.08);
        }
        .btn-login:active { transform: translateY(0); }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none; opacity: .85;
            color: rgba(172,200,162,.7);
        }
        .btn-login.loading::before {
            content: '';
            display: inline-block;
            width: 14px; height: 14px;
            border: 2px solid rgba(172,200,162,.3);
            border-top-color: #ACC8A2;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        .footer-text {
            margin-top: 28px; font-size: 11px;
            color: #d1d5db; text-align: center;
        }

        /* ─── MOBILE ─────────────────────────── */
        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { flex: none; padding: 32px 28px; min-height: 200px; animation: fadeUp .5s both; }
            .stats-row  { display: none; }
            .hero-title { font-size: 24px; margin-bottom: 10px; }
            .hero-desc  { font-size: 13px; }
            .right-panel { width: 100%; padding: 36px 28px; animation: fadeUp .5s .15s both; }
            .back-link  { margin-bottom: 28px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

    {{-- ═══ LEFT PANEL ═══ --}}
    <div class="left-panel">
        <div class="grid-bg"></div>
        <div class="glow-top"></div>
        <div class="glow-bottom"></div>

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-icon">
                <svg style="width:22px;height:22px;color:#ACC8A2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p style="font-family:Outfit,sans-serif;font-weight:700;color:#fff;font-size:14px;line-height:1.2">Lab Management</p>
                <p style="font-size:11px;color:rgba(172,200,162,.4)">Nuris Jember</p>
            </div>
        </div>

        {{-- Hero --}}
        <div class="hero-content">
            <div class="live-badge">
                <span class="live-dot"></span>
                Sistem Aktif
            </div>
            <h1 class="hero-title">
                Kelola Lab<br>
                dengan <span class="accent">Efisien</span><br>
                & Terstruktur
            </h1>
            <p class="hero-desc">
                Platform manajemen laboratorium komputer terpadu — jadwal, booking, inventaris, dan pengadaan dalam satu sistem.
            </p>
        </div>

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number">{{ \App\Models\Resource::count() }}</div>
                <div class="stat-label">Lab Tersedia</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ \App\Models\Booking::where('status','pending')->count() }}</div>
                <div class="stat-label">Booking Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ \App\Models\Booking::whereDate('created_at', today())->count() }}</div>
                <div class="stat-label">Booking Hari Ini</div>
            </div>
        </div>
    </div>

    {{-- ═══ RIGHT PANEL ═══ --}}
    <div class="right-panel">
        <a href="/" class="back-link">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Jadwal
        </a>

        <div class="login-header">
            <p class="login-eyebrow">Panel Admin</p>
            <h2 class="login-title">Selamat<br>Datang Kembali</h2>
            <p class="login-sub">Masuk untuk mengelola laboratorium</p>
        </div>

        {{ $slot }}

        <p class="footer-text">&copy; {{ date('Y') }} Lab Management System &middot; Nuris Jember</p>
    </div>

    @livewireScripts

    <script>
        // Loading state on submit
        document.querySelector('form')?.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-login');
            if (btn) {
                btn.classList.add('loading');
                btn.textContent = 'Memproses...';
            }
        });
    </script>
</body>
</html>