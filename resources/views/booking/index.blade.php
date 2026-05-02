<x-app-layout>
<x-slot name="title">Manajemen Booking</x-slot>

<style>
/* ─── VARS ───────────────────────────────────── */
:root {
    --g9:#1A2517;--g8:#2d3d29;--g7:#3d5438;
    --acc:#ACC8A2;--acc2:#8ab87e;
    --border:#e8f0e6;--text:#374151;--muted:#9ca3af;
    --shadow:0 2px 12px rgba(0,0,0,.08);
    --pending-bg:#fffbeb;  --pending-c:#d97706;  --pending-b:#fde68a;
    --approve-bg:#f0fdf4;  --approve-c:#15803d;  --approve-b:#bbf7d0;
    --reject-bg:#fef2f2;   --reject-c:#dc2626;   --reject-b:#fecaca;
}

/* ─── ANIMATIONS ─────────────────────────────── */
@keyframes fadeUp   { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:none} }
@keyframes modalIn  { from{opacity:0;transform:translateY(16px) scale(.97)} to{opacity:1;transform:none} }
@keyframes pulse    { 0%,100%{opacity:1} 50%{opacity:.5} }

/* ─── STATS ──────────────────────────────────── */
.stat-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px; }
.stat-card {
    background:#fff;border-radius:16px;padding:18px 20px;
    border:1px solid var(--border);box-shadow:0 1px 6px rgba(26,37,23,.05);
    position:relative;overflow:hidden;
    animation:fadeUp .4s cubic-bezier(.16,1,.3,1) both;
}
.stat-card::after {
    content:'';position:absolute;bottom:-18px;right:-18px;
    width:64px;height:64px;border-radius:50%;opacity:.06;
}
.stat-card:nth-child(1)::after { background:#6b7280; }
.stat-card:nth-child(2)::after { background:#d97706; }
.stat-card:nth-child(3)::after { background:#16a34a; }
.stat-card:nth-child(4)::after { background:#dc2626; }
.stat-label { font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px; }
.stat-val   { font-size:30px;font-family:'Outfit',sans-serif;font-weight:800;line-height:1; }

/* ─── FILTER ─────────────────────────────────── */
.filter-bar {
    background:#fff;border-radius:14px;padding:13px 16px;
    border:1px solid var(--border);margin-bottom:16px;
    display:flex;flex-wrap:wrap;gap:9px;align-items:center;
}
.search-container { position:relative;flex:1;min-width:200px; }
.search-clear {
    position:absolute;right:10px;top:50%;transform:translateY(-50%);
    width:20px;height:20px;display:flex;align-items:center;justify-content:center;
    background:#e5e7eb;color:#6b7280;border-radius:50%;font-size:14px;
    cursor:pointer;opacity:0;transition:all .2s;z-index:2;
}
.search-container:hover .search-clear { opacity:1; }
.search-clear:hover { background:#d1d5db;color:var(--g9); }

.filter-inp {
    border:1.5px solid #e5e7eb;border-radius:9px;padding:8px 12px;
    font-size:13px;background:#fafcf9;outline:none;
    transition:border-color .15s;font-family:inherit;width:100%;
}
.filter-inp:focus { border-color:var(--acc);background:#fff; }
.btn-filter {
    background:linear-gradient(135deg,var(--g9),var(--g8));color:var(--acc);
    border:none;border-radius:9px;padding:9px 22px;font-size:13px;
    font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;
}
.btn-filter:hover { opacity:.9;transform:translateY(-1px);box-shadow:0 4px 12px rgba(26,37,23,.15); }

/* ─── TABLE WRAPPER ──────────────────────────── */
.table-card {
    background:#fff;border-radius:14px;border:1px solid var(--border);
    box-shadow:var(--shadow);overflow:hidden;
}
.table-head-bar {
    padding:15px 20px;border-bottom:1px solid #f0f4ee;
    display:flex;align-items:center;justify-content:space-between;
}
.table-title { font-family:'Outfit',sans-serif;font-weight:700;color:var(--g9);font-size:15px; }
.table-count { font-size:12px;color:var(--muted);font-weight:400;font-family:'DM Sans',sans-serif; }
.tbl-scroll  { overflow-x:auto;-webkit-overflow-scrolling:touch;max-height:calc(100vh - 350px); }
.tbl-scroll::-webkit-scrollbar { height:6px;width:6px; }
.tbl-scroll::-webkit-scrollbar-thumb { background:#e5e7eb;border-radius:10px; }
.tbl-scroll::-webkit-scrollbar-thumb:hover { background:var(--acc); }

table { width:100%;border-collapse:separate;border-spacing:0;font-size:13px; }
thead { position:sticky;top:0;z-index:10; }
thead tr { background:#f8faf7; }
thead th {
    padding:12px 14px;text-align:left;
    font-size:10px;font-weight:700;color:var(--muted);
    text-transform:uppercase;letter-spacing:.09em;white-space:nowrap;
    border-bottom:2px solid var(--border);background:#f8faf7;
}
thead th.th-center { text-align:center; }
tbody tr { border-top:1px solid #f5f5f5;transition:background .12s; }
tbody tr:hover { background:#fafcf9; }
td { padding:13px 14px;vertical-align:middle; }
td.td-center { text-align:center; }

/* ─── ROW HIGHLIGHT by STATUS ────────────────── */
tbody tr.row-pending  { border-left:3px solid var(--pending-c); }
tbody tr.row-approved { border-left:3px solid var(--approve-c); }
tbody tr.row-rejected { border-left:3px solid #d1d5db;opacity:.75; }

/* ─── BADGE ──────────────────────────────────── */
.badge {
    display:inline-flex;align-items:center;gap:4px;
    padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;
}
.badge-pending  { background:var(--pending-bg);color:var(--pending-c);border:1px solid var(--pending-b); }
.badge-approved { background:var(--approve-bg);color:var(--approve-c);border:1px solid var(--approve-b); }
.badge-rejected { background:var(--reject-bg);color:var(--reject-c);border:1px solid var(--reject-b); }
.badge-pending .dot { width:6px;height:6px;border-radius:50%;background:currentColor;animation:pulse 1.6s ease-in-out infinite; }

/* ─── INFO CELLS ─────────────────────────────── */
.cell-primary   { font-weight:700;color:var(--g9);font-size:13px; }
.cell-secondary { font-size:11px;color:var(--muted);margin-top:2px; }
.cell-accent    { font-size:11px;color:var(--acc2);font-weight:600;margin-top:2px; }
.cell-slot      { font-weight:700;color:var(--text);font-size:12px; }
.cell-time      { font-size:10px;color:var(--muted);margin-top:2px; }

/* ─── ACTION BUTTONS ─────────────────────────── */
.act-wrap {
    display:flex;align-items:center;justify-content:center;gap:5px;flex-wrap:wrap;
    opacity:.3;transition:opacity .2s cubic-bezier(.4,0,.2,1);
}
tbody tr:hover .act-wrap { opacity:1; }

.btn-approve-single {
    display:inline-flex;align-items:center;gap:4px;
    background:linear-gradient(135deg,var(--g9),var(--g8));color:var(--acc);
    border:none;border-radius:8px;padding:6px 12px;font-size:11px;font-weight:700;
    cursor:pointer;font-family:inherit;transition:transform .15s,box-shadow .15s;white-space:nowrap;
}
.btn-approve-single:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(26,37,23,.25); }

.btn-approve-group {
    display:inline-flex;align-items:center;gap:4px;
    background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#bfdbfe;
    border:none;border-radius:8px;padding:6px 12px;font-size:11px;font-weight:700;
    cursor:pointer;font-family:inherit;transition:transform .15s,box-shadow .15s;white-space:nowrap;
}
.btn-approve-group:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(37,99,235,.3); }

.btn-reject-act {
    display:inline-flex;align-items:center;gap:4px;
    background:#fff;color:#dc2626;border:1.5px solid #fecaca;
    border-radius:8px;padding:5px 10px;font-size:11px;font-weight:700;
    cursor:pointer;font-family:inherit;transition:background .15s,transform .15s;white-space:nowrap;
}
.btn-reject-act:hover { background:#fef2f2;transform:translateY(-1px); }

.btn-detail {
    display:inline-flex;align-items:center;gap:3px;
    background:#f3f4f6;color:#374151;border:1.5px solid #e5e7eb;
    border-radius:8px;padding:5px 11px;font-size:11px;font-weight:600;
    text-decoration:none;transition:border-color .15s,background .15s;white-space:nowrap;
}
.btn-detail:hover { border-color:var(--acc);background:#f0f9f4;color:var(--g7); }

.btn-del {
    background:#fff;color:#d1d5db;border:1.5px solid #f3f4f6;
    border-radius:8px;padding:5px 8px;cursor:pointer;
    transition:color .15s,border-color .15s,background .15s;
}
.btn-del:hover { color:#dc2626;border-color:#fecaca;background:#fef2f2; }

/* ─── PAGINATION ─────────────────────────────── */
.pagi-wrap { padding:13px 20px;border-top:1px solid #f0f4ee; }

/* ─── EMPTY ──────────────────────────────────── */
.empty-state { text-align:center;padding:56px 20px;color:var(--muted);font-size:14px; }
.empty-icon  { font-size:44px;margin-bottom:12px; }

/* ─── FLASH ──────────────────────────────────── */
.flash { padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px; }
.flash-ok  { color:#166534;background:#f0fdf4;border:1px solid #bbf7d0; }
.flash-err { color:#991b1b;background:#fef2f2;border:1px solid #fecaca; }

/* ─── MODAL ──────────────────────────────────── */
.modal-overlay {
    display:none;position:fixed;inset:0;z-index:50;
    background:rgba(26,37,23,.82);backdrop-filter:blur(5px);
    align-items:center;justify-content:center;padding:1rem;
}
.modal-overlay.open { display:flex; }
.modal-box {
    background:#fff;border-radius:16px;width:100%;max-width:440px;
    overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);
    animation:modalIn .22s cubic-bezier(.16,1,.3,1);
}
.modal-head { padding:20px 22px 16px;background:linear-gradient(135deg,#7f1d1d,#991b1b); }
.modal-head-title { font-family:'Outfit',sans-serif;font-weight:700;font-size:18px;color:#fff;margin:0; }
.modal-head-sub   { font-size:12px;color:rgba(255,255,255,.5);margin-top:3px; }
.modal-body { padding:20px 22px; }
.field-label { display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px; }
.inp-textarea {
    width:100%;border:1.5px solid #e5e7eb;border-radius:10px;
    padding:10px 13px;font-size:13px;font-family:inherit;outline:none;
    resize:none;box-sizing:border-box;transition:border-color .15s;background:#fafcf9;
}
.inp-textarea:focus { border-color:var(--acc);box-shadow:0 0 0 3px rgba(172,200,162,.12); }
.modal-actions { display:flex;gap:9px;margin-top:16px; }
.btn-cancel { flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:11px;padding:12px;font-size:13px;font-weight:700;cursor:pointer;transition:background .15s;font-family:inherit; }
.btn-cancel:hover { background:#e5e7eb; }
.btn-reject-submit { flex:1;background:#dc2626;color:#fff;border:none;border-radius:11px;padding:12px;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .15s;font-family:inherit; }
.btn-reject-submit:hover { opacity:.85; }

@media(max-width:768px) {
    .stat-grid { grid-template-columns:repeat(2,1fr); }
    .act-wrap  { flex-direction:column;align-items:stretch; }
}
</style>

<div style="padding:24px">

{{-- Flash --}}
@if(session('success'))
<div class="flash flash-ok">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash flash-err">⚠ {{ session('error') }}</div>
@endif

{{-- ─── STATS ─── --}}
<div class="stat-grid">
    @foreach([
        ['label'=>'Total',    'value'=>$stats['total'],    'color'=>'#6b7280'],
        ['label'=>'Pending',  'value'=>$stats['pending'],  'color'=>'#d97706'],
        ['label'=>'Disetujui','value'=>$stats['approved'], 'color'=>'#16a34a'],
        ['label'=>'Ditolak',  'value'=>$stats['rejected'], 'color'=>'#dc2626'],
    ] as $i => $s)
    <div class="stat-card" style="animation-delay:{{ $i*60 }}ms">
        <div class="stat-label">{{ $s['label'] }}</div>
        <div class="stat-val" style="color:{{ $s['color'] }}">{{ $s['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- ─── FILTER ─── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('booking.index') }}"
          style="display:flex;flex-wrap:wrap;gap:9px;width:100%;align-items:center">

        <div class="search-container">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍 Cari nama, kelas, judul..."
                   class="filter-inp" id="booking-search">
            @if(request('search'))
            <span class="search-clear" onclick="document.getElementById('booking-search').value='';this.closest('form').submit()">×</span>
            @endif
        </div>

        <div style="flex:0 0 auto">
            <select name="status" class="filter-inp" style="width:auto">
                <option value="all" {{ request('status','all')==='all'?'selected':'' }}>Semua Status</option>
                <option value="pending"  {{ request('status')==='pending' ?'selected':'' }}>⏳ Pending</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>✓ Disetujui</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>✗ Ditolak</option>
            </select>
        </div>

        <div style="flex:0 0 auto">
            <select name="resource_id" class="filter-inp" style="width:auto">
                <option value="">Semua Lab</option>
                @foreach($resources as $r)
                <option value="{{ $r->id }}" {{ request('resource_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex:0 0 auto">
            <input type="date" name="date" value="{{ request('date') }}" class="filter-inp" style="width:auto">
        </div>

        <button type="submit" class="btn-filter">Filter</button>

        @if(request()->hasAny(['search','status','resource_id','date']))
        <a href="{{ route('booking.index') }}"
           style="font-size:12px;color:var(--muted);text-decoration:none;padding:4px 8px;font-weight:600">Reset</a>
        @endif
    </form>
</div>

{{-- ─── SUNDAY BOOKINGS ─── --}}
@if($sundayBookings->total() > 0)
<div class="table-card" style="margin-bottom:28px;border:2px solid #3b82f6;box-shadow:0 10px 25px rgba(59,130,246,.12)">
    <div class="table-head-bar" style="background:#3b82f6;color:#fff;padding:12px 20px">
        <div>
            <span class="table-title" style="color:#fff">📅 Permintaan Booking Minggu (Full Day)</span>
            <span class="table-count" style="color:rgba(255,255,255,.8)">&nbsp;({{ $sundayBookings->total() }} data)</span>
        </div>
        <span class="badge" style="background:rgba(255,255,255,.2);color:#fff">KHUSUS MINGGU</span>
    </div>
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th style="background:#f8fafb">Tanggal & Lab</th>
                    <th style="background:#f8fafb">Pemohon</th>
                    <th style="background:#f8fafb">Kegiatan</th>
                    <th style="background:#f8fafb" class="th-center">Status</th>
                    <th style="background:#f8fafb" class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sundayBookings as $sb)
                <tr class="{{ $sb->status === 'pending' ? 'row-pending' : ($sb->status === 'approved' ? 'row-approved' : 'row-rejected') }}">
                    <td>
                        <div class="cell-primary">{{ $sb->booking_date->translatedFormat('d M Y') }}</div>
                        <div class="cell-accent" style="color:#2563eb">🖥 {{ $sb->resource->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $sb->teacher_name }}</div>
                        <div class="cell-secondary">{{ $sb->organization->name ?? '-' }}</div>
                        <div class="cell-secondary">📱 {{ $sb->teacher_phone }}</div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $sb->class_name }}</div>
                        <div class="cell-secondary">{{ $sb->title }}</div>
                    </td>
                    <td class="td-center">
                        <span class="badge badge-{{ $sb->status }}">
                            @if($sb->status==='pending')<span class="dot"></span>@endif
                            {{ ucfirst($sb->status) }}
                        </span>
                    </td>
                    <td class="td-center">
                        <div class="act-wrap">
                            @if($sb->status === 'pending')
                                <form method="POST" action="{{ route('booking.approve.sunday', $sb->id) }}"
                                      onsubmit="return confirm('Setujui booking minggu ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-approve-single" style="background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff">✓ Setujui</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openReject({{ $sb->id }}, '{{ addslashes($sb->title) }}', '{{ addslashes($sb->teacher_name) }}', 'sunday')">
                                    ✗ Tolak
                                </button>
                            @endif
                            <form method="POST" action="{{ route('booking.destroy.sunday', $sb->id) }}"
                                  onsubmit="return confirm('Hapus booking minggu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($sundayBookings->hasPages())
    <div class="pagi-wrap" style="background:#fff">{{ $sundayBookings->links() }}</div>
    @endif
</div>
@endif

{{-- ─── TABLE ─── --}}
<div class="table-card">
    <div class="table-head-bar">
        <div>
            <span class="table-title">Daftar Booking</span>
            @if($bookings->total() > 0)
            <span class="table-count">&nbsp;({{ $bookings->total() }} data)</span>
            @endif
        </div>
        @if($stats['pending'] > 0)
        <span class="badge badge-pending">
            <span class="dot"></span>
            {{ $stats['pending'] }} menunggu persetujuan
        </span>
        @endif
    </div>

    @if($bookings->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        Belum ada booking ditemukan
    </div>
    @else
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th>Tanggal & Lab</th>
                    <th>Pemohon</th>
                    <th>Kegiatan</th>
                    <th>Slot Waktu</th>
                    <th class="th-center">Status</th>
                    <th class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                @php
                    $rowClass = match($b->status) {
                        'pending'  => 'row-pending',
                        'approved' => 'row-approved',
                        default    => 'row-rejected',
                    };
                    $groupCount = $b->status === 'pending'
                        ? $bookings->where('teacher_name', $b->teacher_name)
                            ->where('resource_id', $b->resource_id)
                            ->where('booking_date', $b->booking_date)
                            ->where('status', 'pending')->count()
                        : 0;
                @endphp
                <tr class="{{ $rowClass }}">

                    {{-- Tanggal & Lab --}}
                    <td>
                        <div class="cell-primary">
                            {{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}
                        </div>
                        <div class="cell-accent">🖥 {{ $b->resource->name ?? '-' }}</div>
                    </td>

                    {{-- Pemohon --}}
                    <td>
                        <div class="cell-primary">{{ $b->teacher_name }}</div>
                        <div class="cell-secondary">{{ $b->class_name }}</div>
                        @if($b->teacher_phone)
                        <div class="cell-secondary">📱 {{ $b->teacher_phone }}</div>
                        @endif
                    </td>

                    {{-- Kegiatan --}}
                    <td style="max-width:190px">
                        <div class="cell-primary" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $b->title }}
                        </div>
                        <div class="cell-secondary">
                            {{ $b->subject_name ?? '-' }}
                            @if($b->participant_count)
                            · <span style="font-weight:600;color:#6b7280">{{ $b->participant_count }} peserta</span>
                            @endif
                        </div>
                    </td>

                    {{-- Slot --}}
                    <td>
                        @if($b->timeSlot)
                        <div class="cell-slot">{{ $b->timeSlot->name }}</div>
                        <div class="cell-time">
                            {{ \Carbon\Carbon::parse($b->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($b->timeSlot->end_time)->format('H:i') }}
                        </div>
                        @else
                        <span style="color:#d1d5db">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="td-center">
                        @if($b->status === 'pending')
                            <span class="badge badge-pending"><span class="dot"></span>Pending</span>
                        @elseif($b->status === 'approved')
                            <span class="badge badge-approved">✓ Disetujui</span>
                            @if($b->approved_at)
                            <div style="font-size:10px;color:var(--muted);margin-top:3px">
                                {{ \Carbon\Carbon::parse($b->approved_at)->format('d/m H:i') }}
                            </div>
                            @endif
                        @else
                            <span class="badge badge-rejected">✗ Ditolak</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="td-center">
                        <div class="act-wrap">
                            @if($b->status === 'pending')

                                @if($groupCount > 1)
                                {{-- Approve Group --}}
                                <form method="POST" action="{{ route('booking.approve.group') }}"
                                      onsubmit="return confirm('Setujui semua {{ $groupCount }} slot booking {{ $b->teacher_name }} sekaligus?')">
                                    @csrf
                                    <input type="hidden" name="teacher_name" value="{{ $b->teacher_name }}">
                                    <input type="hidden" name="resource_id"  value="{{ $b->resource_id }}">
                                    <input type="hidden" name="booking_date" value="{{ $b->booking_date }}">
                                    <button type="submit" class="btn-approve-group">
                                        ✓ {{ $groupCount }} Slot
                                    </button>
                                </form>
                                @else
                                {{-- Approve Single --}}
                                <form method="POST" action="{{ route('booking.approve', $b->id) }}"
                                      onsubmit="return confirm('Setujui booking ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-approve-single">✓ Setujui</button>
                                </form>
                                @endif

                                {{-- Reject --}}
                                <button class="btn-reject-act"
                                    onclick="openReject({{ $b->id }}, '{{ addslashes($b->title) }}', '{{ addslashes($b->teacher_name) }}')">
                                    ✗ Tolak
                                </button>

                            @else
                                {{-- Detail --}}
                                <a href="{{ route('booking.show', $b->id) }}" class="btn-detail">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            @endif

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('booking.destroy', $b->id) }}"
                                  onsubmit="return confirm('Hapus booking ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
    <div class="pagi-wrap">{{ $bookings->links() }}</div>
    @endif
    @endif
</div>

</div>


{{-- ═══ MODAL REJECT ═══ --}}
<div id="reject-modal" class="modal-overlay" onclick="if(event.target===this)closeReject()">
    <div class="modal-box">
        <div class="modal-head">
            <h3 class="modal-head-title">Tolak Booking</h3>
            <p id="reject-subtitle" class="modal-head-sub"></p>
        </div>
        <div class="modal-body">
            <form id="reject-form" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="type" id="reject-type" value="regular">
                <label class="field-label">Alasan Penolakan *</label>
                <textarea name="notes" rows="3" required class="inp-textarea"
                    placeholder="Contoh: Slot sudah terpakai untuk kegiatan lain..."></textarea>
                <div class="modal-actions">
                    <button type="button" onclick="closeReject()" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-reject-submit">✗ Tolak Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReject(id, title, teacher, type = 'regular') {
    document.getElementById('reject-subtitle').textContent = teacher + ' — ' + title;
    document.getElementById('reject-form').action = '/booking/' + id + '/reject';
    document.getElementById('reject-type').value = type;
    document.getElementById('reject-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeReject() {
    document.getElementById('reject-modal').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeReject(); });
</script>

</x-app-layout>