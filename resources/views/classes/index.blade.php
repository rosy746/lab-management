<x-app-layout>
<x-slot name="title">Manajemen Kelas</x-slot>

<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.wrap{animation:fadeUp .4s ease both}
.add-card{background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.06);overflow:hidden;margin-bottom:20px}
.add-head{padding:14px 20px;background:linear-gradient(135deg,#1A2517,#2a3826);display:flex;align-items:center;justify-content:space-between;cursor:pointer}
.add-head-title{font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#fff}
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
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:12px;min-width:600px}
thead tr{background:#f8faf7;border-bottom:2px solid #e8f0e6}
thead th{padding:9px 14px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap}
tbody tr{border-top:1px solid #f5f5f5;transition:background .1s}
tbody tr:hover{background:#fafcf9}
tbody td{padding:11px 14px;vertical-align:middle}
.edit-form{display:none;gap:6px;align-items:center;flex-wrap:wrap;margin-top:6px}
.edit-form.show{display:flex}
.inp-sm{border:1.5px solid #e5e7eb;border-radius:7px;padding:5px 9px;font-size:12px;font-family:inherit;outline:none;background:#fafcf9;transition:border-color .15s}
.btn-sm{padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:background .15s}
.btn-save{background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2}
.btn-cancel{background:#f3f4f6;color:#6b7280}
.btn-edit{background:#f0f4ef;color:#1A2517;border:1px solid #e8f0e6;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer}
.btn-delete{background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer}
</style>

<div class="wrap">
    {{-- FORM TAMBAH --}}
    <div class="add-card">
        <div class="add-head" onclick="toggleAdd()">
            <div>
                <div class="add-head-title">➕ Tambah Kelas Baru</div>
            </div>
            <svg class="add-toggle" id="add-toggle" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div class="add-body" id="add-body">
            <form method="POST" action="{{ route('class.store') }}">
                @csrf
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Sekolah / Lembaga *</label>
                        <select name="organization_id" class="inp" required>
                            <option value="">Pilih Sekolah</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">Tingkat *</label>
                        <input name="grade_level" type="text" class="inp" placeholder="Contoh: X, XI, XII atau 7, 8, 9" required>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Nama Kelas *</label>
                        <input name="name" type="text" class="inp" placeholder="Contoh: TKJ 1, RPL 2" required>
                    </div>
                    <div class="field">
                        <label class="field-label">Jurusan</label>
                        <input name="major" type="text" class="inp" placeholder="Contoh: Teknik Komputer dan Jaringan">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Jumlah Siswa</label>
                        <input name="student_count" type="number" class="inp" placeholder="36">
                    </div>
                    <div class="field">
                        <label class="field-label">Tahun Akademik *</label>
                        <input name="academic_year" type="text" class="inp" placeholder="2023/2024" required value="{{ date('Y').'/'.(date('Y')+1) }}">
                    </div>
                </div>
                <button type="submit" class="btn-add">✓ Tambah Kelas</button>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="tbl-card">
        <div class="tbl-head">
            <span class="tbl-head-title">🏫 Daftar Kelas</span>
        </div>
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Sekolah</th>
                        <th>Kelas</th>
                        <th>Tingkat/Jurusan</th>
                        <th>Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $idx => $cls)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><span style="font-weight:600;color:#1A2517">{{ $cls->organization->name ?? '-' }}</span></td>
                        <td>
                            <div style="font-weight:700">{{ $cls->name }}</div>
                            <div id="edit-form-{{ $cls->id }}" class="edit-form">
                                <form method="POST" action="{{ route('class.update', $cls) }}" style="display:contents">
                                    @csrf @method('PATCH')
                                    <select name="organization_id" class="inp-sm" required style="width:140px">
                                        @foreach($organizations as $org)
                                        <option value="{{ $org->id }}" {{ $cls->organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="name" class="inp-sm" value="{{ $cls->name }}" style="width:100px" required>
                                    <input type="text" name="grade_level" class="inp-sm" value="{{ $cls->grade_level }}" style="width:60px" required>
                                    <input type="text" name="major" class="inp-sm" value="{{ $cls->major }}" placeholder="Jurusan" style="width:100px">
                                    <input type="number" name="student_count" class="inp-sm" value="{{ $cls->student_count }}" style="width:60px">
                                    <input type="text" name="academic_year" class="inp-sm" value="{{ $cls->academic_year }}" style="width:90px" required>
                                    <button type="submit" class="btn-sm btn-save">Simpan</button>
                                    <button type="button" class="btn-sm btn-cancel" onclick="cancelEdit({{ $cls->id }})">Batal</button>
                                </form>
                            </div>
                        </td>
                        <td style="color:#6b7280">{{ $cls->grade_level }} {{ $cls->major }}</td>
                        <td style="font-weight:700">{{ $cls->student_count ?? 0 }} <small style="font-weight:400;color:#9ca3af">siswa</small></td>
                        <td>
                            <div style="display:flex;gap:5px">
                                <button class="btn-edit" onclick="toggleEdit({{ $cls->id }})">Edit</button>
                                <form method="POST" action="{{ route('class.destroy', $cls) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af">Belum ada data kelas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
</script>

</x-app-layout>
