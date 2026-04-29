<x-app-layout>
<x-slot name="title">Kelola Inventaris</x-slot>

<style>
.badge-cat { display:inline-flex;align-items:center;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:700; }
.cat-computer   { background:#dbeafe;color:#1d4ed8; }
.cat-peripheral { background:#f3e8ff;color:#7c3aed; }
.cat-furniture  { background:#fef3c7;color:#92400e; }
.cat-network    { background:#dcfce7;color:#15803d; }
.cat-software   { background:#e0f2fe;color:#0369a1; }
.cat-other      { background:#f3f4f6;color:#6b7280; }
.cond-excellent { background:#dcfce7;color:#166534; }
.cond-good      { background:#d1fae5;color:#065f46; }
.cond-fair      { background:#fef3c7;color:#92400e; }
.cond-poor      { background:#fee2e2;color:#991b1b; }
.cond-broken    { background:#fecaca;color:#7f1d1d; }
.filter-inp { border:1.5px solid #e5e7eb;border-radius:9px;padding:8px 12px;font-size:13px;background:#fafcf9;outline:none;transition:border-color .15s;font-family:inherit; }
.filter-inp:focus { border-color:#ACC8A2; }
.inp { width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:13px;background:#fafcf9;outline:none;transition:border-color .15s;box-sizing:border-box;font-family:inherit; }
.inp:focus { border-color:#ACC8A2;box-shadow:0 0 0 3px rgba(172,200,162,.12); }
.btn-primary { background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;border:none;border-radius:9px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-sm-edit { background:#f0f9f4;color:#166534;border:1.5px solid #bbf7d0;border-radius:7px;padding:5px 11px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s; }
.btn-sm-edit:hover { background:#dcfce7;transform:scale(1.05); }
.btn-sm-del  { background:#fff;color:#9ca3af;border:1.5px solid #f3f4f6;border-radius:7px;padding:5px 9px;font-size:11px;cursor:pointer;transition:all .2s; }
.btn-sm-del:hover { color:#dc2626;border-color:#fecaca;background:#fef2f2;transform:scale(1.05); }
.row-actions { opacity:0;transition:opacity .2s;display:flex;align-items:center;justify-content:center;gap:6px; }
tr:hover .row-actions { opacity:1; }
.sticky-thead th { position:sticky;top:0;z-index:10;background:#f8faf7;box-shadow:inset 0 -1.5px 0 #e8f0e6; }
.search-container { flex:1;min-width:240px;position:relative; }
.search-clear { position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#9ca3af;display:none; }
.filter-inp:not(:placeholder-shown) + .search-clear { display:block; }
.modal-overlay { display:none;position:fixed;inset:0;z-index:100;background:rgba(26,37,23,.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;transition:all .3s; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:16px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;animation:su .2s cubic-bezier(.16,1,.3,1); }
@keyframes su { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none} }
.modal-header { padding:18px 24px 14px;background:linear-gradient(135deg,#1A2517,#2d3d29);border-radius:16px 16px 0 0;position:sticky;top:0;z-index:1; }
.modal-body { padding:20px 24px; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px; }
.form-group { margin-bottom:14px; }
.form-label { display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px; }
</style>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
    @foreach([
        ['label'=>'Total Jenis','value'=>$stats['total_items'],'color'=>'#6b7280'],
        ['label'=>'Total Unit','value'=>$stats['total_units'],'color'=>'#1A2517'],
        ['label'=>'Unit Baik','value'=>$stats['total_good'],'color'=>'#16a34a'],
        ['label'=>'Unit Rusak','value'=>$stats['total_broken'],'color'=>'#dc2626'],
    ] as $s)
    <div style="background:#fff;border-radius:14px;padding:18px 20px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05)">
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

{{-- Toolbar --}}
<div style="background:#fff;border-radius:14px;padding:14px 18px;border:1px solid #e8f0e6;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center">
    <form method="GET" action="{{ route('inventory.admin') }}" style="display:flex;flex-wrap:wrap;gap:10px;flex:1;align-items:center">
        <div class="search-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama, merk, spesifikasi..." class="filter-inp" style="width:100%" id="inv-search">
            <span class="search-clear" onclick="document.getElementById('inv-search').value='';this.form.submit()">×</span>
        </div>
        <select name="resource_id" class="filter-inp">
            <option value="">Semua Lab</option>
            @foreach($resources as $r)
            <option value="{{ $r->id }}" {{ request('resource_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
            @endforeach
        </select>
        <select name="category" class="filter-inp">
            <option value="">Semua Kategori</option>
            @foreach($categories as $k => $v)
            <option value="{{ $k }}" {{ request('category')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
        <select name="condition" class="filter-inp">
            <option value="">Semua Kondisi</option>
            @foreach($conditions as $k => $v)
            <option value="{{ $k }}" {{ request('condition')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','resource_id','category','condition']))
        <a href="{{ route('inventory.admin') }}" style="font-size:13px;color:#9ca3af;text-decoration:none">× Reset</a>
        @endif
    </form>
    <button onclick="openAdd()" class="btn-primary" style="background:linear-gradient(135deg,#ACC8A2,#8ab87e);color:#1A2517;flex-shrink:0">
        + Tambah Barang
    </button>
</div>

{{-- Table --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden">
    <div style="padding:14px 20px;border-bottom:1px solid #f0f4ee">
        <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:15px;margin:0">
            Daftar Inventaris
            <span style="font-size:12px;color:#9ca3af;font-weight:400">({{ $items->count() }} item)</span>
        </h2>
    </div>

    @if($items->isEmpty())
    <div style="text-align:center;padding:48px;color:#9ca3af;font-size:14px">
        <div style="font-size:40px;margin-bottom:12px">📦</div>
        Belum ada data inventaris
    </div>
    @else
    <div style="overflow-x:auto;max-height:calc(100vh - 340px);overflow-y:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead class="sticky-thead">
                <tr style="background:#f8faf7;border-bottom:1.5px solid #e8f0e6">
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Nama Barang</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Lab</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Kategori</th>
                    <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Merk/Model</th>
                    <th style="padding:10px 12px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Total</th>
                    <th style="padding:10px 12px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Baik</th>
                    <th style="padding:10px 12px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Rusak</th>
                    <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Kondisi</th>
                    <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr style="border-top:1px solid #f5f5f5;transition:background .1s">
                    <td style="padding:12px 16px">
                        <div style="font-weight:700;color:#1A2517">{{ $item->item_name }}</div>
                        @if($item->specifications)
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $item->specifications }}">{{ $item->specifications }}</div>
                        @endif
                    </td>
                    <td style="padding:12px 16px">
                        <span style="font-size:12px;font-weight:600;color:#3d5438">{{ $item->resource->name ?? '-' }}</span>
                    </td>
                    <td style="padding:12px 16px">
                        <span class="badge-cat cat-{{ $item->category }}">{{ $categories[$item->category] ?? $item->category }}</span>
                    </td>
                    <td style="padding:12px 16px">
                        <div style="font-size:12px;font-weight:600;color:#374151">{{ $item->brand ?? '-' }}</div>
                        <div style="font-size:11px;color:#9ca3af">{{ $item->model ?? '' }}</div>
                    </td>
                    <td style="padding:12px;text-align:center;font-weight:800;font-family:Outfit,sans-serif;color:#1A2517">{{ $item->quantity }}</td>
                    <td style="padding:12px;text-align:center;font-weight:700;color:#16a34a">{{ $item->quantity_good }}</td>
                    <td style="padding:12px;text-align:center;font-weight:700;color:{{ $item->quantity_broken > 0 ? '#dc2626' : '#9ca3af' }}">{{ $item->quantity_broken }}</td>
                    <td style="padding:12px 16px;text-align:center">
                        <span class="badge-cat cond-{{ $item->condition }}">{{ $conditions[$item->condition] ?? $item->condition }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center">
                        <div class="row-actions">
                            <button class="btn-sm-edit" onclick="openEdit(
                                {{ $item->id }},
                                '{{ addslashes($item->item_name) }}',
                                '{{ $item->category }}',
                                '{{ addslashes($item->brand ?? '') }}',
                                '{{ addslashes($item->model ?? '') }}',
                                '{{ addslashes($item->serial_number ?? '') }}',
                                '{{ addslashes($item->specifications ?? '') }}',
                                '{{ $item->condition }}',
                                {{ $item->quantity }},
                                {{ $item->quantity_good }},
                                {{ $item->quantity_broken }},
                                {{ $item->quantity_backup }},
                                '{{ addslashes($item->notes ?? '') }}'
                            )">✏ Edit</button>
                            <form method="POST" action="{{ route('inventory.admin.destroy', $item->id) }}" onsubmit="return confirm('Hapus barang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm-del">
                                    <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

{{-- MODAL TAMBAH --}}
<div id="add-modal" class="modal-overlay" onclick="if(event.target===this)closeAdd()">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="font-family:Outfit,sans-serif;font-weight:700;font-size:17px;color:#fff;margin:0">Tambah Barang</h3>
            <p style="font-size:12px;color:rgba(172,200,162,.5);margin:4px 0 0">Data inventaris laboratorium</p>
        </div>
        <form method="POST" action="{{ route('inventory.admin.store') }}" class="modal-body">
            @csrf
            <div class="form-row">
                <div>
                    <label class="form-label">Laboratorium *</label>
                    <select name="resource_id" class="inp" required>
                        <option value="">— Pilih Lab —</option>
                        @foreach($resources as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="inp" required>
                        <option value="">— Pilih —</option>
                        @foreach($categories as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Barang *</label>
                <input type="text" name="item_name" class="inp" required placeholder="Contoh: Komputer PC">
            </div>
            <div class="form-row">
                <div>
                    <label class="form-label">Merk/Brand</label>
                    <input type="text" name="brand" class="inp" placeholder="Contoh: DELL">
                </div>
                <div>
                    <label class="form-label">Model/Tipe</label>
                    <input type="text" name="model" class="inp" placeholder="Contoh: Optiplex 3060">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Spesifikasi</label>
                <input type="text" name="specifications" class="inp" placeholder="Contoh: Intel i5, RAM 8GB, SSD 256GB">
            </div>
            <div class="form-row">
                <div>
                    <label class="form-label">No. Seri</label>
                    <input type="text" name="serial_number" class="inp" placeholder="Opsional">
                </div>
                <div>
                    <label class="form-label">Kondisi *</label>
                    <select name="condition" class="inp" required>
                        @foreach($conditions as $k => $v)
                        <option value="{{ $k }}" {{ $k==='good'?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row" style="grid-template-columns:repeat(4,1fr)">
                <div>
                    <label class="form-label">Total</label>
                    <input type="number" name="quantity" id="add-qty" class="inp" value="1" min="0" required oninput="calcBroken('add')">
                </div>
                <div>
                    <label class="form-label">Baik</label>
                    <input type="number" name="quantity_good" id="add-good" class="inp" value="1" min="0" required oninput="calcBroken('add')">
                </div>
                <div>
                    <label class="form-label">Rusak</label>
                    <input type="number" name="quantity_broken" id="add-broken" class="inp" value="0" min="0" required>
                </div>
                <div>
                    <label class="form-label">Cadangan</label>
                    <input type="number" name="quantity_backup" class="inp" value="0" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="inp" placeholder="Opsional">
            </div>
            <div style="display:flex;gap:10px">
                <button type="button" onclick="closeAdd()" style="flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer">Batal</button>
                <button type="submit" class="btn-primary" style="flex:1;padding:11px;border-radius:10px">+ Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="edit-modal" class="modal-overlay" onclick="if(event.target===this)closeEdit()">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="font-family:Outfit,sans-serif;font-weight:700;font-size:17px;color:#fff;margin:0">Edit Barang</h3>
        </div>
        <form id="edit-form" method="POST" class="modal-body">
            @csrf @method('PATCH')
            <div class="form-row">
                <div>
                    <label class="form-label">Kategori *</label>
                    <select name="category" id="e-category" class="inp" required>
                        @foreach($categories as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Kondisi *</label>
                    <select name="condition" id="e-condition" class="inp" required>
                        @foreach($conditions as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Barang *</label>
                <input type="text" name="item_name" id="e-name" class="inp" required>
            </div>
            <div class="form-row">
                <div>
                    <label class="form-label">Merk/Brand</label>
                    <input type="text" name="brand" id="e-brand" class="inp">
                </div>
                <div>
                    <label class="form-label">Model/Tipe</label>
                    <input type="text" name="model" id="e-model" class="inp">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Spesifikasi</label>
                <input type="text" name="specifications" id="e-specs" class="inp">
            </div>
            <div class="form-group">
                <label class="form-label">No. Seri</label>
                <input type="text" name="serial_number" id="e-serial" class="inp">
            </div>
            <div class="form-row" style="grid-template-columns:repeat(4,1fr)">
                <div><label class="form-label">Total</label><input type="number" name="quantity" id="e-qty" class="inp" min="0" required oninput="calcBroken('e')"></div>
                <div><label class="form-label">Baik</label><input type="number" name="quantity_good" id="e-good" class="inp" min="0" required oninput="calcBroken('e')"></div>
                <div><label class="form-label">Rusak</label><input type="number" name="quantity_broken" id="e-broken" class="inp" min="0" required></div>
                <div><label class="form-label">Cadangan</label><input type="number" name="quantity_backup" id="e-backup" class="inp" min="0" required></div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" id="e-notes" class="inp">
            </div>
            <div style="display:flex;gap:10px">
                <button type="button" onclick="closeEdit()" style="flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:11px;font-size:13px;font-weight:700;cursor:pointer">Batal</button>
                <button type="submit" class="btn-primary" style="flex:1;padding:11px;border-radius:10px">✓ Simpan</button>
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
function openEdit(id, name, cat, brand, model, serial, specs, cond, qty, good, broken, backup, notes) {
    document.getElementById('edit-form').action = '/inventaris-admin/' + id;
    document.getElementById('e-name').value    = name;
    document.getElementById('e-category').value = cat;
    document.getElementById('e-brand').value   = brand;
    document.getElementById('e-model').value   = model;
    document.getElementById('e-serial').value  = serial;
    document.getElementById('e-specs').value   = specs;
    document.getElementById('e-condition').value = cond;
    document.getElementById('e-qty').value     = qty;
    document.getElementById('e-good').value    = good;
    document.getElementById('e-broken').value  = broken;
    document.getElementById('e-backup').value  = backup;
    document.getElementById('e-notes').value   = notes;
    document.getElementById('edit-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEdit() {
    document.getElementById('edit-modal').classList.remove('open');
    document.body.style.overflow = '';
}
function calcBroken(prefix) {
    const qty = parseInt(document.getElementById(prefix + '-qty').value) || 0;
    const good = parseInt(document.getElementById(prefix + '-good').value) || 0;
    const broken = qty - good;
    document.getElementById(prefix + '-broken').value = Math.max(0, broken);
}
document.addEventListener('keydown', e => { if(e.key==='Escape') { closeAdd(); closeEdit(); } });
</script>

</x-app-layout>
