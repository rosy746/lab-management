@extends('finance.layouts.app')
@section('title', 'Edit Anggaran — Keuangan Tim')
@section('page-title', 'Edit Anggaran')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

.form-wrap { max-width: 620px; }

/* Period badge */
.period-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, var(--g1), var(--g2));
    color: white; padding: 6px 14px; border-radius: 20px;
    font-size: 13px; font-weight: 700;
    box-shadow: 0 3px 10px rgba(7,31,20,0.25);
}
.period-badge::before { content:'📅'; font-size:12px; }

/* Status toggle */
.status-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.sts-opt { position: relative; }
.sts-opt input { position: absolute; opacity:0; width:0; height:0; }
.sts-lbl {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    padding: 10px; border-radius: 9px; font-size: 12.5px; font-weight: 600;
    border: 1.5px solid var(--border); color: var(--muted);
    cursor: pointer; transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1);
}
.sts-opt input[value="active"]:checked ~ .sts-lbl {
    border-color: #16a34a; background: #f0fdf4; color: #15803d;
    transform: scale(1.03); box-shadow: 0 3px 12px rgba(22,163,74,0.2);
}
.sts-opt input[value="closed"]:checked ~ .sts-lbl {
    border-color: var(--muted); background: var(--bg); color: var(--text);
    transform: scale(1.03);
}

/* Budget categories */
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

.bcat-info { font-size: 10px; color: var(--muted); margin-top: 1px; }
.bcat-used { color: var(--red); font-weight: 600; }

.bcat-input {
    border: none; border-left: 1px solid var(--border);
    padding: 11px 12px;
    font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 500;
    color: var(--text); background: transparent; outline: none; width: 100%;
    transition: background 0.15s;
}
.bcat-input:focus { background: rgba(0,232,122,0.04); }
.bcat-input::placeholder { color: var(--border); font-weight: 400; }

/* Alloc preview */
.total-preview {
    background: linear-gradient(135deg, var(--g1), var(--g2));
    border-radius: 11px; padding: 16px 18px;
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 14px;
}
.total-preview-label { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.7px; }
.total-preview-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 20px; font-weight: 600; color: white;
}
.total-preview-note { font-size: 10px; color: rgba(255,255,255,0.3); margin-top: 2px; }

.form-actions { display: flex; gap: 10px; margin-top: 24px; }
</style>
@endpush

@section('content')
<div class="form-wrap float-in" style="animation-delay:.05s">
    <div class="card">
        <div class="card-head">
            <div class="period-badge">{{ $period->name }}</div>
            <a href="{{ route('finance.budgets.index') }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('finance.budgets.update', $period) }}">
                @csrf @method('PUT')

                {{-- Total Anggaran --}}
                <div class="form-group">
                    <label class="form-label" for="total_budget">Total Anggaran</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:600;color:var(--muted);">Rp</span>
                        <input type="number" name="total_budget" id="total_budget"
                               class="form-control {{ $errors->has('total_budget') ? 'is-invalid' : '' }}"
                               style="padding-left:42px;font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:600;"
                               value="{{ old('total_budget', $period->total_budget) }}"
                               min="0" required oninput="updateTotal()">
                    </div>
                    @error('total_budget')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label class="form-label">Status Periode</label>
                    <div class="status-toggle">
                        <div class="sts-opt">
                            <input type="radio" name="status" id="s_active" value="active"
                                   {{ old('status', $period->status) === 'active' ? 'checked' : '' }}>
                            <label for="s_active" class="sts-lbl">
                                <span>✅</span> Aktif
                            </label>
                        </div>
                        <div class="sts-opt">
                            <input type="radio" name="status" id="s_closed" value="closed"
                                   {{ old('status', $period->status) === 'closed' ? 'checked' : '' }}>
                            <label for="s_closed" class="sts-lbl">
                                <span>🔒</span> Ditutup
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Kategori --}}
                <div class="form-group">
                    <label class="form-label">Alokasi per Kategori</label>
                    <div class="budget-cats">
                        @foreach($categories as $i => $cat)
                            @php $usedAmt = $budgets[$cat->id] ?? 0; @endphp
                            <div class="bcat-row">
                                <div class="bcat-name">
                                    <input type="hidden" name="budgets[{{ $i }}][category_id]" value="{{ $cat->id }}">
                                    <span class="bcat-dot {{ $usedAmt > 0 ? 'has-val' : '' }}" id="dot-{{ $i }}"></span>
                                    <div>
                                        {{ $cat->name }}
                                        @if($usedAmt > 0)
                                            <div class="bcat-info">Anggaran: <span class="bcat-used">Rp {{ number_format($usedAmt, 0, ',', '.') }}</span></div>
                                        @endif
                                    </div>
                                </div>
                                <input type="number"
                                       name="budgets[{{ $i }}][amount]"
                                       class="bcat-input"
                                       value="{{ old("budgets.{$i}.amount", $usedAmt) }}"
                                       min="0" placeholder="0"
                                       data-idx="{{ $i }}"
                                       oninput="onBudgetInput(this, {{ $i }})">
                            </div>
                        @endforeach
                    </div>

                    <div class="total-preview">
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
                              placeholder="Catatan tambahan">{{ old('notes', $period->notes) }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
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

document.addEventListener('DOMContentLoaded', updateTotal);
</script>
@endpush