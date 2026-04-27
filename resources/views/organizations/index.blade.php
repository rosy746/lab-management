<x-app-layout>
<x-slot name="title">Manajemen Sekolah & Kelas</x-slot>

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

/* Org Card */
.org-card{background:#fff;border-radius:16px;border:1px solid #e8f0e6;box-shadow:0 1px 6px rgba(26,37,23,.05);margin-bottom:24px;overflow:hidden}
.org-head{padding:16px 20px;background:#fcfdfb;border-bottom:1px solid #e8f0e6;display:flex;align-items:center;justify-content:space-between}
.org-name{font-family:'Outfit',sans-serif;font-weight:800;font-size:17px;color:#1A2517;display:flex;align-items:center;gap:10px}
.org-type{font-size:11px;padding:3px 9px;border-radius:999px;background:#1A2517;color:#ACC8A2;font-weight:700}
.org-meta{font-size:12px;color:#9ca3af;margin-top:4px;display:flex;gap:15px}

/* Nested Classes Table */
.cls-wrap{padding:10px 20px 20px}
.cls-table{width:100%;border-collapse:collapse;font-size:12px}
.cls-table thead th{padding:10px 12px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;border-bottom:2px solid #f3f4f6}
.cls-table tbody tr{border-bottom:1px solid #f9fafb}
.cls-table tbody tr:hover{background:#fafcf9}
.cls-table td{padding:12px;vertical-align:middle}
.cls-name{font-weight:700;color:#1A2517;font-size:13px}
.cls-major{color:#6b7280;font-size:12px}

/* Action Buttons */
.btn-icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px solid #e8f0e6;background:#fff;color:#6b7280;cursor:pointer;transition:all .15s}
.btn-icon:hover{border-color:#ACC8A2;color:#1A2517;background:#f8faf7}
.btn-icon-red:hover{border-color:#fecaca;color:#ef4444;background:#fef2f2}

/* Inline Edit Forms */
.edit-form{display:none;background:#fcfdfb;padding:12px;border-radius:10px;border:1px solid #e8f0e6;margin:10px 0}
.edit-form.show{display:block}
.inp-sm{border:1.5px solid #e5e7eb;border-radius:8px;padding:6px 10px;font-size:12px;outline:none;background:#fff}
</style>

<div class="wrap">
    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div style="padding:12px 16px;border-radius:12px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;font-size:13px;font-weight:600;margin-bottom:20px">✓ {{ session('success') }}</div>
    @endif

    {{-- FORM TAMBAH SEKOLAH --}}
    <div class="add-card">
        <div class="add-head" onclick="toggleAdd('school-form')">
            <div>
                <div class="add-head-title">➕ Tambah Sekolah / Lembaga Baru</div>
            </div>
            <svg class="add-toggle" id="toggle-school-form" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div class="add-body" id="school-form">
            <form method="POST" action="{{ route('organization.store') }}">
                @csrf
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Nama Sekolah *</label>
                        <input name="name" type="text" class="inp" placeholder="Contoh: SMK Nuris Jember" required>
                    </div>
                    <div class="field">
                        <label class="field-label">Tipe *</label>
                        <select name="type" class="inp" required>
                            <option value="SMK">SMK</option>
                            <option value="SMA">SMA</option>
                            <option value="MA">MA</option>
                            <option value="SMP">SMP</option>
                            <option value="MTs">MTs</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-add">✓ Simpan Sekolah</button>
            </form>
        </div>
    </div>

    {{-- LIST SEKOLAH --}}
    @foreach($organizations as $org)
    <div class="org-card">
        {{-- Org Header --}}
        <div class="org-head">
            <div>
                <div class="org-name">
                    <span>🏢 {{ $org->name }}</span>
                    <span class="org-type">{{ $org->type }}</span>
                </div>
                <div class="org-meta">
                    <span>✉ {{ $org->email ?? '-' }}</span>
                    <span>📞 {{ $org->phone ?? '-' }}</span>
                    <span>📍 {{ $org->address ?? '-' }}</span>
                </div>
            </div>
            <div style="display:flex;gap:8px">
                <button class="btn-icon" onclick="toggleEdit('org', {{ $org->id }})" title="Edit Sekolah">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <form action="{{ route('organization.destroy', $org) }}" method="POST" onsubmit="return confirm('Hapus sekolah ini dan semua kelas di dalamnya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon btn-icon-red" title="Hapus Sekolah">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Org Edit Form (Hidden) --}}
        <div id="edit-org-{{ $org->id }}" class="edit-form mx-5 mt-4">
            <form action="{{ route('organization.update', $org) }}" method="POST">
                @csrf @method('PATCH')
                <div class="field-row">
                    <div class="field">
                        <label class="field-label">Nama Sekolah</label>
                        <input name="name" type="text" class="inp" value="{{ $org->name }}" required>
                    </div>
                    <div class="field">
                        <label class="field-label">Tipe</label>
                        <select name="type" class="inp" required>
                            @foreach(['SMK','SMA','MA','SMP','MTs','Lainnya'] as $t)
                                <option value="{{ $t }}" {{ $org->type == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="btn-add" style="padding:7px 15px;font-size:12px">Update Sekolah</button>
                    <button type="button" onclick="toggleEdit('org', {{ $org->id }})" class="btn-icon" style="height:auto;padding:7px 15px;font-size:12px">Batal</button>
                </div>
            </form>
        </div>

        {{-- Classes Table --}}
        <div class="cls-wrap">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                <span style="font-size:11px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">🏫 Daftar Kelas ({{ $org->classes->count() }})</span>
                <button class="btn-add" style="padding:6px 12px;font-size:11px" onclick="toggleAdd('class-form-{{ $org->id }}')">
                    + Tambah Kelas
                </button>
            </div>

            {{-- Form Tambah Kelas (Hidden) --}}
            <div id="class-form-{{ $org->id }}" class="edit-form" style="margin-bottom:15px;border:1.5px dashed #ACC8A2">
                <form action="{{ route('class.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="organization_id" value="{{ $org->id }}">
                    <div class="field-row">
                        <div class="field">
                            <label class="field-label">Tingkat</label>
                            <input name="grade_level" type="text" class="inp-sm w-full" placeholder="X, XI, XII" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Nama Kelas</label>
                            <input name="name" type="text" class="inp-sm w-full" placeholder="TKJ 1" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label class="field-label">Jurusan</label>
                            <input name="major" type="text" class="inp-sm w-full" placeholder="Teknik Komputer">
                        </div>
                        <div class="field">
                            <label class="field-label">Tahun Akademik</label>
                            <input name="academic_year" type="text" class="inp-sm w-full" value="{{ date('Y').'/'.(date('Y')+1) }}" required>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px">
                        <button type="submit" class="btn-add" style="padding:7px 15px;font-size:12px">Simpan Kelas</button>
                        <button type="button" onclick="toggleAdd('class-form-{{ $org->id }}')" class="btn-icon" style="height:auto;padding:7px 15px;font-size:12px">Batal</button>
                    </div>
                </form>
            </div>

            <div class="tbl-wrap" style="border:1px solid #f3f4f6;border-radius:12px;overflow:hidden">
                <table class="cls-table">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Tingkat</th>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th>Tahun</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($org->classes as $idx => $cls)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td style="font-weight:700">{{ $cls->grade_level }}</td>
                            <td class="cls-name">{{ $cls->name }}</td>
                            <td class="cls-major">{{ $cls->major ?? '-' }}</td>
                            <td style="color:#9ca3af">{{ $cls->academic_year }}</td>
                            <td style="display:flex;justify-content:flex-end;gap:5px">
                                <button class="btn-icon" onclick="toggleEdit('class', {{ $cls->id }})">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('class.destroy', $cls) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-red">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        {{-- Edit Form Kelas (Hidden) --}}
                        <tr id="edit-class-{{ $cls->id }}" class="edit-form" style="display:none">
                            <td colspan="6">
                                <form action="{{ route('class.update', $cls) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="organization_id" value="{{ $org->id }}">
                                    <div class="field-row" style="margin-bottom:10px">
                                        <input name="grade_level" type="text" class="inp-sm" value="{{ $cls->grade_level }}" placeholder="Tingkat" required>
                                        <input name="name" type="text" class="inp-sm" value="{{ $cls->name }}" placeholder="Nama Kelas" required>
                                        <input name="major" type="text" class="inp-sm" value="{{ $cls->major }}" placeholder="Jurusan">
                                        <input name="academic_year" type="text" class="inp-sm" value="{{ $cls->academic_year }}" placeholder="Tahun" required>
                                    </div>
                                    <div style="display:flex;gap:8px">
                                        <button type="submit" class="btn-add" style="padding:5px 12px;font-size:11px">Simpan</button>
                                        <button type="button" onclick="toggleEdit('class', {{ $cls->id }})" class="btn-icon" style="height:auto;padding:5px 12px;font-size:11px">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:30px;color:#9ca3af">📦 Belum ada data kelas untuk sekolah ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
function toggleAdd(id) {
    const el = document.getElementById(id);
    const toggle = document.getElementById('toggle-' + id);
    el.classList.toggle('open');
    if(toggle) toggle.classList.toggle('open');
}

function toggleEdit(type, id) {
    const el = document.getElementById('edit-' + type + '-' + id);
    if(el.style.display === 'none' || el.classList.contains('show')) {
        el.style.display = (el.style.display === 'none') ? (type === 'class' ? 'table-row' : 'block') : 'none';
        el.classList.toggle('show');
    } else {
        el.style.display = 'none';
    }
}
</script>

</x-app-layout>
