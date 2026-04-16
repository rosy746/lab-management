<x-app-layout>
<x-slot name="title">Manajemen Jadwal</x-slot>

<style>
/* ─── VARS ─────────────────────────────────── */
:root {
    --g9:#1A2517;--g8:#2d3d29;--g7:#3d5438;
    --acc:#ACC8A2;--acc2:#8ab87e;
    --border:#e8f0e6;--text:#374151;--muted:#9ca3af;
    --shadow:0 2px 12px rgba(0,0,0,.08);
}

/* ─── ANIMATIONS ────────────────────────────── */
@keyframes modalIn { from{opacity:0;transform:translateY(14px) scale(.97)} to{opacity:1;transform:none} }
@keyframes panelIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
@keyframes shimmer { 0%{background-position:-600px 0} 100%{background-position:600px 0} }

/* ─── STATS ─────────────────────────────────── */
.stat-card { background:#fff;border-radius:14px;padding:18px 20px;border:1px solid var(--border);box-shadow:0 1px 4px rgba(26,37,23,.05); }



/* ─── TABS ──────────────────────────────────── */
.tabs { display:flex;flex-wrap:wrap;gap:7px;margin-bottom:16px; }
.tab-btn {
    display:flex;align-items:center;gap:7px;padding:8px 15px;border-radius:11px;
    font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;
    border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;
    transition:border-color .18s,color .18s,transform .18s,box-shadow .18s,background .18s;white-space:nowrap;
}
.tab-btn:hover:not(.tab-active) { border-color:var(--acc);color:var(--g7);transform:translateY(-1px);box-shadow:0 3px 10px rgba(172,200,162,.18); }
.tab-btn.tab-active { background:linear-gradient(135deg,var(--g9),var(--g8));color:var(--acc);border-color:transparent;box-shadow:0 4px 14px rgba(26,37,23,.28);transform:translateY(-1px); }

/* ─── SKELETON ──────────────────────────────── */
.skeleton-wrap { background:#fff;border-radius:14px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;display:none; }
.skel-head { height:70px;background:linear-gradient(135deg,var(--g9),var(--g8)); }
.skel-body { padding:12px 16px;display:flex;flex-direction:column;gap:6px; }
.skel-row  { display:grid;grid-template-columns:75px repeat(7,1fr);gap:5px; }
.skel-cell { height:56px;border-radius:9px;background:linear-gradient(90deg,#e8f0e6 25%,#f4f8f3 50%,#e8f0e6 75%);background-size:600px 100%;animation:shimmer 1.4s infinite; }
.skel-cell-sm { height:24px; }

/* ─── PANEL ─────────────────────────────────── */
.lab-panel { animation:panelIn .28s cubic-bezier(.16,1,.3,1) both; }
.panel-card { background:#fff;border-radius:14px;box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden; }
.panel-header { display:flex;align-items:center;gap:13px;padding:15px 22px;background:linear-gradient(135deg,var(--g9),var(--g8)); }
.panel-icon { width:38px;height:38px;border-radius:11px;background:rgba(172,200,162,.12);border:1.5px solid rgba(172,200,162,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.panel-name { font-family:'Outfit',sans-serif;font-weight:700;color:#fff;font-size:17px; }
.panel-cap  { font-size:11px;color:rgba(172,200,162,.5);margin-top:2px; }

/* ─── TABLE ─────────────────────────────────── */
.tbl-wrap { overflow-x:auto;-webkit-overflow-scrolling:touch; }
.tbl-wrap::-webkit-scrollbar { height:4px; }
.tbl-wrap::-webkit-scrollbar-thumb { background:var(--acc);border-radius:4px; }
table { width:100%;border-collapse:collapse;min-width:700px;font-size:12px; }
thead tr { background:#f8faf7;border-bottom:2px solid var(--border); }
thead th { padding:9px 7px;text-align:center;font-size:10px;font-weight:700;color:var(--muted);letter-spacing:.1em;text-transform:uppercase; }
thead th.col-time { text-align:left;padding-left:13px;width:78px; }
thead th.th-sun { color:#dc2626; }
tbody tr { border-top:1px solid #f3f4f6;transition:background .12s; }
tbody tr:hover { background:#fafcf9; }
tbody tr:hover .col-time { background:#fafcf9; }
.col-time { padding:9px 9px 9px 13px;position:sticky;left:0;z-index:2;background:#fff;border-right:1px solid #f0f0f0; }
.slot-label { font-family:'Outfit',sans-serif;font-weight:700;color:var(--text);font-size:11px; }
.slot-time  { font-size:10px;color:var(--muted);margin-top:1px; }
td.slot-td { padding:4px 3px;border-right:1px solid #f5f5f5; }
td.slot-td.td-sun { background:rgba(254,226,226,.15); }

/* ─── SLOT CARD ─────────────────────────────── */
.sc {
    border-radius:9px;padding:6px 9px;overflow:hidden;position:relative;
    transition:transform .18s,box-shadow .18s,filter .18s;cursor:pointer;
}
.sc::before {
    content:'';position:absolute;top:0;left:-70%;width:45%;height:100%;
    background:linear-gradient(105deg,transparent,rgba(255,255,255,.2),transparent);
    transform:skewX(-18deg);transition:left .45s ease;pointer-events:none;
}
.sc:hover::before { left:130%; }
.sc:hover { transform:translateY(-2px) scale(1.02);box-shadow:0 6px 16px rgba(0,0,0,.1);filter:brightness(1.04);z-index:4; }
.sc-name    { font-weight:700;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.sc-class   { font-size:10px;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.sc-subject { font-size:10px;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.sc-footer  { display:flex;align-items:center;justify-content:space-between;margin-top:4px; }
.sc-status-dot { font-size:9px;font-weight:700; }

.sc-tetap { background:linear-gradient(135deg,#e0edd9,#cde0c7);border:1.5px solid var(--acc); }
.sc-tetap .sc-name    { color:var(--g9); }
.sc-tetap .sc-class   { color:var(--g7); }
.sc-tetap .sc-subject { color:#7a9475; }

.sc-inactive { background:linear-gradient(135deg,#f3f4f6,#e5e7eb);border:1.5px dashed #9ca3af;opacity:.65; }
.sc-inactive .sc-name    { color:#6b7280; }
.sc-inactive .sc-class   { color:#9ca3af; }

/* Mini action buttons inside card */
.sc-del-btn {
    background:rgba(220,38,38,.1);color:#dc2626;border:none;border-radius:4px;
    padding:2px 5px;font-size:9px;cursor:pointer;transition:background .15s;line-height:1.4;
    flex-shrink:0;
}
.sc-del-btn:hover { background:rgba(220,38,38,.2); }

/* ─── BREAK ROW ─────────────────────────────── */
.break-row td {
    text-align:center;padding:8px;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    background:linear-gradient(135deg,#fef9ec,#fef3c7);color:#92400e;
    border-top:1px solid #fde68a;border-bottom:1px solid #fde68a;
}

/* ─── ADD BUTTON ────────────────────────────── */
.add-btn {
    width:100%;border-radius:9px;padding:13px 3px;background:transparent;cursor:pointer;
    border:1.5px dashed #c8d9c5;
    transition:background .18s,border-color .18s,transform .18s,box-shadow .18s;
    display:flex;flex-direction:column;align-items:center;gap:3px;
}
.add-btn:hover { background:rgba(172,200,162,.08);border-color:var(--acc);transform:scale(1.04);box-shadow:0 3px 12px rgba(172,200,162,.18); }
.add-btn:hover .add-icon { color:var(--acc);transform:rotate(90deg); }
.add-icon { transition:transform .2s,color .2s;color:#d1d5db; }
.add-text  { font-size:10px;font-weight:500;color:var(--muted); }
.add-btn-sun { border-color:#fca5a5; }
.add-btn-sun:hover { background:rgba(248,113,113,.05);border-color:#f87171; }
.add-btn-sun:hover .add-icon { color:#f87171; }

/* ─── BADGE DAY ─────────────────────────────── */
.badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700; }
.day-Senin  { background:#dbeafe;color:#1d4ed8; }
.day-Selasa { background:#f3e8ff;color:#7c3aed; }
.day-Rabu   { background:#dcfce7;color:#15803d; }
.day-Kamis  { background:#fef3c7;color:#92400e; }
.day-Jumat  { background:#fee2e2;color:#dc2626; }
.day-Sabtu  { background:#e0f2fe;color:#0369a1; }
.day-Minggu { background:#fce7f3;color:#be185d; }

/* ─── SWIPE HINT ────────────────────────────── */
.swipe-hint { display:none; }
@media(max-width:768px) {
    .swipe-hint { display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#6b7280;background:#fff;border:1px solid var(--border);border-radius:8px;padding:6px 12px;margin:10px 16px 0;width:fit-content; }
}

/* ─── MODALS ────────────────────────────────── */
.modal-overlay { display:none;position:fixed;inset:0;z-index:50;background:rgba(26,37,23,.82);backdrop-filter:blur(5px);align-items:center;justify-content:center;padding:1rem; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:16px;width:100%;max-width:500px;max-height:93vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.35);animation:modalIn .22s cubic-bezier(.16,1,.3,1); }
.modal-head { padding:18px 22px 14px;flex-shrink:0;background:linear-gradient(135deg,var(--g9),var(--g8)); }
.modal-head-top { display:flex;align-items:flex-start;justify-content:space-between; }
.modal-eyebrow { font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(172,200,162,.55);margin-bottom:3px; }
.modal-title   { font-family:'Outfit',sans-serif;font-weight:700;font-size:19px;color:#fff; }
.modal-close { background:none;border:none;cursor:pointer;color:rgba(172,200,162,.5);padding:4px;border-radius:7px;transition:color .15s,background .15s; }
.modal-close:hover { color:var(--acc);background:rgba(172,200,162,.1); }
.modal-badges { display:flex;flex-wrap:wrap;gap:5px;margin-top:11px; }
.mbadge { font-size:11px;padding:3px 10px;border-radius:999px;font-weight:600;background:rgba(172,200,162,.1);color:var(--acc);border:1px solid rgba(172,200,162,.2);white-space:nowrap; }
.modal-body { flex:1;overflow-y:auto;padding:18px 22px;display:flex;flex-direction:column;gap:13px; }
.field-label { display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px; }
.inp { width:100%;border-radius:9px;border:1.5px solid #e5e7eb;padding:9px 12px;font-size:13px;font-family:inherit;background:#fafcf9;outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box; }
.inp:focus { border-color:var(--acc);box-shadow:0 0 0 3px rgba(172,200,162,.12); }
.inp:disabled { opacity:.5;cursor:not-allowed;background:#f3f4f6; }
.field-row { display:grid;grid-template-columns:1fr 1fr;gap:11px; }
.modal-actions { display:flex;gap:9px;padding-top:3px;padding-bottom:3px; }
.btn-cancel { flex:1;background:#f3f4f6;border:none;color:var(--text);font-weight:700;padding:12px;border-radius:11px;font-size:13px;font-family:inherit;cursor:pointer;transition:background .15s; }
.btn-cancel:hover { background:#e5e7eb; }
.btn-submit { flex:1;background:linear-gradient(135deg,var(--g9),var(--g8));border:none;color:var(--acc);font-weight:700;padding:12px;border-radius:11px;font-size:13px;font-family:inherit;cursor:pointer;box-shadow:0 3px 12px rgba(26,37,23,.25);transition:transform .15s,box-shadow .15s; }
.btn-submit:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(26,37,23,.35); }
.btn-danger { flex:1;background:#dc2626;border:none;color:#fff;font-weight:700;padding:12px;border-radius:11px;font-size:13px;font-family:inherit;cursor:pointer;transition:opacity .15s; }
.btn-danger:hover { opacity:.85; }

@media(max-width:600px) { .field-row { grid-template-columns:1fr; } }
@media(prefers-reduced-motion:reduce) { *,*::before,*::after { animation-duration:.01ms !important;transition-duration:.01ms !important; } }
</style>

{{-- ─── STATS ─── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
    @foreach([
        ['label'=>'Total Jadwal','value'=>$stats['total'],   'color'=>'#6b7280'],
        ['label'=>'Aktif',       'value'=>$stats['active'],  'color'=>'#16a34a'],
        ['label'=>'Nonaktif',    'value'=>$stats['inactive'],'color'=>'#9ca3af'],
    ] as $s)
    <div class="stat-card">
        <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">{{ $s['label'] }}</p>
        <p style="font-size:28px;font-family:Outfit,sans-serif;font-weight:800;color:{{ $s['color'] }}">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Flash --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;color:#166534;background:#f0fdf4;border:1px solid #bbf7d0">✓ {{ session('success') }}</div>
@endif
@if($errors->has('error'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;color:#991b1b;background:#fef2f2;border:1px solid #fecaca">⚠ {{ $errors->first('error') }}</div>
@endif



{{-- ─── LEGEND ─── --}}
<div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:14px;align-items:center">
    <div style="display:flex;align-items:center;gap:7px;padding:5px 11px;border-radius:8px;background:#f8faf7;border:1px solid #e8f0e6">
        <div style="width:13px;height:13px;border-radius:4px;background:linear-gradient(135deg,#d6ead2,#ACC8A2)"></div>
        <span style="font-size:11px;color:#6b7280;font-weight:600">Jadwal Aktif — klik untuk edit</span>
    </div>
    <div style="display:flex;align-items:center;gap:7px;padding:5px 11px;border-radius:8px;background:#f8faf7;border:1px solid #e8f0e6">
        <div style="width:13px;height:13px;border-radius:4px;background:#e5e7eb;border:1.5px dashed #9ca3af"></div>
        <span style="font-size:11px;color:#6b7280;font-weight:600">Nonaktif</span>
    </div>
    <div style="display:flex;align-items:center;gap:7px;padding:5px 11px;border-radius:8px;background:#f8faf7;border:1px solid #e8f0e6">
        <div style="width:13px;height:13px;border-radius:4px;background:transparent;border:2px dashed rgba(172,200,162,.5)"></div>
        <span style="font-size:11px;color:#6b7280;font-weight:600">Kosong — klik untuk tambah</span>
    </div>
</div>

{{-- ─── TABS ─── --}}
<div class="tabs">
    @foreach($resources as $i => $resource)
    <button onclick="switchTab({{ $resource->id }})" id="tab-{{ $resource->id }}"
        class="tab-btn {{ $i === 0 ? 'tab-active' : '' }}">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        {{ $resource->name }}
    </button>
    @endforeach
</div>

{{-- Skeleton --}}
<div class="skeleton-wrap" id="skeleton">
    <div class="skel-head"></div>
    <div class="skel-body">
        <div class="skel-row">
            <div class="skel-cell skel-cell-sm"></div>
            @for($d=0;$d<7;$d++)<div class="skel-cell skel-cell-sm"></div>@endfor
        </div>
        @for($r=0;$r<6;$r++)
        <div class="skel-row">
            <div class="skel-cell" style="animation-delay:{{ $r*30 }}ms"></div>
            @for($d=0;$d<7;$d++)<div class="skel-cell" style="animation-delay:{{ ($r*7+$d)*20 }}ms"></div>@endfor
        </div>
        @endfor
    </div>
</div>

{{-- ─── LAB PANELS ─── --}}
<div id="panels-wrap">
@foreach($resources as $i => $resource)
<div id="panel-{{ $resource->id }}" class="lab-panel" style="{{ $i !== 0 ? 'display:none' : '' }}">
    <div class="panel-card">

        <div class="panel-header">
            <div class="panel-icon">
                <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="panel-name">{{ $resource->name }}</div>
                @if($resource->capacity)
                <div class="panel-cap">Kapasitas {{ $resource->capacity }} komputer</div>
                @endif
            </div>
        </div>

        <div class="swipe-hint">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Geser kiri/kanan untuk semua hari
        </div>

        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="col-time">JAM</th>
                        @foreach($days as $dayEn => $dayId)
                        @php $isSun = $dayId === 'Minggu'; @endphp
                        <th class="{{ $isSun ? 'th-sun' : '' }}">
                            <span class="badge day-{{ $dayId }}" style="font-size:10px;padding:2px 8px">{{ $dayId }}</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $slot)
                    @php $isBreak = $slot->is_break ?? false; @endphp

                    @if($isBreak)
                    <tr class="break-row">
                        <td colspan="{{ count($days) + 1 }}">
                            ☕ ISTIRAHAT · {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                            @if($slot->end_time) – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}@endif
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td class="col-time">
                            <div class="slot-label">{{ $slot->name }}</div>
                            <div class="slot-time">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                @if($slot->end_time)–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}@endif
                            </div>
                        </td>

                        @foreach($days as $dayEn => $dayId)
                        @php
                            $isSun = $dayId === 'Minggu';
                            $sk    = $resource->id.'_'.$dayEn.'_'.$slot->id;
                            $sched = $scheduleGrid->get($sk)?->first();
                        @endphp
                        <td class="slot-td {{ $isSun ? 'td-sun' : '' }}">

                            @if($sched)
                            {{-- Ada jadwal → kartu, klik = edit --}}
                            <div class="sc {{ $sched->status === 'active' ? 'sc-tetap' : 'sc-inactive' }}"
                                onclick="openEdit(
                                    {{ $sched->id }},
                                    '{{ addslashes($sched->teacher_name) }}',
                                    '{{ addslashes($sched->subject_name ?? '') }}',
                                    '{{ addslashes($sched->notes ?? '') }}',
                                    '{{ $sched->status }}',
                                    '{{ addslashes($sched->labClass?->name ?? '-') }}',
                                    '{{ $dayId }}',
                                    '{{ addslashes($slot->name) }}',
                                    '{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}',
                                    '{{ addslashes($resource->name) }}'
                                )">
                                <div class="sc-name">{{ $sched->teacher_name }}</div>
                                <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                                @if($sched->subject_name)<div class="sc-subject">{{ $sched->subject_name }}</div>@endif
                                <div class="sc-footer" onclick="event.stopPropagation()">
                                    <span class="sc-status-dot" style="color:{{ $sched->status === 'active' ? '#7a9475' : '#9ca3af' }}">
                                        {{ $sched->status === 'active' ? '● Aktif' : '○ Nonaktif' }}
                                    </span>
                                    <button class="sc-del-btn"
                                        onclick="confirmDelete({{ $sched->id }}, '{{ addslashes($sched->teacher_name) }}')">
                                        🗑
                                    </button>
                                </div>
                            </div>

                            @else
                            {{-- Kosong → tambah --}}
                            <button class="add-btn {{ $isSun ? 'add-btn-sun' : '' }}"
                                onclick="openAdd(
                                    {{ $resource->id }},
                                    '{{ addslashes($resource->name) }}',
                                    {{ $slot->id }},
                                    '{{ addslashes($slot->name) }}',
                                    '{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}{{ $slot->end_time ? '–'.\Carbon\Carbon::parse($slot->end_time)->format('H:i') : '' }}',
                                    '{{ $dayEn }}',
                                    '{{ $dayId }}'
                                )">
                                <svg class="add-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="add-text">Tambah</span>
                            </button>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
</div>


{{-- ═══ MODAL TAMBAH ═══ --}}
<div id="add-modal" class="modal-overlay" onclick="if(event.target===this)closeAdd()">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">Jadwal Tetap</p>
                    <h2 class="modal-title">Tambah Jadwal</h2>
                </div>
                <button class="modal-close" onclick="closeAdd()">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="mbadge" id="add-b-lab">🖥 -</span>
                <span class="mbadge" id="add-b-day">📅 -</span>
                <span class="mbadge" id="add-b-slot">🕐 -</span>
            </div>
        </div>

        <form method="POST" action="{{ route('schedule.admin.store') }}" class="modal-body">
            @csrf
            <input type="hidden" name="resource_id"  id="add-f-rid">
            <input type="hidden" name="time_slot_id" id="add-f-sid">
            <input type="hidden" name="day_of_week"  id="add-f-day">

            <div>
                <label class="field-label">Unit Sekolah *</label>
                <select name="organization_id" class="inp" required onchange="loadKelasAdd(this.value)" style="appearance:auto">
                    <option value="">— Pilih unit sekolah —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label">Kelas *</label>
                <select name="class_id" id="add-class" class="inp" required disabled style="appearance:auto">
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>

            <div>
                <label class="field-label">Nama Guru *</label>
                <div style="position:relative">
                    <input name="teacher_name" id="add-teacher-inp" type="text"
                           placeholder="Ketik nama guru..." class="inp" required autocomplete="off"
                           oninput="filterTeacher('add-teacher-inp','add-teacher-sug',this.value)">
                    <div id="add-teacher-sug" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #ACC8A2;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:999;max-height:180px;overflow-y:auto;margin-top:3px"></div>
                </div>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label">Mata Pelajaran</label>
                    <input name="subject_name" type="text" placeholder="Contoh: TIK" class="inp">
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <select name="status" class="inp" style="appearance:auto">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="field-label">Catatan</label>
                <input name="notes" type="text" placeholder="Opsional" class="inp">
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeAdd()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">+ Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══ MODAL EDIT ═══ --}}
<div id="edit-modal" class="modal-overlay" onclick="if(event.target===this)closeEdit()">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-top">
                <div>
                    <p class="modal-eyebrow">Edit Jadwal Tetap</p>
                    <h2 class="modal-title" id="edit-modal-title">Edit Jadwal</h2>
                </div>
                <button class="modal-close" onclick="closeEdit()">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-badges">
                <span class="mbadge" id="edit-b-lab">🖥 -</span>
                <span class="mbadge" id="edit-b-day">📅 -</span>
                <span class="mbadge" id="edit-b-slot">🕐 -</span>
            </div>
        </div>

        <form id="edit-form" method="POST" class="modal-body">
            @csrf @method('PATCH')

            <div>
                <label class="field-label">Nama Guru *</label>
                <div style="position:relative">
                    <input type="text" name="teacher_name" id="edit-teacher" class="inp" required
                           autocomplete="off" oninput="filterTeacher('edit-teacher','edit-teacher-sug',this.value)">
                    <div id="edit-teacher-sug" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #ACC8A2;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:999;max-height:180px;overflow-y:auto;margin-top:3px"></div>
                </div>
            </div>

            <div class="field-row">
                <div>
                    <label class="field-label">Mata Pelajaran</label>
                    <input type="text" name="subject_name" id="edit-subject" class="inp">
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <select name="status" id="edit-status" class="inp" style="appearance:auto">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="field-label">Catatan</label>
                <input type="text" name="notes" id="edit-notes" class="inp">
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeEdit()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">✓ Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══ MODAL DELETE ═══ --}}
<div id="delete-modal" class="modal-overlay" onclick="if(event.target===this)closeDelete()">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:380px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.35);animation:modalIn .22s cubic-bezier(.16,1,.3,1)">
        <div style="padding:20px 22px;background:linear-gradient(135deg,#7f1d1d,#991b1b)">
            <h3 style="font-family:Outfit,sans-serif;font-weight:700;font-size:17px;color:#fff;margin:0">Hapus Jadwal?</h3>
            <p id="delete-desc" style="font-size:13px;color:rgba(255,255,255,.6);margin:4px 0 0"></p>
        </div>
        <div style="padding:18px 22px">
            <p style="font-size:13px;color:#6b7280;margin-bottom:18px">Jadwal ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <div class="modal-actions">
                    <button type="button" onclick="closeDelete()" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-danger">🗑 Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
const TEACHERS = @json($teachers);

// ─── TAB SWITCHER ─────────────────────────────────────
function switchTab(id) {
    document.querySelectorAll('.lab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('tab-active'));
    document.getElementById('skeleton').style.display = 'block';
    document.getElementById('tab-' + id).classList.add('tab-active');
    setTimeout(() => {
        document.getElementById('skeleton').style.display = 'none';
        const panel = document.getElementById('panel-' + id);
        panel.style.display = '';
        panel.style.animation = 'none';
        void panel.offsetWidth;
        panel.style.animation = '';
    }, 280);
}

// ─── MODAL TAMBAH ─────────────────────────────────────
function openAdd(rid, rname, sid, sname, stime, dayEn, dayId) {
    document.getElementById('add-f-rid').value = rid;
    document.getElementById('add-f-sid').value = sid;
    document.getElementById('add-f-day').value = dayEn;
    document.getElementById('add-b-lab').textContent  = '🖥 ' + rname;
    document.getElementById('add-b-day').textContent  = '📅 ' + dayId;
    document.getElementById('add-b-slot').textContent = '🕐 ' + sname + ' · ' + stime;
    // reset
    const cls = document.getElementById('add-class');
    cls.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>';
    cls.disabled  = true;
    document.querySelector('#add-modal select[name="organization_id"]').value = '';
    document.getElementById('add-teacher-inp').value = '';
    document.getElementById('add-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeAdd() {
    document.getElementById('add-modal').classList.remove('open');
    document.body.style.overflow = '';
}

// ─── MODAL EDIT ───────────────────────────────────────
function openEdit(id, teacher, subject, notes, status, className, dayId, slotName, slotTime, labName) {
    document.getElementById('edit-form').action = '/jadwal-admin/' + id;
    document.getElementById('edit-modal-title').textContent = teacher;
    document.getElementById('edit-teacher').value = teacher;
    document.getElementById('edit-subject').value = subject;
    document.getElementById('edit-notes').value   = notes;
    document.getElementById('edit-status').value  = status;
    document.getElementById('edit-b-lab').textContent  = '🖥 ' + labName;
    document.getElementById('edit-b-day').textContent  = '📅 ' + dayId + ' · ' + className;
    document.getElementById('edit-b-slot').textContent = '🕐 ' + slotName + ' · ' + slotTime;
    document.getElementById('edit-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEdit() {
    document.getElementById('edit-modal').classList.remove('open');
    document.body.style.overflow = '';
}

// ─── MODAL DELETE ─────────────────────────────────────
function confirmDelete(id, teacher) {
    document.getElementById('delete-form').action = '/jadwal-admin/' + id;
    document.getElementById('delete-desc').textContent = teacher;
    document.getElementById('delete-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDelete() {
    document.getElementById('delete-modal').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeAdd(); closeEdit(); closeDelete(); }
});

// ─── AUTOCOMPLETE GURU ────────────────────────────────
function filterTeacher(inputId, sugId, val) {
    const box = document.getElementById(sugId);
    if (!val || val.length < 2) { box.style.display = 'none'; return; }
    const filtered = TEACHERS.filter(t => t.name.toLowerCase().includes(val.toLowerCase()));
    if (!filtered.length) { box.style.display = 'none'; return; }
    box.innerHTML = filtered.map(t => `
        <div onclick="selectTeacher('${inputId}','${sugId}','${t.name}')"
            style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center"
            onmouseover="this.style.background='#f0f7ee'" onmouseout="this.style.background=''">
            <span style="font-weight:600;color:#1A2517">${t.name}</span>
            ${t.phone ? `<span style="font-size:11px;color:#9ca3af">${t.phone}</span>` : ''}
        </div>`).join('');
    box.style.display = 'block';
}
function selectTeacher(inputId, sugId, name) {
    document.getElementById(inputId).value = name;
    document.getElementById(sugId).style.display = 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('#add-teacher-inp') && !e.target.closest('#add-teacher-sug'))
        document.getElementById('add-teacher-sug').style.display = 'none';
    if (!e.target.closest('#edit-teacher') && !e.target.closest('#edit-teacher-sug'))
        document.getElementById('edit-teacher-sug').style.display = 'none';
});

// ─── LOAD KELAS ───────────────────────────────────────
function loadKelasAdd(orgId) {
    const sel = document.getElementById('add-class');
    if (!orgId) { sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>'; sel.disabled = true; return; }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled  = true;
    fetch('/kelas?organization_id=' + orgId)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            sel.disabled = false;
        })
        .catch(() => { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
}
</script>

</x-app-layout>