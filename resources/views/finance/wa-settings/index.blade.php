@extends('finance.layouts.app')
@section('title', 'Pengaturan WA — Keuangan Tim')
@section('page-title', 'Pengaturan WhatsApp')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes pulse-ring {
    0%   { transform: scale(0.85); opacity: 0.9; }
    100% { transform: scale(1.7);  opacity: 0; }
}
@keyframes tagIn {
    from { opacity: 0; transform: scale(0.7) translateY(6px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60% { transform: translateX(-5px); }
    40%,80% { transform: translateX(5px); }
}
@keyframes msgIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes spin { to { transform: rotate(360deg); } }

.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

/* ── Layout ── */
.wa-grid {
    display: grid;
    grid-template-columns: 1.25fr 1fr;
    gap: 14px;
}
@media (max-width: 900px) { .wa-grid { grid-template-columns: 1fr; } }

/* ── Status Hero Card ── */
.status-card {
    background: linear-gradient(135deg, var(--g1) 0%, var(--g2) 60%, #0f5c38 100%);
    border-radius: var(--radius);
    padding: 22px;
    position: relative; overflow: hidden;
    border: 1px solid rgba(255,255,255,0.06);
    margin-bottom: 14px;
}
.status-card::before {
    content: '';
    position: absolute;
    width: 220px; height: 220px; border-radius: 50%;
    background: radial-gradient(circle, rgba(0,232,122,0.13) 0%, transparent 70%);
    right: -60px; top: -60px; pointer-events: none;
}
.sc-inner { position: relative; z-index: 1; }
.sc-top { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
.sc-icon {
    width: 52px; height: 52px; border-radius: 15px;
    background: rgba(255,255,255,0.09);
    border: 1px solid rgba(255,255,255,0.12);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
}
.sc-title { font-size: 17px; font-weight: 700; color: white; }
.sc-sub   { font-size: 12px; color: rgba(255,255,255,0.4); margin-top: 2px; }

.conn-badge {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700; margin-bottom: 14px;
}
.conn-badge.ok  { background: rgba(0,232,122,0.15); color: var(--accent); border: 1px solid rgba(0,232,122,0.25); }
.conn-badge.off { background: rgba(255,77,109,0.15); color: #fb7185; border: 1px solid rgba(255,77,109,0.25); }
.conn-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; position: relative; flex-shrink: 0; }
.conn-dot.pulse::after {
    content: ''; position: absolute; inset: -3px; border-radius: 50%;
    background: currentColor; opacity: 0;
    animation: pulse-ring 1.5s ease-out infinite;
}

/* Stats row */
.sc-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.sc-stat {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px; padding: 10px 12px;
    text-align: center;
}
.sc-stat-val { font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 700; color: white; }
.sc-stat-lbl { font-size: 10px; color: rgba(255,255,255,0.35); margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

/* ── Multi-target Input ── */
.target-box {
    border: 1.5px solid var(--border); border-radius: 11px;
    padding: 10px;
    min-height: 80px;
    transition: border-color 0.2s, box-shadow 0.2s;
    cursor: text;
    position: relative;
}
.target-box:focus-within {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(0,232,122,0.1);
}

/* Tags */
.tags-area { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
.tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 10px; border-radius: 20px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11.5px; font-weight: 500;
    animation: tagIn 0.25s cubic-bezier(0.34,1.56,0.64,1);
    transition: all 0.2s;
    max-width: 100%;
}
.tag.phone { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.tag.group { background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }
.tag-label { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tag-del {
    width: 16px; height: 16px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0; font-size: 11px; line-height: 1;
    background: rgba(0,0,0,0.1); transition: background 0.15s;
}
.tag-del:hover { background: rgba(255,77,109,0.3); color: var(--red); }

/* Add input row */
.add-row { display: flex; gap: 6px; }
.add-input {
    flex: 1; border: none; outline: none;
    font-family: 'JetBrains Mono', monospace; font-size: 12.5px;
    color: var(--text); background: transparent;
    padding: 4px 6px;
}
.add-input::placeholder { font-family: 'Sora', sans-serif; font-size: 12px; color: var(--border); }
.add-btn {
    padding: 5px 12px; border-radius: 8px;
    background: var(--g1); color: white;
    font-size: 12px; font-weight: 700;
    border: none; cursor: pointer;
    transition: all 0.2s; white-space: nowrap;
    flex-shrink: 0;
}
.add-btn:hover { background: var(--g2); transform: scale(1.03); }

/* Target type hint */
.type-hint {
    display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap;
}
.type-chip {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10.5px; font-weight: 600;
    background: var(--bg); border: 1px solid var(--border); color: var(--muted);
    cursor: pointer; transition: all 0.15s;
}
.type-chip:hover { border-color: var(--accent); color: var(--g1); background: rgba(0,232,122,0.05); }

/* ── Toggle ── */
.toggle-section { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
.toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 14px; border-radius: 10px;
    background: var(--bg); border: 1px solid var(--border);
    transition: background 0.15s, border-color 0.15s;
}
.toggle-row:hover { background: white; border-color: var(--accent); }
.t-title { font-size: 13px; font-weight: 600; color: var(--text); }
.t-desc  { font-size: 11px; color: var(--muted); margin-top: 2px; }

.switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.switch input { opacity:0; width:0; height:0; }
.slider {
    position: absolute; inset: 0; border-radius: 24px;
    background: var(--border); cursor: pointer; transition: background 0.25s;
}
.slider::before {
    content: ''; position: absolute;
    width: 18px; height: 18px; border-radius: 50%;
    background: white; left: 3px; top: 3px;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.switch input:checked + .slider { background: var(--accent2); }
.switch input:checked + .slider::before { transform: translateX(20px); }

/* ── Token field ── */
.token-wrap { position: relative; }
.token-inp  { font-family: 'JetBrains Mono', monospace !important; font-size: 12px !important; padding-right: 44px !important; }
.token-eye  {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--muted); padding: 2px; transition: color 0.15s;
}
.token-eye:hover { color: var(--text); }
.token-eye svg { width: 16px; height: 16px; display: block; }

/* ── Threshold ── */
.thresh-wrap { position: relative; }
.thresh-prefix {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; color: var(--muted);
}
.thresh-inp { padding-left: 42px !important; font-family: 'JetBrains Mono', monospace !important; font-size: 14px !important; font-weight: 600 !important; }

/* ── Divider ── */
.form-divider {
    font-size: 9.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.2px; color: var(--muted);
    display: flex; align-items: center; gap: 10px;
    margin: 16px 0 12px;
}
.form-divider::before, .form-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* ── WA Preview ── */
.phone-mockup { border-radius: var(--radius); overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
.phone-head { background: #075e54; padding: 11px 14px; display: flex; align-items: center; gap: 10px; }
.phone-avatar { width: 34px; height: 34px; border-radius: 50%; background: #128c7e; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
.phone-name { font-size: 13px; font-weight: 700; color: white; }
.phone-status { font-size: 10.5px; color: rgba(255,255,255,0.6); }
.phone-body {
    background: #e5ddd5;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000000' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
    padding: 14px 12px; min-height: 160px;
}
.wa-bubble {
    max-width: 86%; background: white; border-radius: 8px 8px 8px 2px;
    padding: 10px 12px 22px; margin-bottom: 6px; position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    font-size: 12px; line-height: 1.55; color: #111;
    white-space: pre-line; animation: msgIn 0.4s ease;
}
.wa-bubble::before {
    content: ''; position: absolute;
    top: 0; left: -7px;
    border-style: solid; border-width: 0 8px 8px 0;
    border-color: transparent white transparent transparent;
}
.wa-time { position: absolute; bottom: 5px; right: 9px; font-size: 10px; color: #999; }
.wa-check { color: #4fc3f7; font-size: 11px; }

/* ── Target count badge ── */
.tcount-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    background: rgba(0,232,122,0.1); color: var(--accent2);
    border: 1px solid rgba(0,232,122,0.2);
    font-size: 11px; font-weight: 700;
}

/* ── Log ── */
.log-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.log-table thead tr { background: var(--bg); border-bottom: 2px solid var(--border); }
.log-table thead th { padding: 9px 14px; text-align: left; font-size: 9.5px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.7px; white-space: nowrap; }
.log-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.12s; }
.log-table tbody tr:last-child { border-bottom: none; }
.log-table tbody tr:hover { background: var(--bg); }
.log-table tbody td { padding: 9px 14px; }
.log-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; white-space: nowrap; }
.log-status.sent   { background: #dcfce7; color: #16a34a; }
.log-status.failed { background: #fff1f2; color: var(--red); }
.log-phone { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--muted); max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.log-msg   { font-size: 11px; color: var(--muted); max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* ── Btn WA ── */
.btn-wa { background: #25d366; color: white; box-shadow: 0 3px 10px rgba(37,211,102,0.3); }
.btn-wa:hover { background: #22c35e; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(37,211,102,0.4); }

/* ── Test modal ── */
.modal-backdrop {
    display: none; position: fixed; inset: 0; z-index: 500;
    align-items: center; justify-content: center;
    background: rgba(7,31,20,0.65); backdrop-filter: blur(5px);
}
.modal-backdrop.open { display: flex; }
.modal-box {
    background: white; border-radius: 16px; padding: 28px;
    width: 400px; max-width: 95vw;
    box-shadow: 0 24px 48px rgba(0,0,0,0.2);
    animation: floatIn .3s ease;
}
.test-result-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 12px; border-radius: 8px; font-size: 12px;
    margin-bottom: 6px;
}
.test-result-row.ok  { background: #f0fdf4; border: 1px solid #86efac; }
.test-result-row.err { background: #fff1f2; border: 1px solid #fda4af; }
.test-phone { font-family: 'JetBrains Mono', monospace; font-size: 11px; }
</style>
@endpush

@section('content')
<div class="wa-grid">

    {{-- ═══ KIRI: Status + Form ═══ --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Status Card --}}
        @php
            $targets      = $setting->getAllTargets();
            $totalTargets = count($targets);
            $connected    = !empty($setting->fonnte_token);
        @endphp
        <div class="status-card float-in" style="animation-delay:.05s">
            <div class="sc-inner">
                <div class="sc-top">
                    <div class="sc-icon">💬</div>
                    <div>
                        <div class="sc-title">Baileys WhatsApp</div>
                        <div class="sc-sub">Broadcast notifikasi ke tim & grup</div>
                    </div>
                </div>
                <div class="conn-badge {{ $connected ? 'ok' : 'off' }}">
                    <span class="conn-dot {{ $connected ? 'pulse' : '' }}"></span>
                    {{ $connected ? 'Token Aktif' : 'Belum Dikonfigurasi' }}
                </div>
                <div class="sc-stats">
                    <div class="sc-stat">
                        <div class="sc-stat-val">{{ $totalTargets }}</div>
                        <div class="sc-stat-lbl">Target</div>
                    </div>
                    <div class="sc-stat">
                        <div class="sc-stat-val">{{ $logs->where('status','sent')->count() }}</div>
                        <div class="sc-stat-lbl">Terkirim</div>
                    </div>
                    <div class="sc-stat">
                        <div class="sc-stat-val">{{ $logs->where('status','failed')->count() }}</div>
                        <div class="sc-stat-lbl">Gagal</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Konfigurasi --}}
        <div class="card float-in" style="animation-delay:.1s">
            <div class="card-head">
                <span class="card-title">Konfigurasi</span>
                <a href="https://fonnte.com" target="_blank" class="btn btn-secondary" style="font-size:11px;padding:5px 10px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Fonnte.com
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('finance.wa-settings.update') }}" id="waForm">
                    @csrf

                    {{-- Token --}}
                    <div class="form-group">
                        <label class="form-label" for="fonnte_token">API Token</label>
                        <div class="token-wrap">
                            <input type="password" name="fonnte_token" id="fonnte_token"
                                   class="form-control token-inp {{ $errors->has('fonnte_token') ? 'is-invalid' : '' }}"
                                   value="{{ old('fonnte_token', $setting->fonnte_token ?? '') }}"
                                   placeholder="Token API Baileys / Fonnte" required>
                            <button type="button" class="token-eye" onclick="toggleToken(this)">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('fonnte_token')<p class="invalid-feedback">{{ $message }}</p>@enderror
                    </div>

                    {{-- Device Number --}}
                    <div class="form-group">
                        <label class="form-label" for="device_number">Nomor Device WA</label>
                        <input type="text" name="device_number" id="device_number"
                               class="form-control" style="font-family:'JetBrains Mono',monospace;"
                               value="{{ old('device_number', $setting->device_number ?? '') }}"
                               placeholder="628xxx (nomor yang terhubung ke Baileys)">
                    </div>

                    <div class="form-divider">Target Notifikasi</div>

                    {{-- Multi-target Input --}}
                    <div class="form-group">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label class="form-label" style="margin:0;">Nomor / Grup Tujuan</label>
                            <span class="tcount-badge" id="targetCount">
                                {{ $totalTargets }} target
                            </span>
                        </div>

                        <div class="target-box" id="targetBox" onclick="focusInput()">
                            <div id="hiddenInputs"></div>
                            <div class="tags-area" id="tagsArea"></div>
                            <div class="add-row">
                                <input type="text" id="targetInput" class="add-input"
                                       placeholder="Ketik nomor/ID grup lalu Enter atau klik Tambah...">
                                <button type="button" class="add-btn" onclick="addTarget()">+ Tambah</button>
                            </div>
                        </div>

                        <div class="type-hint">
                            <span style="font-size:10px;color:var(--muted);align-self:center;">Contoh:</span>
                            <span class="type-chip" onclick="fillExample('6281234567890')">📱 6281234567890</span>
                            <span class="type-chip" onclick="fillExample('120363xxxxxxxx@g.us')">👥 GroupID@g.us</span>
                        </div>
                        <p class="form-hint">Bisa input nomor HP (628xxx / 08xxx) dan/atau ID grup WhatsApp (xxx@g.us)</p>
                    </div>

                    <div class="form-divider">Pengaturan Notif</div>

                    <div class="toggle-section">
                        <div class="toggle-row">
                            <div>
                                <div class="t-title">Notif Transaksi</div>
                                <div class="t-desc">Kirim WA setiap ada transaksi baru</div>
                            </div>
                            <label class="switch">
                                <input type="hidden" name="notify_on_transaction" value="0">
                                <input type="checkbox" name="notify_on_transaction" value="1"
                                       {{ old('notify_on_transaction', $setting->notify_on_transaction ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div>
                                <div class="t-title">Notif Anggaran Kritis</div>
                                <div class="t-desc">Peringatan saat melewati threshold</div>
                            </div>
                            <label class="switch">
                                <input type="hidden" name="notify_on_budget_exceeded" value="0">
                                <input type="checkbox" name="notify_on_budget_exceeded" value="1"
                                       {{ old('notify_on_budget_exceeded', $setting->notify_on_budget_exceeded ?? true) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="budget_alert_threshold">Threshold Anggaran (%)</label>
                        <div class="thresh-wrap">
                            <span class="thresh-prefix">%</span>
                            <input type="number" name="budget_alert_threshold" id="budget_alert_threshold"
                                   class="form-control thresh-inp"
                                   value="{{ old('budget_alert_threshold', $setting->budget_alert_threshold ?? 80) }}"
                                   min="1" max="100">
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Pengaturan
                        </button>
                        <button type="button" class="btn btn-wa" onclick="openTestModal()">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.999 2C6.477 2 2 6.477 2 12c0 1.99.574 3.847 1.574 5.414L2 22l4.586-1.574A9.945 9.945 0 0012 22c5.522 0 10-4.478 10-10S17.521 2 12 2zm0 18c-1.77 0-3.42-.49-4.83-1.34L4 20l1.34-3.17A7.948 7.948 0 014 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.591 8-8 8z"/></svg>
                            Test Kirim ke Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ KANAN: Preview + Log ═══ --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Preview --}}
        <div class="float-in" style="animation-delay:.15s">
            <p style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--muted);margin-bottom:8px;">Preview Pesan Transaksi</p>
            <div class="phone-mockup">
                <div class="phone-head">
                    <div class="phone-avatar">🤖</div>
                    <div>
                        <div class="phone-name">Keuangan Tim</div>
                        <div class="phone-status" id="previewTarget">
                            @if($totalTargets > 0)
                                {{ $totalTargets }} target terdaftar
                            @else
                                Belum ada target
                            @endif
                        </div>
                    </div>
                </div>
                <div class="phone-body">
                    <div class="wa-bubble">🔴 *PENGELUARAN BARU*
━━━━━━━━━━━━━━━━━━━━
📋 Kode     : TRX-2502-001
📂 Kategori : Konsumsi
💸 Jumlah   : Rp 150.000
📅 Tanggal  : 25 Feb 2026
📝 Ket      : ngopi
👤 Dicatat  : Admin Finance
📊 Anggaran : Rp 500.000 (terpakai 30%)
━━━━━━━━━━━━━━━━━━━━
💳 Saldo Kas: Rp 550.000
                        <div class="wa-time">21:32 <span class="wa-check">✓✓</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Log --}}
        <div class="card float-in" style="animation-delay:.2s">
            <div class="card-head">
                <span class="card-title">Log Pengiriman</span>
                <span style="font-size:11px;color:var(--muted);">20 terbaru</span>
            </div>
            @if($logs->count())
                <div class="table-wrap">
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Target</th>
                                <th>Pesan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td style="font-family:'JetBrains Mono',monospace;font-size:10.5px;color:var(--muted);white-space:nowrap;">
                                        {{ $log->created_at->format('d/m H:i') }}
                                    </td>
                                    <td class="log-phone">
                                        {{ str_contains($log->phone, '@g.us') ? '👥 ' : '📱 ' }}{{ $log->phone }}
                                    </td>
                                    <td class="log-msg">{{ Str::limit($log->message, 40) }}</td>
                                    <td>
                                        <span class="log-status {{ $log->status }}">
                                            {{ $log->status === 'sent' ? '✓ Terkirim' : '✗ Gagal' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align:center;padding:32px;color:var(--muted);font-size:13px;">
                    <div style="font-size:28px;margin-bottom:8px;">📭</div>
                    Belum ada log pengiriman
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Test Modal --}}
<div class="modal-backdrop" id="testModal" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="font-size:36px;margin-bottom:10px;">📲</div>
            <div style="font-size:16px;font-weight:700;color:var(--text);">Test Kirim WA</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                Mengirim pesan test ke <span id="modalTargetCount" style="font-weight:700;color:var(--g1);">semua target</span>
            </div>
        </div>

        <div id="testResultsWrap" style="display:none;margin-bottom:14px;max-height:200px;overflow-y:auto;"></div>

        <div style="display:flex;gap:8px;">
            <button onclick="runTest()" class="btn btn-wa" style="flex:1;" id="testBtn">
                Kirim Test Sekarang
            </button>
            <button onclick="closeModal()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── State ── */
let targets = @json($targets);

/* ── Init ── */
function init() {
    renderTags();
    updateHiddenInputs();
    updateCount();
}

/* ── Deteksi tipe target ── */
function detectType(val) {
    if (val.includes('@g.us'))          return 'group';  // Grup Baileys: 120363xxx@g.us
    if (/^628\d{8,13}$/.test(val))      return 'phone';  // Format 628xxx
    if (/^08\d{8,12}$/.test(val))       return 'phone';  // Format 08xxx
    if (/^\d{10,15}$/.test(val))        return 'phone';  // Digit lainnya
    return null;
}

/* ── Normalisasi nomor HP ── */
function normalizePhone(val) {
    if (val.includes('@g.us')) return val;     // grup, kembalikan apa adanya
    val = val.replace(/\D/g, '');              // hapus non-digit
    if (val.startsWith('0'))  val = '62' + val.slice(1);
    if (!val.startsWith('62')) val = '62' + val;
    return val;
}

/* ── Render tags ── */
function renderTags() {
    const area = document.getElementById('tagsArea');
    area.innerHTML = '';
    targets.forEach((t, i) => {
        const isGroup = t.includes('@g.us');
        const div = document.createElement('div');
        div.className = `tag ${isGroup ? 'group' : 'phone'}`;
        div.innerHTML = `
            <span>${isGroup ? '👥' : '📱'}</span>
            <span class="tag-label" title="${t}">${t}</span>
            <span class="tag-del" onclick="removeTarget(${i})">✕</span>
        `;
        area.appendChild(div);
    });
}

/* ── Update hidden inputs ── */
function updateHiddenInputs() {
    const wrap = document.getElementById('hiddenInputs');
    wrap.innerHTML = '';
    targets.forEach((t, i) => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = `target_phones[${i}]`;
        inp.value = t;
        wrap.appendChild(inp);
    });
}

/* ── Count badge ── */
function updateCount() {
    const badge = document.getElementById('targetCount');
    badge.textContent      = targets.length + ' target';
    badge.style.background = targets.length ? 'rgba(0,196,104,0.1)' : 'var(--bg)';
    badge.style.color      = targets.length ? 'var(--accent2)' : 'var(--muted)';

    const preview = document.getElementById('previewTarget');
    if (preview) {
        preview.textContent = targets.length
            ? targets.length + ' target terdaftar'
            : 'Belum ada target';
    }

    const mc = document.getElementById('modalTargetCount');
    if (mc) mc.textContent = targets.length + ' target';
}

/* ── Add target ── */
function addTarget() {
    const inp = document.getElementById('targetInput');
    let val   = inp.value.trim();
    if (!val) { inp.focus(); return; }

    const type = detectType(val);

    if (!type) {
        inp.style.animation = 'shake .3s ease';
        inp.placeholder     = '⚠ Format: 628xxx / 08xxx / GroupID@g.us';
        setTimeout(() => {
            inp.style.animation = '';
            inp.placeholder     = 'Ketik nomor/ID grup lalu Enter atau klik Tambah...';
        }, 2000);
        inp.focus();
        return;
    }

    // Normalisasi nomor HP
    if (type === 'phone') val = normalizePhone(val);

    if (targets.includes(val)) {
        inp.value = '';
        inp.focus();
        return;
    }

    targets.push(val);
    inp.value = '';
    renderTags();
    updateHiddenInputs();
    updateCount();
    inp.focus();
}

/* ── Remove ── */
function removeTarget(idx) {
    targets.splice(idx, 1);
    renderTags();
    updateHiddenInputs();
    updateCount();
}

/* ── Enter key ── */
document.addEventListener('DOMContentLoaded', () => {
    init();
    document.getElementById('targetInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); addTarget(); }
    });
});

function focusInput()     { document.getElementById('targetInput').focus(); }
function fillExample(val) {
    document.getElementById('targetInput').value = val;
    document.getElementById('targetInput').focus();
}

/* ── Token toggle ── */
function toggleToken(btn) {
    const inp = document.getElementById('fonnte_token');
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    btn.querySelector('svg').style.opacity = inp.type === 'text' ? '0.5' : '1';
}

/* ── Test Modal ── */
function openTestModal() {
    document.getElementById('testModal').classList.add('open');
    document.getElementById('testResultsWrap').style.display = 'none';
    document.getElementById('testBtn').disabled  = false;
    document.getElementById('testBtn').innerHTML = 'Kirim Test Sekarang';
}
function closeModal() {
    document.getElementById('testModal').classList.remove('open');
}

async function runTest() {
    const btn  = document.getElementById('testBtn');
    const wrap = document.getElementById('testResultsWrap');

    btn.disabled  = true;
    btn.innerHTML = `<svg style="animation:spin .8s linear infinite;width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mengirim ke ${targets.length} target...`;

    try {
        const resp = await fetch('{{ route("finance.wa-settings.test") }}', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            }
        });
        const data = await resp.json();

        wrap.style.display = 'block';
        wrap.innerHTML     = '';

        if (data.results && data.results.length) {
            data.results.forEach(r => {
                const isGroup = r.phone.includes('@g.us');
                const row     = document.createElement('div');
                row.className = `test-result-row ${r.ok ? 'ok' : 'err'}`;
                row.innerHTML = `
                    <span class="test-phone">${isGroup ? '👥' : '📱'} ${r.phone}</span>
                    <span style="font-size:11px;font-weight:700;color:${r.ok ? '#16a34a' : 'var(--red)'}">
                        ${r.ok ? '✓ Terkirim' : '✗ Gagal'}
                    </span>
                `;
                wrap.appendChild(row);
            });
        } else {
            wrap.innerHTML = `<div class="test-result-row ${data.success ? 'ok' : 'err'}" style="font-size:13px;">${data.message}</div>`;
        }

        btn.innerHTML = data.success ? '✓ Semua Terkirim' : '⚠ Ada yang Gagal';
        btn.disabled  = false;

    } catch(e) {
        wrap.style.display = 'block';
        wrap.innerHTML     = `<div class="test-result-row err">❌ Koneksi gagal: ${e.message}</div>`;
        btn.innerHTML      = 'Coba Lagi';
        btn.disabled       = false;
    }
}

const s = document.createElement('style');
s.textContent = '@keyframes spin{to{transform:rotate(360deg)}} @keyframes shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-5px)}40%,80%{transform:translateX(5px)}}';
document.head.appendChild(s);
</script>
@endpush