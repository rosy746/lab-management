@props(['stats' => ['labs' => 0, 'pending' => 0, 'today' => 0]])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Login</title>

    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    @vite(['resources/css/app.css'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @keyframes fadeUp {
            from { transform: translateY(16px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes slideLeft {
            from { transform: translateX(-32px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes slideRight {
            from { transform: translateX(32px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: .2; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes shimmer {
            0%   { left: -60%; }
            100% { left: 130%; }
        }

        /* ── BASE ── */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #080c08;
        }

        /* ══════════════════════════════════
           LEFT PANEL
        ══════════════════════════════════ */
        .left-panel {
            flex: 1.15;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            overflow: hidden;
            background: #090e09;
            border-right: 1px solid #1a271a;
            animation: slideLeft .6s cubic-bezier(.16,1,.3,1) both;
        }

        .grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(172,200,162,.032) 1px, transparent 1px),
                linear-gradient(90deg, rgba(172,200,162,.032) 1px, transparent 1px);
            background-size: 54px 54px;
            pointer-events: none;
        }

        .glow-tl {
            position: absolute; top: -130px; left: -80px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(172,200,162,.09) 0%, transparent 65%);
            animation: float 9s ease-in-out infinite;
            pointer-events: none;
        }
        .glow-br {
            position: absolute; bottom: -100px; right: -60px;
            width: 380px; height: 380px; border-radius: 50%;
            background: radial-gradient(circle, rgba(172,200,162,.05) 0%, transparent 65%);
            animation: float 13s ease-in-out infinite reverse;
            pointer-events: none;
        }

        /* brand */
        .brand {
            position: relative; z-index: 1;
            display: flex; align-items: center; gap: 12px;
            animation: fadeUp .5s .05s both;
        }
        .brand-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: rgba(172,200,162,.08);
            border: 1px solid rgba(172,200,162,.18);
            display: flex; align-items: center; justify-content: center;
            transition: background .2s, transform .2s;
        }
        .brand-icon:hover {
            background: rgba(172,200,162,.15);
            transform: rotate(-4deg) scale(1.05);
        }
        .brand-icon svg { width: 20px; height: 20px; stroke: #ACC8A2; fill: none; }
        .brand-name {
            font-weight: 700; font-size: 14px;
            color: #e8f0e8; letter-spacing: -.01em;
            line-height: 1.2;
        }
        .brand-sub {
            font-size: 11px;
            color: rgba(172,200,162,.32);
            margin-top: 2px;
        }

        /* hero */
        .hero {
            position: relative; z-index: 1;
            animation: fadeUp .6s .15s both;
        }
        .live-badge {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 5px 13px; border-radius: 100px;
            background: rgba(172,200,162,.07);
            border: 1px solid rgba(172,200,162,.15);
            color: rgba(172,200,162,.65);
            font-size: 10px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            margin-bottom: 22px;
        }
        .live-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #ACC8A2;
            animation: blink 2.2s ease-in-out infinite;
        }

        .accent-line {
            width: 32px; height: 2px;
            background: #ACC8A2; border-radius: 1px;
            opacity: .65; margin-bottom: 16px;
        }

        .hero-title {
            font-weight: 800;
            font-size: clamp(30px, 3.2vw, 48px);
            line-height: 1.07;
            color: #f0f4f0;
            letter-spacing: -.03em;
            margin-bottom: 16px;
        }
        .hero-title .accent {
            color: #ACC8A2;
            font-style: italic;
            position: relative;
            display: inline-block;
            overflow: hidden;
        }
        .hero-title .accent::after {
            content: '';
            position: absolute; top: 0; left: -60%;
            width: 40%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.18), transparent);
            animation: shimmer 4s ease-in-out infinite 1.5s;
        }

        .hero-desc {
            font-size: 13px;
            color: rgba(172,200,162,.36);
            line-height: 1.75;
            max-width: 340px;
            font-weight: 300;
        }

        /* stats */
        .stats-row {
            position: relative; z-index: 1;
            display: flex; gap: 10px;
            animation: fadeUp .6s .3s both;
        }
        .stat-card {
            flex: 1; padding: 16px 18px;
            border-radius: 14px;
            background: rgba(172,200,162,.04);
            border: 1px solid rgba(172,200,162,.08);
            transition: background .2s, border-color .2s, transform .18s;
            position: relative; overflow: hidden;
        }
        .stat-card:hover {
            background: rgba(172,200,162,.08);
            border-color: rgba(172,200,162,.18);
            transform: translateY(-2px);
        }
        .stat-number {
            font-weight: 800; font-size: 28px;
            color: #ACC8A2; line-height: 1;
            margin-bottom: 5px; letter-spacing: -.03em;
        }
        .stat-label {
            font-size: 10px; font-weight: 500;
            color: rgba(172,200,162,.32);
            letter-spacing: .03em;
        }

        /* ══════════════════════════════════
           RIGHT PANEL
        ══════════════════════════════════ */
        .right-panel {
            width: 420px; flex-shrink: 0;
            display: flex; flex-direction: column;
            justify-content: center;
            padding: 52px 44px;
            background: #0c120c;
            position: relative;
            overflow-y: auto;
            animation: slideRight .6s cubic-bezier(.16,1,.3,1) both;
        }

        .right-panel::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 1px; height: 100%;
            background: linear-gradient(180deg,
                transparent 0%,
                rgba(172,200,162,.25) 25%,
                rgba(172,200,162,.45) 50%,
                rgba(172,200,162,.25) 75%,
                transparent 100%);
            pointer-events: none;
        }

        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600;
            color: rgba(172,200,162,.28);
            text-decoration: none;
            transition: color .15s, gap .15s;
            margin-bottom: 44px;
            letter-spacing: .02em;
        }
        .back-link svg { width: 14px; height: 14px; stroke: currentColor; fill: none; }
        .back-link:hover { color: #ACC8A2; gap: 9px; }

        .panel-eyebrow {
            font-size: 9px; font-weight: 700;
            letter-spacing: .18em; text-transform: uppercase;
            color: #ACC8A2; margin-bottom: 10px;
            display: flex; align-items: center; gap: 10px;
            animation: fadeUp .5s .2s both;
        }
        .panel-eyebrow::after {
            content: '';
            flex: 1; height: 1px;
            background: rgba(172,200,162,.1);
        }

        .login-title {
            font-weight: 800; font-size: 32px;
            color: #f0f4f0; letter-spacing: -.03em;
            line-height: 1.1; margin-bottom: 6px;
            animation: fadeUp .5s .25s both;
        }
        .login-sub {
            font-size: 13px; color: rgba(172,200,162,.28);
            font-weight: 300; margin-bottom: 36px;
            animation: fadeUp .5s .28s both;
        }

        /* form */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 9px; font-weight: 700;
            letter-spacing: .16em; text-transform: uppercase;
            color: rgba(172,200,162,.42);
            margin-bottom: 7px;
        }

        .input-wrap { position: relative; }

        .form-input {
            width: 100%;
            background: rgba(172,200,162,.04);
            border: 1px solid rgba(172,200,162,.11);
            border-radius: 11px;
            padding: 12px 16px;
            font-size: 13px;
            color: #e8f0e8;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color .18s, background .18s, box-shadow .18s;
        }
        .form-input::placeholder { color: rgba(172,200,162,.16); font-size: 12px; }
        .form-input:focus {
            border-color: rgba(172,200,162,.38);
            background: rgba(172,200,162,.06);
            box-shadow: 0 0 0 3px rgba(172,200,162,.06);
        }
        .form-input.has-icon { padding-right: 44px; }

        .toggle-pass {
            position: absolute; right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: rgba(172,200,162,.22);
            display: flex; align-items: center;
            padding: 4px; border-radius: 6px;
            transition: color .15s;
        }
        .toggle-pass:hover { color: #ACC8A2; }
        .toggle-pass svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

        /* error */
        .error-box {
            background: rgba(220,53,69,.07);
            border: 1px solid rgba(220,53,69,.18);
            color: #f87171;
            padding: 11px 14px; border-radius: 10px;
            font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: flex-start; gap: 9px;
            animation: fadeUp .3s both;
        }
        .error-box svg {
            width: 15px; height: 15px;
            stroke: currentColor; fill: none;
            flex-shrink: 0; margin-top: 1px;
        }

        /* button */
        .btn-login {
            width: 100%;
            background: #ACC8A2;
            color: #0b130b;
            font-weight: 700; font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 13px 20px; border: none;
            border-radius: 11px; cursor: pointer;
            letter-spacing: .02em;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
            margin-top: 8px;
            position: relative; overflow: hidden;
            transition: background .18s, transform .15s;
        }
        .btn-login::after {
            content: '';
            position: absolute; top: 0; left: -80%;
            width: 50%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.28), transparent);
            transform: skewX(-18deg);
            transition: left .5s ease;
        }
        .btn-login:hover::after { left: 150%; }
        .btn-login:hover { background: #bdd4b3; transform: translateY(-1px); }
        .btn-login:active { transform: scale(.99); }
        .btn-login svg {
            width: 15px; height: 15px;
            stroke: currentColor; fill: none;
            transition: transform .15s;
        }
        .btn-login:hover svg { transform: translateX(3px); }
        .btn-login.loading { pointer-events: none; opacity: .75; }
        .btn-login.loading::before {
            content: '';
            width: 13px; height: 13px;
            border: 2px solid rgba(11,19,11,.3);
            border-top-color: #0b130b;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        /* footer */
        .panel-footer {
            margin-top: 32px;
            font-size: 11px;
            color: rgba(172,200,162,.16);
            text-align: center;
        }

        /* ── MOBILE ── */
        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel {
                flex: none; padding: 32px 28px;
                border-right: none;
                border-bottom: 1px solid #1a271a;
                animation: fadeUp .5s both;
            }
            .stats-row { display: none; }
            .hero-title { font-size: 26px; }
            .right-panel { width: 100%; padding: 36px 28px; animation: fadeUp .5s .1s both; }
            .back-link { margin-bottom: 28px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>

    {{-- ═══ LEFT PANEL ═══ --}}
    <div class="left-panel">
        <div class="grid-bg"></div>
        <div class="glow-tl"></div>
        <div class="glow-br"></div>

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-icon">
                <svg stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="brand-name">Lab Management</div>
                <div class="brand-sub">Nuris Jember</div>
            </div>
        </div>

        {{-- Hero --}}
        <div class="hero">
            <div class="live-badge">
                <span class="live-dot"></span>
                Sistem Aktif
            </div>
            <div class="accent-line"></div>
            <h1 class="hero-title">
                Kelola Lab<br>
                dengan <span class="accent">Efisien</span><br>
                &amp; Terstruktur
            </h1>
            <p class="hero-desc">
                Platform manajemen laboratorium komputer terpadu —
                jadwal, booking, inventaris, dan pengadaan dalam satu sistem.
            </p>
        </div>

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['labs'] }}</div>
                <div class="stat-label">Lab Tersedia</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">Booking Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['today'] }}</div>
                <div class="stat-label">Booking Hari Ini</div>
            </div>
        </div>
    </div>

    {{-- ═══ RIGHT PANEL ═══ --}}
    <div class="right-panel">

        <a href="{{ url('/') }}" class="back-link">
            <svg stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Jadwal
        </a>

        <div class="panel-eyebrow">Panel Admin</div>
        <h2 class="login-title">Selamat<br>Datang Kembali</h2>
        <p class="login-sub">Masuk untuk mengelola laboratorium</p>

        {{-- slot: konten dari login.blade.php --}}
        {{ $slot }}

        <div class="panel-footer">
            &copy; {{ date('Y') }} Lab Management System &middot; Nuris Jember
        </div>
    </div>

    <script>
        document.querySelector('form')?.addEventListener('submit', function () {
            const btn = this.querySelector('.btn-login');
            if (btn) {
                btn.classList.add('loading');
                btn.innerHTML = 'Memproses...';
            }
        });
    </script>

</body>
</html>