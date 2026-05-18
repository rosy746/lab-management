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

        :root {
            --g9:    #1A2517;
            --g8:    #2d3d29;
            --g7:    #3d5438;
            --acc:   #ACC8A2;
            --acc2:  #8ab87e;
            --bg:    #f0f4ef;
            --border:#e8f0e6;
            --text:  #374151;
            --muted: #9ca3af;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%  { transform: translateX(-7px); }
            30%  { transform: translateX(7px); }
            45%  { transform: translateX(-5px); }
            60%  { transform: translateX(5px); }
            75%  { transform: translateX(-3px); }
            90%  { transform: translateX(3px); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: none; }
        }
        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(14px, -18px) scale(1.04); }
            66%  { transform: translate(-10px, 12px) scale(.97); }
        }
        @keyframes slideLeft {
            from { transform: translateX(-24px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }
        @keyframes slideRight {
            from { transform: translateX(24px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }

        /* ── BASE ── */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            background: var(--bg);
            position: relative;
            overflow-x: hidden;
        }

        /* Blobs background */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 {
            width: 500px; height: 500px;
            top: -180px; left: -150px;
            background: radial-gradient(circle, rgba(172,200,162,.45) 0%, transparent 70%);
            animation: blobFloat 14s ease-in-out infinite;
        }
        .blob-2 {
            width: 400px; height: 400px;
            bottom: -130px; right: -120px;
            background: radial-gradient(circle, rgba(172,200,162,.35) 0%, transparent 70%);
            animation: blobFloat 18s ease-in-out infinite reverse;
        }
        .blob-3 {
            width: 260px; height: 260px;
            top: 40%; right: 5%;
            background: radial-gradient(circle, rgba(172,200,162,.25) 0%, transparent 70%);
            animation: blobFloat 11s ease-in-out infinite 2s;
        }

        /* ── WRAPPER ── */
        .login-wrapper {
            position: relative; z-index: 1;
            width: 100%; max-width: 860px;
            animation: fadeUp .55s cubic-bezier(.16,1,.3,1) both;
        }

        /* Tombol kembali di atas card */
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600;
            color: var(--g8);
            text-decoration: none;
            padding: 7px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: #fff;
            margin-bottom: 14px;
            transition: color .15s, border-color .15s, transform .15s, box-shadow .15s;
            display: inline-flex;
        }
        .back-link svg { width: 14px; height: 14px; stroke: currentColor; fill: none; }
        .back-link:hover {
            color: var(--g9);
            border-color: var(--acc);
            transform: translateX(-2px);
            box-shadow: 0 2px 8px rgba(26,37,23,.08);
        }

        /* ── CARD (split panel) ── */
        .login-card {
            display: flex;
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 4px 6px rgba(26,37,23,.04),
                0 12px 32px rgba(26,37,23,.1),
                0 32px 64px rgba(26,37,23,.08);
            border: 2px solid var(--acc);
            min-height: 520px;
        }

        /* ── LEFT PANEL (hijau) ── */
        .left-panel {
            flex: 1;
            background: linear-gradient(160deg, var(--g7) 0%, var(--g8) 50%, var(--g9) 100%);
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            animation: slideLeft .55s .05s cubic-bezier(.16,1,.3,1) both;
        }

        /* Decorative circles di panel kiri */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            border-radius: 50%;
            border: 1px solid rgba(172,200,162,.1);
            bottom: -100px; right: -80px;
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 1px solid rgba(172,200,162,.08);
            top: -60px; right: 40px;
            pointer-events: none;
        }

        /* Brand */
        .brand {
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 1;
            animation: fadeUp .5s .1s both;
        }
        .brand-icon {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: rgba(172,200,162,.15);
            border: 1px solid rgba(172,200,162,.25);
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon svg { width: 20px; height: 20px; stroke: var(--acc); fill: none; }
        .brand-name { font-weight: 700; font-size: 14px; color: #fff; letter-spacing: -.01em; }
        .brand-sub  { font-size: 10px; color: rgba(172,200,162,.45); margin-top: 1px; }

        /* Hero text */
        .left-hero {
            position: relative; z-index: 1;
            animation: fadeUp .55s .18s both;
        }
        .left-hero h1 {
            font-weight: 800;
            font-size: clamp(26px, 3vw, 36px);
            color: #fff;
            line-height: 1.15;
            letter-spacing: -.03em;
            margin-bottom: 14px;
        }
        .left-hero h1 span { color: var(--acc); }
        .left-hero p {
            font-size: 13px;
            color: rgba(172,200,162,.5);
            line-height: 1.7;
            max-width: 260px;
        }

        /* Stats */
        .left-stats {
            display: flex; gap: 10px;
            position: relative; z-index: 1;
            animation: fadeUp .55s .28s both;
        }
        .left-stat {
            flex: 1;
            border-top: 1px solid rgba(172,200,162,.2);
            padding-top: 12px;
        }
        .left-stat-num {
            font-weight: 800; font-size: 22px;
            color: var(--acc); line-height: 1;
            letter-spacing: -.03em;
            animation: countUp .5s both;
        }
        .left-stat-lbl {
            font-size: 10px; font-weight: 600;
            color: rgba(172,200,162,.35);
            text-transform: uppercase; letter-spacing: .06em;
            margin-top: 3px;
        }

        /* ── RIGHT PANEL (putih) ── */
        .right-panel {
            width: 400px; flex-shrink: 0;
            background: #fff;
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: slideRight .55s .05s cubic-bezier(.16,1,.3,1) both;
        }

        .panel-eyebrow {
            font-size: 10px; font-weight: 700;
            letter-spacing: .14em; text-transform: uppercase;
            color: var(--acc2);
            margin-bottom: 8px;
            animation: fadeUp .5s .15s both;
        }
        .panel-title {
            font-weight: 800; font-size: 26px;
            color: var(--g9); letter-spacing: -.03em;
            line-height: 1.15; margin-bottom: 6px;
            animation: fadeUp .5s .18s both;
        }
        .panel-sub {
            font-size: 13px; color: var(--muted);
            font-weight: 400; margin-bottom: 32px;
            animation: fadeUp .5s .2s both;
        }

        /* ── FORM ── */
        .form-group { margin-bottom: 16px; }

        .float-label { position: relative; }
        .float-label .form-input { padding: 20px 16px 8px; }
        .float-label .form-input.has-icon { padding-right: 44px; }
        .float-label label {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            font-size: 13px; font-weight: 400;
            color: var(--muted);
            pointer-events: none;
            transition: all .18s cubic-bezier(.4,0,.2,1);
        }
        .float-label .form-input:focus ~ label,
        .float-label .form-input:not(:placeholder-shown) ~ label {
            top: 10px; transform: none;
            font-size: 9px; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--g8);
        }

        .form-input {
            width: 100%;
            background: #fafcf9;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 13px 16px;
            font-size: 13px; color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color .18s, background .18s, box-shadow .18s;
        }
        .form-input::placeholder { color: transparent; }
        .form-input:focus {
            border-color: var(--acc);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(172,200,162,.15);
        }
        .form-input.is-error { border-color: #fca5a5; background: #fef2f2; }
        .form-input.is-error:focus { box-shadow: 0 0 0 3px rgba(248,113,113,.1); }

        .input-wrap { position: relative; }
        .toggle-pass {
            position: absolute; right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--muted); display: flex; align-items: center;
            padding: 4px; border-radius: 6px; transition: color .15s;
        }
        .toggle-pass:hover { color: var(--g8); }
        .toggle-pass svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

        /* Caps lock */
        .caps-warning {
            display: none; align-items: center; gap: 5px;
            font-size: 11px; color: #b45309;
            margin-top: 5px; animation: fadeIn .2s both;
        }
        .caps-warning.show { display: flex; }
        .caps-warning svg { width: 12px; height: 12px; stroke: currentColor; fill: none; flex-shrink: 0; }

        /* Remember me */
        .form-footer {
            display: flex; align-items: center;
            margin-bottom: 20px; margin-top: 4px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--muted);
            cursor: pointer; user-select: none;
        }
        .remember-label input[type="checkbox"] {
            appearance: none; -webkit-appearance: none;
            width: 16px; height: 16px;
            border: 1.5px solid var(--border);
            border-radius: 5px; background: #fafcf9;
            cursor: pointer; position: relative;
            transition: background .15s, border-color .15s; flex-shrink: 0;
        }
        .remember-label input[type="checkbox"]:checked {
            background: var(--g9); border-color: var(--g9);
        }
        .remember-label input[type="checkbox"]:checked::after {
            content: '';
            position: absolute; top: 2px; left: 5px;
            width: 4px; height: 7px;
            border: 2px solid var(--acc);
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        /* Error box */
        .error-box {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #dc2626; padding: 11px 14px; border-radius: 12px;
            font-size: 12px; margin-bottom: 18px;
            display: flex; align-items: flex-start; gap: 9px;
            animation: fadeUp .3s both;
        }
        .error-box svg {
            width: 14px; height: 14px; stroke: currentColor; fill: none;
            flex-shrink: 0; margin-top: 1px;
        }
        .error-box.shake { animation: shake .45s cubic-bezier(.36,.07,.19,.97) both; }

        /* Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--g9) 0%, var(--g8) 100%);
            color: var(--acc);
            font-weight: 700; font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 14px 20px; border: none;
            border-radius: 12px; cursor: pointer;
            letter-spacing: .02em;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
            position: relative; overflow: hidden;
            transition: opacity .18s, transform .15s, box-shadow .18s;
            box-shadow: 0 4px 16px rgba(26,37,23,.25);
        }
        .btn-login::after {
            content: '';
            position: absolute; top: 0; left: -80%;
            width: 50%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.1), transparent);
            transform: skewX(-18deg);
            transition: left .5s ease;
        }
        .btn-login:hover::after { left: 150%; }
        .btn-login:hover {
            opacity: .92; transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,37,23,.32);
        }
        .btn-login:active { transform: scale(.99); }
        .btn-login svg {
            width: 15px; height: 15px; stroke: currentColor; fill: none;
            transition: transform .15s;
        }
        .btn-login:hover svg { transform: translateX(3px); }
        .btn-login.loading { pointer-events: none; opacity: .7; gap: 10px; }
        .btn-login .spinner {
            display: none; width: 14px; height: 14px;
            border: 2px solid rgba(172,200,162,.25);
            border-top-color: var(--acc);
            border-radius: 50%; animation: spin .7s linear infinite; flex-shrink: 0;
        }
        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-arrow { display: none; }

        /* Footer */
        .panel-footer {
            margin-top: 28px; font-size: 11px;
            color: var(--muted); text-align: center;
        }

        /* ── MOBILE ── */
        @media (max-width: 720px) {
            .login-card { flex-direction: column; min-height: unset; }
            .left-panel { padding: 32px 28px; }
            .left-stats { display: none; }
            .left-hero p { display: none; }
            .right-panel { width: 100%; padding: 32px 28px; }
        }
        @media (max-width: 480px) {
            .left-panel { padding: 24px 22px; }
            .right-panel { padding: 28px 22px; }
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

    {{-- Blobs --}}
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="login-wrapper">

        {{-- Tombol kembali --}}
        <a href="{{ url('/') }}" class="back-link">
            <svg viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Jadwal
        </a>

        {{-- Card split panel --}}
        <div class="login-card" id="login-card">

            {{-- ── LEFT PANEL ── --}}
            <div class="left-panel">

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
                <div class="left-hero">
                    <h1>Selamat<br>Datang <span>Kembali</span></h1>
                    <p>Platform manajemen laboratorium komputer — jadwal, booking, inventaris dalam satu sistem.</p>
                </div>

                {{-- Stats --}}
                <div class="left-stats">
                    <div class="left-stat">
                        <div class="left-stat-num" data-target="{{ $stats['labs'] }}">0</div>
                        <div class="left-stat-lbl">Lab</div>
                    </div>
                    <div class="left-stat">
                        <div class="left-stat-num" data-target="{{ $stats['pending'] }}">0</div>
                        <div class="left-stat-lbl">Pending</div>
                    </div>
                    <div class="left-stat">
                        <div class="left-stat-num" data-target="{{ $stats['today'] }}">0</div>
                        <div class="left-stat-lbl">Hari Ini</div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT PANEL ── --}}
            <div class="right-panel">
                <div class="panel-eyebrow">Panel Admin</div>
                <div class="panel-title">Masuk ke<br>Sistem</div>
                <p class="panel-sub">Kelola laboratorium dengan mudah</p>

                {{-- slot: konten dari login.blade.php --}}
                {{ $slot }}

                <div class="panel-footer">
                    &copy; {{ date('Y') }} Lab Management &middot; Nuris Jember
                </div>
            </div>

        </div>
    </div>

    <script>
        // Count-up stats
        document.querySelectorAll('.left-stat-num[data-target]').forEach(el => {
            const target = parseInt(el.dataset.target, 10) || 0;
            if (target === 0) { el.textContent = '0'; return; }
            const duration = 900;
            const step = Math.ceil(duration / target);
            let current = 0;
            const timer = setInterval(() => {
                current = Math.min(current + 1, target);
                el.textContent = current;
                if (current >= target) clearInterval(timer);
            }, step);
        });

        // Shake saat error
        const errorBox = document.querySelector('.error-box');
        if (errorBox) {
            const card = document.getElementById('login-card');
            card?.classList.add('shake');
            setTimeout(() => card?.classList.remove('shake'), 500);
        }

        // Loading state
        document.querySelector('form')?.addEventListener('submit', function () {
            const btn = this.querySelector('.btn-login');
            if (btn) {
                btn.classList.add('loading');
                const text = btn.querySelector('.btn-text');
                if (text) text.textContent = 'Memproses...';
            }
        });

        // Caps Lock warning
        const passInput = document.getElementById('password');
        const capsWarn  = document.getElementById('caps-warning');
        if (passInput && capsWarn) {
            passInput.addEventListener('keyup', e => {
                capsWarn.classList.toggle('show', !!e.getModifierState?.('CapsLock'));
            });
            passInput.addEventListener('blur', () => capsWarn.classList.remove('show'));
        }
    </script>

</body>
</html>
