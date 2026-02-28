@extends('finance.layouts.app')
@section('title', 'Anggaran — Keuangan Tim')
@section('page-title', 'Anggaran')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes rowIn {
    from { opacity: 0; transform: translateX(-10px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

/* ── Page Top ── */
.page-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }

/* ── Period Cards Grid ── */
.period-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
}

.period-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s;
    animation: rowIn 0.4s ease both;
}
.period-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.09);
}

/* Card top strip */
.period-head {
    padding: 16px 18px 14px;
    position: relative; overflow: hidden;
}
.period-head::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, var(--g1) 0%, var(--g2) 100%);
    z-index: 0;
}
/* Subtle pattern */
.period-head::after {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 80% 20%, rgba(0,232,122,0.15) 0%, transparent 50%),
                      radial-gradient(circle at 20% 80%, rgba(0,232,122,0.08) 0%, transparent 50%);
    z-index: 1;
}
.period-head-inner { position: relative; z-index: 2; }

.period-name {
    font-size: 16px; font-weight: 700; color: white;
    letter-spacing: -0.3px; margin-bottom: 4px;
}
.period-status-row { display: flex; align-items: center; justify-content: space-between; }
.period-total {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px; color: rgba(255,255,255,0.55);
}
.status-dot {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10px; font-weight: 700;
}
.status-active { background: rgba(0,232,122,0.2); color: var(--accent); border: 1px solid rgba(0,232,122,0.3); }
.status-closed { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.15); }
.status-dot::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

/* Stats row inside card */
.period-stats {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    border-bottom: 1px solid var(--border);
}
.pstat { padding: 12px 14px; text-align: center; border-right: 1px solid var(--border); }
.pstat:last-child { border-right: none; }
.pstat-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 4px; }
.pstat-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px; font-weight: 600; color: var(--text);
}
.pstat-val.green { color: #16a34a; }
.pstat-val.red   { color: var(--red); }

/* Budget bars inside card */
.period-budgets { padding: 14px 16px; }
.bud-row { margin-bottom: 10px; }
.bud-row:last-child { margin-bottom: 0; }
.bud-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
.bud-name { font-size: 12px; font-weight: 600; color: var(--text); }
.bud-pct  { font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: var(--muted); }
.prog-track { height: 5px; background: var(--bg); border-radius: 10px; overflow:hidden; border: 1px solid var(--border); }
.prog-fill {
    height: 100%; border-radius: 10px; width: 0;
    transition: width 1.2s cubic-bezier(0.4,0,0.2,1);
    position: relative; overflow: hidden;
}
.prog-fill::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
    background-size: 200%; animation: shimmer 2s infinite;
}
.prog-fill.ok      { background: linear-gradient(90deg, #00e87a, #00c468); }
.prog-fill.warning { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.prog-fill.danger  { background: linear-gradient(90deg, #fb7185, #ff4d6d); }
.bud-sub { font-size: 10px; color: var(--muted); margin-top: 3px; }

/* No budget */
.no-budget { font-size: 12px; color: var(--muted); text-align: center; padding: 10px 0; }

/* Card footer */
.period-foot {
    padding: 10px 14px;
    border-top: 1px solid var(--border);
    display: flex; justify-content: flex-end; gap: 7px;
    background: var(--bg);
}

/* Empty state */
.empty-state {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 60px 24px;
    text-align: center; box-shadow: var(--shadow-sm);
}
.empty-icon { font-size: 40px; margin-bottom: 12px; }
.empty-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
.empty-sub { font-size: 13px; color: var(--muted); margin-bottom: 20px; }

@media (max-width: 600px) {
    .period-grid { grid-template-columns: 1fr; }
    .page-top { flex-direction: column; gap: 10px; align-items: stretch; }
    .page-top .btn { justify-content: center; }
}
</style>
@endpush

@section('content')

<div class="page-top float-in" style="animation-delay:.05s">
    <div style="font-size:13px;color:var(--muted);">
        Kelola anggaran bulanan tim
    </div>
    <a href="{{ route('finance.budgets.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Anggaran
    </a>
</div>

@if($periods->isEmpty())
    <div class="empty-state float-in" style="animation-delay:.1s">
        <div class="empty-icon">📋</div>
        <div class="empty-title">Belum ada anggaran</div>
        <div class="empty-sub">Buat anggaran bulanan untuk memantau pengeluaran tim</div>
        <a href="{{ route('finance.budgets.create') }}" class="btn btn-primary">Buat Anggaran Pertama</a>
    </div>
@else
    <div class="period-grid">
        @foreach($periods as $i => $period)
            @php
                $expense   = $period->total_expense ?? 0;
                $income    = $period->total_income  ?? 0;
                $remaining = $period->total_budget - $expense;
                $pct       = $period->total_budget > 0 ? round(($expense / $period->total_budget) * 100) : 0;
                $budgets   = $period->budgets ?? collect();
            @endphp
            <div class="period-card float-in" style="animation-delay:{{ 0.1 + $i * 0.06 }}s">
                {{-- Head --}}
                <div class="period-head">
                    <div class="period-head-inner">
                        <div class="period-name">{{ $period->name }}</div>
                        <div class="period-status-row">
                            <span class="period-total">Total: Rp {{ number_format($period->total_budget, 0, ',', '.') }}</span>
                            <span class="status-dot {{ $period->status === 'active' ? 'status-active' : 'status-closed' }}">
                                {{ $period->status === 'active' ? 'Aktif' : 'Ditutup' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="period-stats">
                    <div class="pstat">
                        <div class="pstat-label">Pemasukan</div>
                        <div class="pstat-val green">{{ number_format($income/1000, 0, ',', '.') }}rb</div>
                    </div>
                    <div class="pstat">
                        <div class="pstat-label">Pengeluaran</div>
                        <div class="pstat-val red">{{ number_format($expense/1000, 0, ',', '.') }}rb</div>
                    </div>
                    <div class="pstat">
                        <div class="pstat-label">Sisa</div>
                        <div class="pstat-val {{ $remaining >= 0 ? 'green' : 'red' }}">
                            {{ number_format($remaining/1000, 0, ',', '.') }}rb
                        </div>
                    </div>
                </div>

                {{-- Budget bars --}}
                <div class="period-budgets">
                    @if($budgets->isEmpty())
                        <div class="no-budget">Belum ada kategori anggaran</div>
                    @else
                        @foreach($budgets->take(4) as $b)
                            @php $bp = $b->percentage_used; $bc = $bp >= 90 ? 'danger' : ($bp >= 75 ? 'warning' : 'ok'); @endphp
                            <div class="bud-row">
                                <div class="bud-top">
                                    <span class="bud-name">{{ $b->category->name }}</span>
                                    <span class="bud-pct">{{ $bp }}%</span>
                                </div>
                                <div class="prog-track">
                                    <div class="prog-fill {{ $bc }}" data-width="{{ min($bp, 100) }}"></div>
                                </div>
                                <div class="bud-sub">
                                    Rp {{ number_format($b->used_amount, 0, ',', '.') }} / Rp {{ number_format($b->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                        @if($budgets->count() > 4)
                            <div class="no-budget">+ {{ $budgets->count() - 4 }} kategori lainnya</div>
                        @endif
                    @endif
                </div>

                {{-- Footer --}}
                <div class="period-foot">
                    <a href="{{ route('finance.budgets.edit', $period) }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('finance.budgets.destroy', $period) }}"
                          onsubmit="return confirm('Hapus anggaran {{ $period->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="font-size:11.5px;padding:6px 12px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    @if($periods->hasPages())
        <div style="display:flex;justify-content:center;margin-top:20px;">
            {{ $periods->links() }}
        </div>
    @endif
@endif

@endsection

@push('scripts')
<script>
setTimeout(() => {
    document.querySelectorAll('.prog-fill[data-width]').forEach(el => {
        el.style.width = el.dataset.width + '%';
    });
}, 500);
</script>
@endpush