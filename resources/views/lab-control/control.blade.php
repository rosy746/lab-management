<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $labName }} – Lab Control</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0f1610;min-height:100vh;color:#fff}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes glow{0%,100%{box-shadow:0 0 20px rgba(34,197,94,.3)}50%{box-shadow:0 0 40px rgba(34,197,94,.6)}}
@keyframes glowRed{0%,100%{box-shadow:0 0 20px rgba(239,68,68,.3)}50%{box-shadow:0 0 40px rgba(239,68,68,.6)}}

/* HEADER */
.hdr{background:linear-gradient(180deg,#1a2517 0%,#0f1610 100%);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(172,200,162,.08)}
.hdr-brand{display:flex;align-items:center;gap:10px}
.hdr-dot{width:8px;height:8px;border-radius:50%;background:#ACC8A2;animation:pulse 2s infinite}
.hdr-name{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
.hdr-sub{font-size:11px;color:rgba(172,200,162,.4);margin-top:1px}
.btn-end{display:flex;align-items:center;gap:5px;padding:7px 14px;border-radius:8px;border:1px solid rgba(248,113,113,.25);background:rgba(248,113,113,.08);color:#f87171;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s}
.btn-end:hover{background:rgba(248,113,113,.15);border-color:rgba(248,113,113,.4)}

/* SESSION BAR */
.session-bar{background:rgba(172,200,162,.04);border-bottom:1px solid rgba(172,200,162,.06);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.session-info{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.session-item{font-size:11px;color:rgba(172,200,162,.4)}
.session-item strong{color:rgba(172,200,162,.8);font-family:'JetBrains Mono',monospace}
.countdown{font-size:12px;font-weight:700;color:#fbbf24;font-family:'JetBrains Mono',monospace}
.countdown.urgent{color:#f87171;animation:pulse 1s infinite}

/* MAIN */
.main{max-width:680px;margin:0 auto;padding:24px 16px 48px;animation:fadeUp .4s ease both}

/* STATUS HERO */
.status-hero{text-align:center;padding:32px 20px;margin-bottom:24px}
.status-ring{width:140px;height:140px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;position:relative;transition:all .4s ease}
.status-ring.online{background:radial-gradient(circle,rgba(34,197,94,.15),rgba(34,197,94,.05));border:2px solid rgba(34,197,94,.3);animation:glow 3s infinite}
.status-ring.offline{background:radial-gradient(circle,rgba(239,68,68,.15),rgba(239,68,68,.05));border:2px solid rgba(239,68,68,.3);animation:glowRed 3s infinite}
.status-ring.checking{background:radial-gradient(circle,rgba(251,191,36,.1),transparent);border:2px solid rgba(251,191,36,.2)}
.status-icon{font-size:48px;line-height:1}
.status-label{font-family:'Outfit',sans-serif;font-weight:800;font-size:24px;margin-bottom:6px}
.status-label.online{color:#22c55e}
.status-label.offline{color:#ef4444}
.status-label.checking{color:#fbbf24}
.status-desc{font-size:13px;color:rgba(255,255,255,.3)}

/* CONTROL BUTTONS */
.ctrl-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px}
.btn-ctrl{padding:20px 16px;border:none;border-radius:16px;font-size:15px;font-weight:700;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:8px;font-family:inherit;transition:all .2s;position:relative;overflow:hidden}
.btn-ctrl:disabled{opacity:.3;cursor:not-allowed;transform:none !important}
.btn-ctrl-icon{font-size:28px;line-height:1}
.btn-on{background:linear-gradient(135deg,#14532d,#16a34a);color:#fff;border:1px solid rgba(34,197,94,.3)}
.btn-on:hover:not(:disabled){transform:translateY(-3px);box-shadow:0 8px 24px rgba(34,197,94,.3);border-color:rgba(34,197,94,.5)}
.btn-off{background:linear-gradient(135deg,#7f1d1d,#dc2626);color:#fff;border:1px solid rgba(239,68,68,.3)}
.btn-off:hover:not(:disabled){transform:translateY(-3px);box-shadow:0 8px 24px rgba(239,68,68,.3);border-color:rgba(239,68,68,.5)}

/* INFO CARDS */
.info-row{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:24px}
.info-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:14px;text-align:center}
.info-val{font-family:'Outfit',sans-serif;font-size:20px;font-weight:800;color:#fff;margin-bottom:3px}
.info-key{font-size:10px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.07em}

/* DEVICES */
.devices-wrap{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:16px;overflow:hidden}
.devices-hdr{padding:14px 18px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.05)}
.devices-title{font-size:13px;font-weight:700;color:rgba(255,255,255,.7)}
.btn-refresh{display:flex;align-items:center;gap:4px;padding:5px 10px;border-radius:7px;border:1px solid rgba(172,200,162,.15);background:rgba(172,200,162,.06);color:rgba(172,200,162,.6);font-size:10px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s}
.btn-refresh:hover{background:rgba(172,200,162,.12);color:#ACC8A2}
.devices-body{padding:14px}
.device-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px}
.device-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:10px;padding:12px}
.device-card.active{border-color:rgba(34,197,94,.2);background:rgba(34,197,94,.04)}
.device-status{display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin-bottom:8px}
.device-status.online{background:rgba(34,197,94,.15);color:#22c55e}
.device-status.offline{background:rgba(255,255,255,.06);color:rgba(255,255,255,.3)}
.device-host{font-size:12px;font-weight:700;color:#fff;margin-bottom:4px}
.device-ip{font-size:11px;font-family:'JetBrains Mono',monospace;color:rgba(172,200,162,.5);margin-bottom:3px}
.device-mac{font-size:9px;font-family:'JetBrains Mono',monospace;color:rgba(255,255,255,.2)}
.empty{text-align:center;padding:32px;color:rgba(255,255,255,.2);font-size:13px}

/* LOADING */
.loading-overlay{position:fixed;inset:0;background:rgba(0,0,0,.8);display:none;align-items:center;justify-content:center;z-index:999;backdrop-filter:blur(4px)}
.loading-overlay.active{display:flex}
.loading-box{background:#1a2517;border:1px solid rgba(172,200,162,.15);border-radius:16px;padding:32px;text-align:center;min-width:220px}
.spinner{width:40px;height:40px;border:3px solid rgba(172,200,162,.1);border-top-color:#ACC8A2;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 14px}
.loading-text{font-size:13px;font-weight:600;color:rgba(172,200,162,.7)}

/* TOAST */
.toast{position:fixed;bottom:20px;right:20px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.4);display:none;align-items:center;gap:10px;padding:14px 18px;min-width:260px;z-index:1000;animation:fadeUp .3s ease}
.toast.active{display:flex}
.toast.ok{background:#14532d;border:1px solid rgba(34,197,94,.3)}
.toast.err{background:#7f1d1d;border:1px solid rgba(239,68,68,.3)}
.toast-msg{font-size:13px;font-weight:600;color:#fff;flex:1}
.toast-close{cursor:pointer;color:rgba(255,255,255,.4);font-size:18px;line-height:1}

@media(max-width:480px){
    .ctrl-row{grid-template-columns:1fr}
    .info-row{grid-template-columns:1fr 1fr}
}
</style>
</head>
<body>

{{-- HEADER --}}
<div class="hdr">
    <div class="hdr-brand">
        <div class="hdr-dot"></div>
        <div>
            <div class="hdr-name">{{ $labName }}</div>
            <div class="hdr-sub">{{ $session->teacher_name }} · Lab Control</div>
        </div>
    </div>
    <form method="POST" action="{{ route('lab.logout', $token) }}">
        @csrf
        <button type="submit" class="btn-end" onclick="return confirm('Akhiri sesi? Link ini tidak bisa digunakan lagi.')">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Akhiri Sesi
        </button>
    </form>
</div>

{{-- SESSION BAR --}}
<div class="session-bar">
    <div class="session-info">
        <div class="session-item">Sesi: <strong>{{ $session->session_start->format('H:i') }} – {{ $session->session_end->format('H:i') }}</strong></div>
        <div class="session-item">Tanggal: <strong>{{ $session->session_start->translatedFormat('d M Y') }}</strong></div>
    </div>
    <div class="countdown" id="countdown">–</div>
</div>

{{-- MAIN --}}
<div class="main">

    {{-- STATUS HERO --}}
    <div class="status-hero">
        <div class="status-ring checking" id="status-ring">
            <div class="status-icon" id="status-icon">⏳</div>
        </div>
        <div class="status-label checking" id="status-label">Memeriksa...</div>
        <div class="status-desc" id="status-desc">Menghubungi MikroTik</div>
    </div>

    {{-- CONTROL BUTTONS --}}
    <div class="ctrl-row">
        <button class="btn-ctrl btn-on" id="btn-on" onclick="toggleInternet('on')" disabled>
            <span class="btn-ctrl-icon">🟢</span>
            <span>Hidupkan Internet</span>
        </button>
        <button class="btn-ctrl btn-off" id="btn-off" onclick="toggleInternet('off')" disabled>
            <span class="btn-ctrl-icon">🔴</span>
            <span>Matikan Internet</span>
        </button>
    </div>

    {{-- INFO --}}
    <div class="info-row">
        <div class="info-card">
            <div class="info-val" id="info-status">–</div>
            <div class="info-key">Status</div>
        </div>
        <div class="info-card">
            <div class="info-val" id="info-devices">–</div>
            <div class="info-key">Devices</div>
        </div>
        <div class="info-card">
            <div class="info-val" id="info-update">–</div>
            <div class="info-key">Update</div>
        </div>
    </div>

    {{-- DEVICES --}}
    <div class="devices-wrap">
        <div class="devices-hdr">
            <div class="devices-title">📡 Connected Devices</div>
            <button class="btn-refresh" onclick="loadStatus()">↺ Refresh</button>
        </div>
        <div class="devices-body">
            <div id="devices-list"><div class="empty">Memuat...</div></div>
        </div>
    </div>
</div>

{{-- LOADING --}}
<div class="loading-overlay" id="loading">
    <div class="loading-box">
        <div class="spinner"></div>
        <div class="loading-text" id="loading-text">Memproses...</div>
    </div>
</div>

{{-- TOAST --}}
<div class="toast" id="toast">
    <span id="toast-icon" style="font-size:18px">✅</span>
    <div class="toast-msg" id="toast-msg"></div>
    <span class="toast-close" onclick="hideToast()">×</span>
</div>

<script>
const TOKEN      = '{{ $token }}';
const SESSION_END = new Date('{{ $session->session_end->toIso8601String() }}');
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const STATUS_URL = '/lab-control/' + TOKEN + '/status';
const TOGGLE_URL = '/lab-control/' + TOKEN + '/toggle';

// Countdown
function updateCountdown() {
    const diff = Math.floor((SESSION_END - new Date()) / 1000);
    const el = document.getElementById('countdown');
    if (diff <= 0) {
        el.textContent = '⚠ Sesi berakhir';
        el.classList.add('urgent');
        setButtonsEnabled(false);
        return;
    }
    const h = Math.floor(diff / 3600);
    const m = Math.floor((diff % 3600) / 60);
    const s = diff % 60;
    el.textContent = h > 0
        ? `${h}j ${m}m tersisa`
        : `${m}m ${String(s).padStart(2,'0')}s tersisa`;
    el.classList.toggle('urgent', diff < 300);
}
setInterval(updateCountdown, 1000);
updateCountdown();

// Load status
async function loadStatus() {
    try {
        const res  = await fetch(STATUS_URL);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Gagal');

        const online = data.nat_enabled;
        const ring   = document.getElementById('status-ring');
        const icon   = document.getElementById('status-icon');
        const label  = document.getElementById('status-label');
        const desc   = document.getElementById('status-desc');

        ring.className  = 'status-ring ' + (online ? 'online' : 'offline');
        icon.textContent = online ? '🌐' : '🚫';
        label.className = 'status-label ' + (online ? 'online' : 'offline');
        label.textContent = online ? 'Internet Aktif' : 'Internet Mati';
        desc.textContent  = online ? 'Siswa dapat mengakses internet' : 'Internet diblokir untuk lab ini';

        document.getElementById('info-status').textContent  = online ? 'Online' : 'Offline';
        document.getElementById('info-devices').textContent = data.active_users ?? 0;
        document.getElementById('info-update').textContent  = new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});

        setButtonsEnabled(true);
        renderDevices(data.devices || []);
    } catch(e) {
        document.getElementById('status-label').textContent = 'Error';
        document.getElementById('status-desc').textContent  = e.message;
        showToast('Gagal memuat status: ' + e.message, 'err');
    }
}

function renderDevices(devices) {
    const el = document.getElementById('devices-list');
    if (!devices.length) {
        el.innerHTML = '<div class="empty">📭 Tidak ada device terdeteksi</div>';
        return;
    }
    let html = '<div class="device-grid">';
    devices.forEach(d => {
        html += `
        <div class="device-card ${d.active ? 'active' : ''}">
            <div class="device-status ${d.active ? 'online' : 'offline'}">
                ${d.active ? '● ONLINE' : '○ OFFLINE'}
            </div>
            <div class="device-host">🖥 ${d.hostname || 'Unknown'}</div>
            <div class="device-ip">${d.ip}</div>
            <div class="device-mac">${d.mac}</div>
        </div>`;
    });
    html += '</div>';
    el.innerHTML = html;
}

async function toggleInternet(action) {
    const txt = action === 'on' ? 'menghidupkan' : 'mematikan';
    if (!confirm(`Yakin ingin ${txt} internet?`)) return;

    showLoading(`${txt.charAt(0).toUpperCase()+txt.slice(1)} internet...`);
    setButtonsEnabled(false);

    try {
        const res  = await fetch(TOGGLE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({action})
        });
        const data = await res.json();
        hideLoading();

        if (data.success) {
            showToast(`Internet berhasil di-${action}kan`, 'ok');
            await loadStatus();
        } else {
            throw new Error(data.error || 'Gagal');
        }
    } catch(e) {
        hideLoading();
        showToast('Gagal: ' + e.message, 'err');
        setButtonsEnabled(true);
    }
}

function setButtonsEnabled(v) {
    document.getElementById('btn-on').disabled  = !v;
    document.getElementById('btn-off').disabled = !v;
}
function showLoading(msg) {
    document.getElementById('loading-text').textContent = msg;
    document.getElementById('loading').classList.add('active');
}
function hideLoading() {
    document.getElementById('loading').classList.remove('active');
}
function showToast(msg, type) {
    document.getElementById('toast-icon').textContent = type === 'ok' ? '✅' : '❌';
    document.getElementById('toast-msg').textContent  = msg;
    const t = document.getElementById('toast');
    t.className = `toast active ${type}`;
    setTimeout(hideToast, 5000);
}
function hideToast() {
    document.getElementById('toast').classList.remove('active');
}

loadStatus();
setInterval(loadStatus, 30000);
</script>
</body>
</html>