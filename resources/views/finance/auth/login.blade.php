<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Keuangan Tim</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --green-dark:   #0a3d2b;
            --green-mid:    #155c3e;
            --green-accent: #1db974;
            --cream:        #f7f4ef;
            --text-dark:    #111810;
            --text-muted:   #6b7a6e;
            --border:       #d4ddd6;
            --error:        #dc2626;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .left-panel {
            background: var(--green-dark);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(29,185,116,0.15) 0%, transparent 70%);
            top: -100px; right: -100px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(29,185,116,0.1) 0%, transparent 70%);
            bottom: -50px; left: -50px;
        }
        .brand {
            display: flex; align-items: center; gap: 12px;
            position: relative; z-index: 1;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: var(--green-accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon svg { width: 22px; height: 22px; color: white; }
        .brand-name { font-size: 15px; font-weight: 600; color: white; letter-spacing: -0.3px; }
        .left-content { position: relative; z-index: 1; }
        .left-content h1 {
            font-size: 40px; font-weight: 300; color: white;
            line-height: 1.2; letter-spacing: -1px; margin-bottom: 16px;
        }
        .left-content h1 span { color: var(--green-accent); font-weight: 600; }
        .left-content p { font-size: 14px; color: rgba(255,255,255,0.5); line-height: 1.6; max-width: 280px; }
        .stats-row { display: flex; gap: 24px; position: relative; z-index: 1; }
        .stat-item { border-top: 1px solid rgba(255,255,255,0.15); padding-top: 16px; }
        .stat-value { font-family: 'DM Mono', monospace; font-size: 20px; color: white; font-weight: 500; }
        .stat-label { font-size: 11px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.8px; margin-top: 2px; }
        .right-panel { display: flex; align-items: center; justify-content: center; padding: 48px; }
        .login-box { width: 100%; max-width: 380px; }
        .login-box h2 { font-size: 26px; font-weight: 600; color: var(--text-dark); letter-spacing: -0.5px; margin-bottom: 6px; }
        .login-box .subtitle { font-size: 13px; color: var(--text-muted); margin-bottom: 36px; }
        .alert { padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: var(--error); }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 12px; font-weight: 500;
            color: var(--text-dark); text-transform: uppercase;
            letter-spacing: 0.6px; margin-bottom: 7px;
        }
        .form-group input {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid var(--border); border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: 14px;
            color: var(--text-dark); background: white;
            transition: border-color 0.2s; outline: none;
        }
        .form-group input:focus { border-color: var(--green-accent); }
        .form-group input.is-error { border-color: var(--error); background: #fef2f2; }
        .field-error { font-size: 12px; color: var(--error); margin-top: 5px; }
        .form-footer { display: flex; align-items: center; margin-bottom: 24px; }
        .remember-label { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--text-muted); cursor: pointer; }
        .remember-label input { width: 15px; height: 15px; accent-color: var(--green-accent); }
        .btn-login {
            width: 100%; padding: 13px;
            background: var(--green-dark); color: white;
            border: none; border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-login:hover { background: var(--green-mid); }
        .back-link { text-align: center; margin-top: 20px; font-size: 12px; color: var(--text-muted); }
        .back-link a { color: var(--green-mid); text-decoration: none; font-weight: 500; }
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
        }
    </style>
</head>
<body>
    <div class="left-panel">
        <div class="brand">
            <div class="brand-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="brand-name">Keuangan Tim</span>
        </div>
        <div class="left-content">
            <h1>Kelola keuangan<br>tim dengan <span>mudah</span></h1>
            <p>Catat pemasukan, pengeluaran, dan pantau anggaran bulanan tim dalam satu tempat.</p>
        </div>
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-value">WA</div>
                <div class="stat-label">Notif Otomatis</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">2 Role</div>
                <div class="stat-label">Admin & Bendahara</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">100%</div>
                <div class="stat-label">Terpisah</div>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-box">
            <h2>Selamat datang</h2>
            <p class="subtitle">Masuk ke sistem keuangan tim</p>

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('finance.login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           class="{{ $errors->has('email') ? 'is-error' : '' }}"
                           required autofocus autocomplete="email">
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           required autocomplete="current-password">
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-footer">
                    <label class="remember-label">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                </div>
                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="back-link">
                Bukan halaman Lab Management —
                <a href="{{ url('/login') }}">Login Lab</a>
            </div>
        </div>
    </div>
</body>
</html>