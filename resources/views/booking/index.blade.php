<x-app-layout>
<x-slot name="title">Manajemen Booking</x-slot>

<style>
.stat-card { background:#fff; border-radius:14px; padding:18px 20px; border:1px solid #e8f0e6; box-shadow:0 1px 4px rgba(26,37,23,.05); }
.badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700; }
.badge-pending  { background:#fef3c7;color:#92400e; }
.badge-approved { background:#dcfce7;color:#166534; }
.badge-rejected { background:#fee2e2;color:#991b1b; }
.btn-approve { background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;transition:opacity .15s; }
.btn-approve:hover { opacity:.85; }
.btn-reject  { background:#fff;color:#dc2626;border:1.5px solid #fecaca;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s; }
.btn-reject:hover  { background:#fef2f2; }
.btn-delete  { background:#fff;color:#9ca3af;border:1.5px solid #f3f4f6;border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;transition:all .15s; }
.btn-delete:hover  { color:#dc2626;border-color:#fecaca; }
.filter-inp { border:1.5px solid #e5e7eb;border-radius:9px;padding:8px 12px;font-size:13px;background:#fafcf9;outline:none;transition:border-color .15s; }
.filter-inp:focus { border-color:#ACC8A2; }

/* Modal */
#reject-modal { display:none;position:fixed;inset:0;z-index:50;background:rgba(26,37,23,.75);backdrop-filter:blur(5px);align-items:center;justify-content:center;padding:1rem; }
#reject-modal.open { display:flex; }
#reject-box { background:#fff;border-radius:16px;width:100%;max-width:440px;padding:28px;animation:su .2s cubic-bezier(.16,1,.3,1); }
@keyframes su { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none} }
</style>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
    @foreach([
        ['label'=>'Total','value'=>$stats['total'],'color'=>'#6b7280','bg'=>'#f9fafb'],
        ['label'=>'Pending','value'=>$stats['pending'],'color'=>'#d97706','bg'=>'#fffbeb'],
        ['label'=>'Disetujui','value'=>$stats['approved'],'color'=>'#16a34a','bg'=>'#f0fdf4'],
        ['label'=>'Ditolak','value'=>$stats['rejected'],'color'=>'#dc2626','bg'=>'#fef2f2'],
    ] as $s)
    <div class="stat-card">
        <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">{{ $s['label'] }}</p>
        <p style="font-size:28px;font-family:Outfit,sans-serif;font-weight:800;color:{{ $s['color'] }}">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Filter bar --}}
<div style="background:#fff;border-radius:14px;padding:16px 20px;border:1px solid #e8f0e6;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center">
    <form method="GET" action="{{ route('booking.index') }}" style="display:flex;flex-wrap:wrap;gap:10px;width:100%;align-items:center">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="🔍 Cari nama, kelas, judul..."
               class="filter-inp" style="flex:1;min-width:200px">

        <select name="status" class="filter-inp">
            <option value="all" {{ request('status','all')==='all'?'selected':'' }}>Semua Status</option>
            <option value="pending"  {{ request('status')==='pending' ?'selected':'' }}>⏳ Pending</option>
            <option value="approved" {{ request('status')==='approved'?'selected':'' }}>✓ Disetujui</option>
            <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>✗ Ditolak</option>
        </select>

        <select name="resource_id" class="filter-inp">
            <option value="">Semua Lab</option>
            @foreach($resources as $r)
            <option value="{{ $r->id }}" {{ request('resource_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
            @endforeach
        </select>

        <input type="date" name="date" value="{{ request('date') }}" class="filter-inp">

        <button type="submit" style="background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:9px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer">
            Filter
        </button>
        @if(request()->hasAny(['search','status','resource_id','date']))
        <a href="{{ route('booking.index') }}" style="font-size:13px;color:#9ca3af;text-decoration:none">× Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f0f4ee;display:flex;align-items:center;justify-content:space-between">
        <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:15px">
            Daftar Booking
            @if($bookings->total() > 0)
            <span style="font-size:12px;color:#9ca3af;font-weight:400;font-family:DM Sans,sans-serif">({{ $bookings->total() }} data)</span>
            @endif
        </h2>
    </div>

    @if($bookings->isEmpty())
    <div style="text-align:center;padding:48px;color:#9ca3af;font-size:14px">
        <div style="font-size:40px;margin-bottom:12px">📋</div>
        Belum ada booking ditemukan
    </div>
    @else
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f8faf7;border-bottom:1.5px solid #e8f0e6">
                    <th style="padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Tanggal & Lab</th>
                    <th style="padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Pemohon</th>
                    <th style="padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Kegiatan</th>
                    <th style="padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Slot</th>
                    <th style="padding:11px 16px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Status</th>
                    <th style="padding:11px 16px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr style="border-top:1px solid #f5f5f5;transition:background .1s" onmouseover="this.style.background='#fafcf9'" onmouseout="this.style.background=''">

                    {{-- Tanggal & Lab --}}
                    <td style="padding:13px 16px">
                        <div style="font-weight:700;color:#1A2517">
                            {{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}
                        </div>
                        <div style="font-size:11px;color:#ACC8A2;margin-top:2px">🖥 {{ $b->resource->name ?? '-' }}</div>
                    </td>

                    {{-- Pemohon --}}
                    <td style="padding:13px 16px">
                        <div style="font-weight:600;color:#374151">{{ $b->teacher_name }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $b->class_name }} · {{ $b->teacher_phone }}</div>
                    </td>

                    {{-- Kegiatan --}}
                    <td style="padding:13px 16px;max-width:200px">
                        <div style="font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $b->title }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $b->subject_name }} · {{ $b->participant_count }} peserta</div>
                    </td>

                    {{-- Slot --}}
                    <td style="padding:13px 16px">
                        @if($b->timeSlot)
                        <div style="font-size:12px;color:#374151;font-weight:600">{{ $b->timeSlot->name }}</div>
                        <div style="font-size:11px;color:#9ca3af">
                            {{ \Carbon\Carbon::parse($b->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($b->timeSlot->end_time)->format('H:i') }}
                        </div>
                        @else
                        <span style="color:#d1d5db">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="padding:13px 16px;text-align:center">
                        @if($b->status === 'pending')
                            <span class="badge badge-pending">⏳ Pending</span>
                        @elseif($b->status === 'approved')
                            <span class="badge badge-approved">✓ Disetujui</span>
                            @if($b->approved_at)
                            <div style="font-size:10px;color:#9ca3af;margin-top:3px">{{ \Carbon\Carbon::parse($b->approved_at)->format('d/m H:i') }}</div>
                            @endif
                        @else
                            <span class="badge badge-rejected">✗ Ditolak</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td style="padding:13px 16px;text-align:center">
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                            @if($b->status === 'pending')
                            @php
                                $groupCount = $bookings->where('teacher_name', $b->teacher_name)
                                    ->where('resource_id', $b->resource_id)
                                    ->where('booking_date', $b->booking_date)
                                    ->where('status', 'pending')
                                    ->count();
                            @endphp
                            {{-- Approve Semua jika ada lebih dari 1 slot --}}
                            @if($groupCount > 1)
                            <form method="POST" action="{{ route('booking.approve.group') }}" onsubmit="return confirm('Setujui semua {{ $groupCount }} slot booking {{ $b->teacher_name }} sekaligus?')">
                                @csrf
                                <input type="hidden" name="teacher_name" value="{{ $b->teacher_name }}">
                                <input type="hidden" name="resource_id" value="{{ $b->resource_id }}">
                                <input type="hidden" name="booking_date" value="{{ $b->booking_date }}">
                                <button type="submit" class="btn-approve" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);white-space:nowrap">✓ Setujui {{ $groupCount }} Slot</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('booking.approve', $b->id) }}" onsubmit="return confirm('Setujui booking ini?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-approve">✓ Setujui</button>
                            </form>
                            @endif
                            {{-- Reject --}}
                            <button class="btn-reject" onclick="openReject({{ $b->id }}, '{{ addslashes($b->title) }}')">✗ Tolak</button>
                            @else
                            {{-- Detail --}}
                            <a href="{{ route('booking.show', $b->id) }}"
                               style="background:#f3f4f6;color:#374151;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;text-decoration:none;display:inline-block">
                               Detail
                            </a>
                            @endif
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('booking.destroy', $b->id) }}" onsubmit="return confirm('Hapus booking ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Hapus">
                                    <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

    {{-- Pagination --}}
    @if($bookings->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f0f4ee">
        {{ $bookings->links() }}
    </div>
    @endif
    @endif
</div>

{{-- REJECT MODAL --}}
<div id="reject-modal" onclick="if(event.target===this)closeReject()">
    <div id="reject-box">
        <h3 style="font-family:Outfit,sans-serif;font-weight:700;font-size:17px;color:#1A2517;margin-bottom:4px">Tolak Booking</h3>
        <p id="reject-title" style="font-size:13px;color:#9ca3af;margin-bottom:18px"></p>

        <form id="reject-form" method="POST">
            @csrf @method('PATCH')
            <label style="display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px">
                Alasan Penolakan *
            </label>
            <textarea name="notes" rows="3" required
                style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 13px;font-size:13px;font-family:DM Sans,sans-serif;outline:none;resize:none;box-sizing:border-box;transition:border-color .15s"
                onfocus="this.style.borderColor='#ACC8A2'" onblur="this.style.borderColor='#e5e7eb'"
                placeholder="Contoh: Slot sudah terpakai untuk kegiatan lain..."></textarea>
            <div style="display:flex;gap:10px;margin-top:16px">
                <button type="button" onclick="closeReject()"
                    style="flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer">
                    Batal
                </button>
                <button type="submit"
                    style="flex:1;background:#dc2626;color:#fff;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer">
                    ✗ Tolak Booking
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReject(id, title) {
    document.getElementById('reject-title').textContent = '"' + title + '"';
    document.getElementById('reject-form').action = '/booking/' + id + '/reject';
    document.getElementById('reject-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeReject() {
    document.getElementById('reject-modal').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeReject(); });
</script>

</x-app-layout>