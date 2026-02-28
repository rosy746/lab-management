<x-app-layout>
<x-slot name="title">Manajemen Guru</x-slot>

<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.wrap{animation:fadeUp .4s ease both}
.flash{padding:11px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
.flash-ok{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.flash-err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.add-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;margin-bottom:20px}
.add-head{padding:14px 20px;background:linear-gradient(135deg,#1A2517,#2a3826);display:flex;align-items:center;justify-content:space-between;cursor:pointer}
.add-head-title{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
.add-head-sub{font-size:11px;color:rgba(172,200,162,.4);margin-top:2px}
.add-toggle{color:#ACC8A2;transition:transform .2s}
.add-toggle.open{transform:rotate(180deg)}
.add-body{padding:20px;display:none}
.add-body.open{display:block}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:500px){.field-row{grid-template-columns:1fr}}
.field{margin-bottom:14px}
.field-label{display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.inp{width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:13px;font-family:inherit;background:#fafcf9;outline:none;transition:border-color .15s}
.inp:focus{border-color:#ACC8A2;box-shadow:0 0 0 3px rgba(172,200,162,.1)}
.btn-add{padding:10px 22px;border-radius:10px;border:none;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;transition:transform .15s,box-shadow .15s}
.btn-add:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(26,37,23,.25)}
.tbl-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden}
.tbl-head{padding:14px 20px;border-bottom:1px solid #e8f0e6;display:flex;align-items:center;justify-content:space-between}
.tbl-head-title{font-size:13px;font-weight:700;color:#1A2517}
.tbl-count{font-size:11px;color:#9ca3af}
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:12px;min-width:560px}
thead tr{background:#f8faf7;border-bottom:2px solid #e8f0e6}
thead th{padding:9px 14px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap}
tbody tr{border-top:1px solid #f5f5f5;transition:background .1s}
tbody tr:hover{background:#fafcf9}
tbody td{padding:11px 14px;vertical-align:middle}
.token-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:7px;font-family:'Outfit',sans-serif;font-weight:700;font-size:13px;letter-spacing:.08em;background:rgba(172,200,162,.1);color:#1A2517;border:1px solid rgba(172,200,162,.3)}
.status-badge{padding:3px 9px;border-radius:999px;font-size:10px;font-weight:700}
.status-active{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
.status-inactive{background:#f9fafb;color:#9ca3af;border:1px solid #e5e7eb}
.edit-form{display:none;gap:6px;align-items:center;flex-wrap:wrap;margin-top:6px}
.edit-form.show{display:flex}
.inp-sm{border:1.5px solid #e5e7eb;border-radius:7px;padding:5px 9px;font-size:12px;font-family:inherit;outline:none;background:#fafcf9;transition:border-color .15s}
.inp-sm:focus{border-color:#ACC8A2}
.btn-sm{padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:background .15s}
.btn-save{background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2}
.btn-cancel{background:#f3f4f6;color:#6b7280}
.btn-edit{background:#f0f4ef;color:#1A2517;border:1px solid #e8f0e6;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit}
.btn-deactivate{background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit}
.stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:12px;padding:14px 16px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);text-align:center}
.stat-val{font-family:'Outfit',sans-serif;font-size:22px;font-weight:800;color:#1A2517}
.stat-key{font-size:10px;color:#9ca3af;margin-top:3px}
</style>

<div class="wrap">

    @if(session('success'))
        <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="flash flash-err">⚠ {{ $errors->first() }}</div>
    @endif

    {{-- STATS --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-val">{{ $teachers->count() }}</div>
            <div class="stat-key">Total Guru</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:#16a34a">{{ $teachers->where('is_active',true)->count() }}</div>
            <div class="stat-key">Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:#9ca3af">{{ $teachers->where('is_active',false)->count() }}</div>
            <div class="stat-key">Nonaktif</div>
        </div>
    </div>

    {{-- FORM TAMBAH --}}
    <div class="add-card">
        <div class="add-head" onclick="toggleAdd()">
            <div>
                <div class="add-head-title">➕ Tambah Guru Baru</div>
                <div class="add-head-sub">Token akan digenerate otomatis</div>
            </div>
            <svg class="add-toggle" id="add-toggle" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div class="add-body" id="add-body">
            <form method="POST" action="{{ route('teacher.store') }}">
                @csrf
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Nama Lengkap *</label>
                        <input name="name" type="text" class="inp" placeholder="Contoh: Bu Husnul" required value="{{ old('name') }}">
                    </div>
                    <div class="field">
                        <label class="field-label">Nomor HP</label>
                        <input name="phone" type="text" class="inp" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                    </div>
                </div>
                <button type="submit" class="btn-add">✓ Tambah Guru</button>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="tbl-card">
        <div class="tbl-head">
            <span class="tbl-head-title">👩‍🏫 Daftar Guru</span>
            <span class="tbl-count">{{ $teachers->count() }} guru terdaftar</span>
        </div>
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Nama Guru</th>
                        <th>No. HP</th>
                        <th style="text-align:center">Jadwal</th>
                        <th style="text-align:center">Booking</th>
                        <th style="text-align:center">Tugas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $t)
                    <tr>
                        <td><span class="token-badge">🔑 {{ $t->token }}</span></td>
                        <td>
                            <div style="font-weight:700">{{ $t->name }}</div>
                            <div id="edit-form-{{ $t->id }}" class="edit-form">
                                <form method="POST" action="{{ route('teacher.update', $t) }}" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                                    @csrf @method('PATCH')
                                    <input type="text" name="name" class="inp-sm" value="{{ $t->name }}" style="width:140px" required>
                                    <input type="text" name="phone" class="inp-sm" value="{{ $t->phone }}" placeholder="No HP" style="width:120px">
                                    <input type="hidden" name="is_active" value="{{ $t->is_active ? 1 : 0 }}">
                                    <button type="submit" class="btn-sm btn-save">Simpan</button>
                                    <button type="button" class="btn-sm btn-cancel" onclick="cancelEdit({{ $t->id }})">Batal</button>
                                </form>
                            </div>
                        </td>
                        <td style="color:#6b7280">{{ $t->phone ?? '-' }}</td>
                        <td style="text-align:center;font-weight:700">{{ $t->schedules_count }}</td>
                        <td style="text-align:center;font-weight:700">{{ $t->bookings_count }}</td>
                        <td style="text-align:center;font-weight:700">{{ $t->assignments_count }}</td>
                        <td>
                            <span class="status-badge {{ $t->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap">
                                <button class="btn-edit" onclick="toggleEdit({{ $t->id }})">Edit</button>
                                @if($t->is_active)
                                <form method="POST" action="{{ route('teacher.destroy', $t) }}" onsubmit="return confirm('Nonaktifkan guru {{ addslashes($t->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-deactivate">Nonaktifkan</button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('teacher.update', $t) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="name" value="{{ $t->name }}">
                                    <input type="hidden" name="phone" value="{{ $t->phone }}">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="btn-sm btn-save">Aktifkan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:36px;color:#9ca3af">
                            Belum ada data guru
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Link ke panel tugas --}}
    <div style="margin-top:16px;text-align:right">
        <a href="{{ route('assignment.admin') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;font-size:13px;font-weight:700;text-decoration:none">
            📋 Buka Panel Tugas →
        </a>
    </div>
</div>

<script>
function toggleAdd() {
    document.getElementById('add-body').classList.toggle('open');
    document.getElementById('add-toggle').classList.toggle('open');
}
function toggleEdit(id) {
    document.getElementById('edit-form-' + id).classList.toggle('show');
}
function cancelEdit(id) {
    document.getElementById('edit-form-' + id).classList.remove('show');
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('add-body').classList.add('open');
    document.getElementById('add-toggle').classList.add('open');
});
@endif
</script>

</x-app-layout>