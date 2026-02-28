@extends('finance.layouts.app')
@section('title', 'Buat Anggaran — Keuangan Tim')
@section('page-title', 'Buat Anggaran')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

.form-wrap { max-width: 620px; }

/* ── Month Picker ── */
.month-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 7px; margin-bottom: 6px;
}
.month-opt { position: relative; }
.month-opt input { position: absolute; opacity:0; width:0; height:0; }
.month-lbl {
    display: flex; align-items: center; justify-content: center;
    padding: 9px 6px; border-radius: 9px; font-size: 12px; font-weight: 600;
    border: 1.5px solid var(--border); color: var(--muted);
    cursor: pointer; transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1);
    user-select: none;
}
.month-opt input:checked ~ .month-lbl {
    border-color: var(--accent2); background: rgba(0,196,104,0.08);
    color: var(--g1); transform: scale(1.05);
    box-shadow: 0 3px 10px rgba(0,196,104,0.2);
}
.month-lbl:hover { border-color: var(--accent2); color: var(--g1); }

/* ── Budget Categories Input ── */
.budget-cats {
    border: 1.5px solid var(--border); border-radius: 11px; overflow: hidden;
    transition: border-color 0.2s;
}
.budget-cats:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(0,232,122,0.1); }

.bcat-row {
    display: grid; grid-template-columns: 1fr 160px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.bcat-row:last-child { border-bottom: none; }
.bcat-row:hover { background: #f9f7f4; }

.bcat-name {
    padding: 11px 14px; font-size: 13px; font-weight: 500;
    color: var(--text); display: flex; align-items: center; gap: 8px;
}
.bcat-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--border); flex-shrink: 0; }
.bcat-dot.has-val { background: var(--accent2); }

.bcat-input {
    border: none; border-left: 1px solid var(--border);
    padding: 11px 12px;
    font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 500;
    color: var(--text); background: transparent; outline: none; width: 100%;
    transition: background 0.15s;
}
.bcat-input:focus { background: rgba(0,232,122,0.04); }
.bcat-input::placeholder { color: var(--border); font-weight: 400; }

/* ── Total Preview ── */
.total-preview {
    background: linear-gradient(135deg, var(--g1), var(--g2));
    border-radius: 11px; padding: 16px 18px;
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
}
.total-preview-label { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.7px; }
.total-preview-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 20px; font-weight: 600; color: white;
    transition: all 0.3s;
}
.total-preview-note { font-size: 10px; color: rgba(255,255,255,0.3); margin-top: 2px; }

.form-actions { display: flex; gap: 10px; margin-top: 24px; }
</style>
@endpush

@section('content')
<div class="form-wrap float-in" style="animation-delay:.05s">
    <div class="card">
        <div class="card-head">
            <span class="card-title">Anggaran Baru</span>
            <a href="{{ route('finance.budgets.index') }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('finance.budgets.store') }}" id="budgetForm">
                @csrf

                {{-- Bulan --}}
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <div class="month-grid">
                        @php $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des']; @endphp
                        @foreach($months as $idx => $mn)
                            <div class="month-opt">
                                <input type="radio" name="month" id="m{{ $idx+1 }}" value="{{ $idx+1 }}"
                                       {{ old('month', now()->month) == $idx+1 ? 'checked' : '' }}>
                                <label for="m{{ $idx+1 }}" class="month-lbl">{{ $mn }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('month')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Tahun --}}
                <div class="form-group">
                    <label class="form-label" for="year">Tahun</label>
                    <input type="number" name="year" id="year" class="form-control"
                           style="width:120px;" value="{{ old('year', now()->year) }}" min="2024" required>
                </div>

                {{-- Total Anggaran --}}
                <div class="form-group">
                    <label class="form-label" for="total_budget">Total Anggaran</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:600;color:var(--muted);">Rp</span>
                        <input type="number" name="total_budget" id="total_budget"
                               class="form-control {{ $errors->has('total_budget') ? 'is-invalid' : '' }}"
                               style="padding-left:42px;font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:600;"
                               value="{{ old('total_budget') }}" min="0" required placeholder="0"
                               oninput="updateTotal()">
                    </div>
                    @error('total_budget')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori Pengeluaran --}}
                <div class="form-group">
                    <label class="form-label">Alokasi per Kategori</label>
                    <p class="form-hint" style="margin-bottom:10px;">Kosongkan jika kategori tidak dianggarkan</p>

                    <div class="budget-cats" id="budgetCats">
                        @foreach($categories as $i => $cat)
                            <div class="bcat-row">
                                <div class="bcat-name">
                                    <input type="hidden" name="budgets[{{ $i }}][category_id]" value="{{ $cat->id }}">
                                    <span class="bcat-dot" id="dot-{{ $i }}"></span>
                                    {{ $cat->name }}
                                </div>
                                <input type="number" name="budgets[{{ $i }}][amount]"
                                       class="bcat-input"
                                       value="{{ old("budgets.{$i}.amount", 0) }}"
                                       min="0" placeholder="0"
                                       data-idx="{{ $i }}"
                                       oninput="onBudgetInput(this, {{ $i }})">
                            </div>
                        @endforeach
                    </div>

                    {{-- Alokasi preview --}}
                    <div class="total-preview" style="margin-top:14px;" id="allocPreview">
                        <div>
                            <div class="total-preview-label">Total Dialokasikan</div>
                            <div class="total-preview-val" id="allocVal">Rp 0</div>
                            <div class="total-preview-note" id="allocNote">dari Rp 0 anggaran</div>
                        </div>
                        <div id="allocPct" style="font-family:'JetBrains Mono',monospace;font-size:28px;font-weight:700;color:rgba(255,255,255,0.4);">0%</div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="form-group">
                    <label class="form-label" for="notes">Catatan <span style="font-weight:400;color:var(--muted)">(opsional)</span></label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"
                              placeholder="Catatan tambahan untuk periode ini">{{ old('notes') }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Anggaran
                    </button>
                    <a href="{{ route('finance.budgets.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fmtRp(v) { return 'Rp ' + Math.round(v).toLocaleString('id-ID'); }

function updateTotal() {
    const total  = parseFloat(document.getElementById('total_budget').value) || 0;
    const inputs = document.querySelectorAll('.bcat-input');
    let alloc    = 0;
    inputs.forEach(inp => alloc += parseFloat(inp.value) || 0);

    document.getElementById('allocVal').textContent  = fmtRp(alloc);
    document.getElementById('allocNote').textContent = 'dari ' + fmtRp(total) + ' anggaran';

    const pct = total > 0 ? Math.round((alloc / total) * 100) : 0;
    document.getElementById('allocPct').textContent = pct + '%';
    document.getElementById('allocPct').style.color =
        pct > 100 ? '#fb7185' : pct >= 80 ? '#fbbf24' : 'rgba(255,255,255,0.4)';
}

function onBudgetInput(el, idx) {
    const dot = document.getElementById('dot-' + idx);
    if (dot) dot.classList.toggle('has-val', parseFloat(el.value) > 0);
    updateTotal();
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.bcat-input').forEach((inp, i) => {
        if (parseFloat(inp.value) > 0) {
            const dot = document.getElementById('dot-' + i);
            if (dot) dot.classList.add('has-val');
        }
    });
    updateTotal();
});
</script>
@endpush