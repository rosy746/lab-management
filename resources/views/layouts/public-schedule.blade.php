<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lab Management') - Lab Management Nuris Jember</title>

    {{-- Font lokal (jalankan localize-fonts.php dulu untuk generate file ini) --}}
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    {{-- CSS navbar + page-trans yang dipakai semua halaman publik --}}
    <style>
        /* ─── CSS VARIABLES — dipakai semua halaman publik ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g9: #1A2517;
            --g8: #2d3d29;
            --g7: #3d5438;
            --acc: #ACC8A2;
            --acc2: #8ab87e;
            --white: #fff;
            --bg: #f4f7f3;
            --border: #e8f0e6;
            --text: #1A2517;
            --muted: #9ca3af;
            --sub: #6b7280;
            --shadow: 0 2px 12px rgba(0,0,0,.07);
            --r: 14px;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }

        footer { text-align: center; padding: 18px; font-size: 12px; color: var(--muted); }

        .pub-navbar {
            position: sticky; top: 0; z-index: 100;
            background: linear-gradient(135deg, #1A2517, #2a3826);
            box-shadow: 0 2px 16px rgba(0,0,0,.28);
            animation: navSlideDown .4s cubic-bezier(.16,1,.3,1) both;
            width: 100%; overflow: hidden;
        }
        .pub-inner {
            max-width: 1280px; margin: 0 auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 60px; gap: 10px;
        }
        .pub-brand {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; flex-shrink: 0;
        }
        .pub-brand-icon {
            width: 32px; height: 32px; border-radius: 9px;
            background: rgba(172,200,162,.12); border: 1.5px solid rgba(172,200,162,.25);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .18s;
        }
        .pub-brand-icon:hover { background: rgba(172,200,162,.22); }
        .pub-brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700;
            font-size: 14px; color: #fff; line-height: 1.2;
        }
        .pub-brand-sub { font-size: 10px; color: rgba(172,200,162,.4); }

        .pub-links {
            display: flex; align-items: center; gap: 2px;
            overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .pub-links::-webkit-scrollbar { display: none; }

        .pub-link {
            font-size: 12px; font-weight: 600;
            color: rgba(172,200,162,.55); text-decoration: none;
            padding: 6px 10px; border-radius: 8px;
            transition: color .15s, background .15s;
            white-space: nowrap;
        }
        .pub-link:hover, .pub-link.on { color: #ACC8A2; background: rgba(172,200,162,.1); }

        .pub-btn {
            font-size: 11px; font-weight: 700; padding: 6px 12px;
            border-radius: 8px; color: #ACC8A2;
            border: 1px solid rgba(172,200,162,.3); text-decoration: none;
            transition: background .15s, transform .15s;
            white-space: nowrap; flex-shrink: 0; margin-left: 4px;
        }
        .pub-btn:hover { background: rgba(172,200,162,.08); transform: translateY(-1px); }

        .pub-nav-row2 { display: none; }

        footer { text-align: center; padding: 18px; font-size: 12px; color: #9ca3af; }

        .page-trans {
            position: fixed; inset: 0; z-index: 9999;
            background: linear-gradient(135deg, #1A2517, #2d3d29);
            opacity: 0; pointer-events: none; transition: opacity .22s ease;
        }
        .page-trans.go { opacity: 1; pointer-events: all; }

        @keyframes navSlideDown {
            from { transform: translateY(-64px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }

        @media (max-width: 640px) {
            .pub-inner { padding: 0 1rem; }
            .pub-brand-sub { display: none; }
            .pub-brand-name { font-size: 13px; }
            .pub-link { padding: 6px 8px; font-size: 11px; }
            .pub-btn { padding: 5px 10px; }
        }

        @media (max-width: 600px) {
            .pub-link { display: none; }
            .pub-btn { padding: 6px 11px; font-size: 12px; margin-left: 3px; }

            .pub-nav-row2 {
                display: flex; align-items: center; gap: 2px;
                padding: 0 10px 8px;
                overflow-x: auto; -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                border-top: 1px solid rgba(172,200,162,.1);
            }
            .pub-nav-row2::-webkit-scrollbar { display: none; }

            .pub-nav2-link {
                padding: 6px 12px; border-radius: 8px;
                font-size: 12px; font-weight: 600;
                color: rgba(172,200,162,.55); text-decoration: none;
                white-space: nowrap; transition: color .15s, background .15s;
            }
            .pub-nav2-link:hover, .pub-nav2-link.on {
                color: #ACC8A2; background: rgba(172,200,162,.1);
            }
        }
    </style>

    {{-- CSS + JS per halaman via Vite --}}
    @yield('vite')
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
            <a href="{{ route('home') }}"
               class="pub-link {{ request()->routeIs('home') ? 'on' : '' }}">Jadwal</a>
            <a href="{{ route('inventory.public') }}"
               class="pub-link {{ request()->routeIs('inventory.public') ? 'on' : '' }}">Inventaris</a>
            <a href="{{ route('rekap.public') }}"
               class="pub-link {{ request()->routeIs('rekap.public') ? 'on' : '' }}">Rekap</a>
            <a href="{{ route('assignment.public') }}"
               class="pub-link {{ request()->routeIs('assignment.public') ? 'on' : '' }}">Tugas</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>

    {{-- Mobile nav row 2 --}}
    <div class="pub-nav-row2">
        <a href="{{ route('home') }}"
           class="pub-nav2-link {{ request()->routeIs('home') ? 'on' : '' }}">Jadwal</a>
        <a href="{{ route('inventory.public') }}"
           class="pub-nav2-link {{ request()->routeIs('inventory.public') ? 'on' : '' }}">Inventaris</a>
        <a href="{{ route('rekap.public') }}"
           class="pub-nav2-link {{ request()->routeIs('rekap.public') ? 'on' : '' }}">Rekap</a>
        <a href="{{ route('assignment.public') }}"
           class="pub-nav2-link {{ request()->routeIs('assignment.public') ? 'on' : '' }}">Tugas</a>
    </div>
</nav>

{{-- ═══ KONTEN HALAMAN ═══ --}}
@yield('content')

<footer>© {{ date('Y') }} Lab Management System · Nuris Jember</footer>

{{-- Page transition overlay --}}
<div class="page-trans" id="pt"></div>

{{-- JS dari halaman masing-masing --}}
@yield('scripts')

{{-- SPA Page Transition — dipakai semua halaman publik --}}
<script>
document.querySelectorAll('a.pub-link, a.pub-btn, a.pub-brand').forEach(function(a) {
    var href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') || a.getAttribute('target') === '_blank') return;
    a.addEventListener('click', function(e) {
        var current = window.location.pathname;
        try {
            var target = new URL(href, window.location.href).pathname;
            if (target === current) return;
        } catch(err) {}
        e.preventDefault();
        document.getElementById('pt').classList.add('go');
        setTimeout(function() { window.location.href = href; }, 220);
    });
});
window.addEventListener('pageshow', function() {
    document.getElementById('pt').classList.remove('go');
});
</script>

</body>
</html>