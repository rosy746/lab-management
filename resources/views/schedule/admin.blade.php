<x-app-layout>
<x-slot name="title">Manajemen Jadwal</x-slot>

<style>
.badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700; }
.badge-active   { background:#dcfce7;color:#166534; }
.badge-inactive { background:#f3f4f6;color:#6b7280; }
.filter-inp { border:1.5px solid #e5e7eb;border-radius:9px;padding:8px 12px;font-size:13px;background:#fafcf9;outline:none;transition:border-color .15s;font-family:inherit; }
.filter-inp:focus { border-color:#ACC8A2; }
.inp { width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:13px;background:#fafcf9;outline:none;transition:border-color .15s;box-sizing:border-box;font-family:inherit; }
.inp:focus { border-color:#ACC8A2;box-shadow:0 0 0 3px rgba(172,200,162,.12); }
.btn-primary { background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:9px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-sm-edit { background:#f0f9f4;color:#166534;border:1.5px solid #bbf7d0;border-radius:7px;padding:5px 11px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s; }
.btn-sm-edit:hover { background:#dcfce7; }
.btn-sm-del  { background:#fff;color:#9ca3af;border:1.5px solid #f3f4f6;border-radius:7px;padding:5px 9px;font-size:11px;cursor:pointer;transition:all .15s; }
.btn-sm-del:hover  { color:#dc2626;border-color:#fecaca;background:#fef2f2; }

/* Day color pills */
.day-Senin    { background:#dbeafe;color:#1d4ed8; }
.day-Selasa   { background:#f3e8ff;color:#7c3aed; }
.day-Rabu     { background:#dcfce7;color:#15803d; }
.day-Kamis    { background:#fef3c7;color:#92400e; }
.day-Jumat    { background:#fee2e2;color:#dc2626; }
.day-Sabtu    { background:#e0f2fe;color:#0369a1; }
.day-Minggu   { background:#fce7f3;color:#be185d; }

/* Modals */
.modal-overlay { display:none;position:fixed;inset:0;z-index:50;background:rgba(26,37,23,.75);backdrop-filter:blur(5px);align-items:center;justify-content:center;padding:1rem; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:16px;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;animation:su .2s cubic-bezier(.16,1,.3,1); }
@keyframes su { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none} }
.modal-header { padding:20px 24px 16px;background:linear-gradient(135deg,#1A2517,#2d3d29);border-radius:16px 16px 0 0; }
.modal-body { padding:20px 24px; }
.form-group { margin-bottom:14px; }
.form-label { display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px; }
</style>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
    @foreach([
        ['label'=>'Total Jadwal','value'=>$stats['total'],  'color'=>'#6b7280'],
        ['label'=>'Aktif',       'value'=>$stats['active'], 'color'=>'#16a34a'],
        ['label'=>'Nonaktif',    'value'=>$stats['inactive'],'color'=>'#9ca3af'],
    ] as $s)
    <div style="background:#fff;border-radius:14px;padding:18px 20px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05)">
        <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">{{ $s['label'] }}</p>
        <p style="font-size:28px;font-family:Outfit,sans-serif;font-weight:800;color:{{ $s['color'] }}">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Flash --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;color:#166534;background:#f0fdf4;border:1px solid #bbf7d0">
    ✓ {{ session('success') }}
</div>
@endif
@if($errors->has('error'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;color:#991b1b;background:#fef2f2;border:1px solid #fecaca">
    ⚠ {{ $errors->first('error') }}
</div>
@endif

{{-- Toolbar --}}
<div style="background:#fff;border-radius:14px;padding:14px 18px;border:1px solid #e8f0e6;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center">
    <form method="GET" action="{{ route('schedule.admin') }}" style="display:flex;flex-wrap:wrap;gap:10px;flex:1;align-items:center">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari guru, mapel..." class="filter-inp" style="flex:1;min-width:160px">
        <select name="resource_id" class="filter-inp">
            <option value="">Semua Lab</option>
            @foreach($resources as $r)
            <option value="{{ $r->id }}" {{ request('resource_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
            @endforeach
        </select>
        <select name="day" class="filter-inp">
            <option value="">Semua Hari</option>
            @foreach($days as $en => $id)
            <option value="{{ $en }}" {{ request('day')===$en?'selected':'' }}>{{ $id }}</option>
            @endforeach
        </select>
        <select name="status" class="filter-inp">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status')==='active'  ?'selected':'' }}>Aktif</option>
            <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Nonaktif</option>
        </select>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','resource_id','day','status']))
        <a href="{{ route('schedule.admin') }}" style="font-size:13px;color:#9ca3af;text-decoration:none">× Reset</a>
        @endif
    </form>
    <button onclick="openAdd()" class="btn-primary" style="background:linear-gradient(135deg,#ACC8A2,#8ab87e);color:#1A2517;flex-shrink:0">
        + Tambah Jadwal
    </button>
</div>

{{-- Table --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden">
    <div style="padding:14px 20px;border-bottom:1px solid #f0f4ee;display:flex;align-items:center;justify-content:space-between">
        <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:15px">
            Daftar Jadwal Tetap
            <span style="font-size:12px;color:#9ca3af;font-weight:400;font-family:DM Sans,sans-serif">({{ $schedules->count() }} jadwal)</span>
        </h2>
    </div>

    @if($schedules->isEmpty())
    <div style="text-align:center;padding:48px;color:#9ca3af;font-size:14px">
        <div style="font-size:40px;margin-bottom:12px">📅</div>
        Belum ada jadwal ditemukan
    </div>
    @else
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f8faf7;border-bottom:1.5px solid #e8f0e6">
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Hari</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Slot Waktu</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Lab</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Guru / Mapel</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Kelas</th>
                    <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Status</th>
                    <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $s)
                @php $dayId = $days[$s->day_of_week] ?? $s->day_of_week; @endphp
                <tr style="border-top:1px solid #f5f5f5;transition:background .1s" onmouseover="this.style.background='#fafcf9'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px">
                        <span class="badge day-{{ $dayId }}">{{ $dayId }}</span>
                    </td>
                    <td style="padding:12px 16px">
                        @if($s->timeSlot)
                        <div style="font-weight:700;font-size:12px;color:#1A2517">{{ $s->timeSlot->name }}</div>
                        <div style="font-size:11px;color:#9ca3af">
                            {{ \Carbon\Carbon::parse($s->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($s->timeSlot->end_time)->format('H:i') }}
                        </div>
                        @else <span style="color:#d1d5db">—</span> @endif
                    </td>
                    <td style="padding:12px 16px">
                        <div style="font-size:12px;font-weight:600;color:#374151">{{ $s->resource->name ?? '—' }}</div>
                    </td>
                    <td style="padding:12px 16px">
                        <div style="font-weight:600;color:#1A2517">{{ $s->teacher_name }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $s->subject_name ?? '—' }}</div>
                    </td>
                    <td style="padding:12px 16px">
                        <div style="font-size:12px;color:#374151">{{ $s->labClass->name ?? '—' }}</div>
                    </td>
                    <td style="padding:12px 16px;text-align:center">
                        <span class="badge badge-{{ $s->status }}">
                            {{ $s->status === 'active' ? '● Aktif' : '○ Nonaktif' }}
                        </span>
                    </td>
                    <td style="padding:12px 16px;text-align:center">
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                            <button class="btn-sm-edit" onclick="openEdit(
                                {{ $s->id }},
                                '{{ addslashes($s->teacher_name) }}',
                                '{{ addslashes($s->subject_name ?? '') }}',
                                '{{ addslashes($s->notes ?? '') }}',
                                '{{ $s->status }}'
                            )">✏ Edit</button>
                            <form method="POST" action="{{ route('schedule.admin.destroy', $s->id) }}"
                                  onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm-del" title="Hapus">
                                    <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
    @endif
</div>

{{-- ═══ MODAL TAMBAH ═══ --}}
<div id="add-modal" class="modal-overlay" onclick="if(event.target===this)closeAdd()">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="font-family:Outfit,sans-serif;font-weight:700;font-size:17px;color:#fff;margin:0">Tambah Jadwal Tetap</h3>
            <p style="font-size:12px;color:rgba(172,200,162,.5);margin:4px 0 0">Jadwal rutin mingguan laboratorium</p>
        </div>
        <form method="POST" action="{{ route('schedule.admin.store') }}" class="modal-body">
            @csrf
            <div class="form-group">
                <label class="form-label">Laboratorium *</label>
                <select name="resource_id" class="inp" required>
                    <option value="">— Pilih Lab —</option>
                    @foreach($resources as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">Hari *</label>
                    <select name="day_of_week" class="inp" required>
                        <option value="">— Pilih Hari —</option>
                        @foreach($days as $en => $id)
                        <option value="{{ $en }}">{{ $id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Slot Waktu *</label>
                    <select name="time_slot_id" class="inp" required>
                        <option value="">— Pilih Slot —</option>
                        @foreach($timeSlots as $ts)
                        <option value="{{ $ts->id }}">{{ $ts->name }} ({{ \Carbon\Carbon::parse($ts->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($ts->end_time)->format('H:i') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Unit Sekolah *</label>
                <select name="organization_id" class="inp" required onchange="loadKelasAdd(this.value)">
                    <option value="">— Pilih Unit —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kelas *</label>
                <select name="class_id" id="add-class" class="inp" required disabled>
                    <option value="">— Pilih unit sekolah dulu —</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Guru *</label>
                <input type="text" name="teacher_name" class="inp" required placeholder="Contoh: Bu Husnul">
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran</label>
                <input type="text" name="subject_name" class="inp" placeholder="Contoh: TIK">
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="inp" placeholder="Opsional">
            </div>
            <div style="display:flex;gap:10px;margin-top:4px">
                <button type="button" onclick="closeAdd()" style="flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer">Batal</button>
                <button type="submit" class="btn-primary" style="flex:1;padding:11px;border-radius:10px">+ Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL EDIT ═══ --}}
<div id="edit-modal" class="modal-overlay" onclick="if(event.target===this)closeEdit()">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="font-family:Outfit,sans-serif;font-weight:700;font-size:17px;color:#fff;margin:0">Edit Jadwal</h3>
        </div>
        <form id="edit-form" method="POST" class="modal-body">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Nama Guru *</label>
                <input type="text" name="teacher_name" id="edit-teacher" class="inp" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran</label>
                <input type="text" name="subject_name" id="edit-subject" class="inp">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="edit-status" class="inp">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" id="edit-notes" class="inp">
            </div>
            <div style="display:flex;gap:10px;margin-top:4px">
                <button type="button" onclick="closeEdit()" style="flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer">Batal</button>
                <button type="submit" class="btn-primary" style="flex:1;padding:11px;border-radius:10px">✓ Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdd() {
    document.getElementById('add-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeAdd() {
    document.getElementById('add-modal').classList.remove('open');
    document.body.style.overflow = '';
}
function openEdit(id, teacher, subject, notes, status) {
    document.getElementById('edit-form').action = '/jadwal-admin/' + id;
    document.getElementById('edit-teacher').value = teacher;
    document.getElementById('edit-subject').value = subject;
    document.getElementById('edit-notes').value   = notes;
    document.getElementById('edit-status').value  = status;
    document.getElementById('edit-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEdit() {
    document.getElementById('edit-modal').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') { closeAdd(); closeEdit(); } });

function loadKelasAdd(orgId) {
    const sel = document.getElementById('add-class');
    if (!orgId) { sel.innerHTML = '<option value="">— Pilih unit sekolah dulu —</option>'; sel.disabled = true; return; }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled = true;
    fetch('/kelas?organization_id=' + orgId)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Pilih kelas —</option>';
            data.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            sel.disabled = false;
        });
}
</script>

</x-app-layout>