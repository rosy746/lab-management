@extends('finance.layouts.app')
@section('title', 'Dashboard — Keuangan Tim')
@section('page-title', 'Dashboard')

@push('styles')
<style>
/* ═══════════════════════════ KEYFRAMES ═══════════════════════════ */
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(24px) scale(0.97); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0,232,122,0); }
    50%       { box-shadow: 0 0 0 6px rgba(0,232,122,0.08); }
}
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
}

.float-in {
    opacity: 0;
    animation: floatIn 0.55s cubic-bezier(0.34,1.56,0.64,1) forwards;
}

/* ═══════════════════════════ HEADER ROW ═══════════════════════════ */
.page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
}
.page-greeting { font-size: 13px; color: var(--muted); }
.page-greeting strong { color: var(--text); }

/* ═══════════════════════════ STAT CARDS ═══════════════════════════ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 16px;
}

.stat-card {
    background: var(--surface);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 20px;
    position: relative;
    overflow: hidden;
    cursor: default;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1),
                box-shadow 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-5px) scale(1.01);
    box-shadow: 0 12px 32px rgba(0,0,0,0.1);
}

/* Shine effect on hover */
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 60%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transform: skewX(-20deg);
    transition: left 0.5s ease;
    pointer-events: none;
}
.stat-card:hover::before { left: 150%; }

/* Big soft glow bg */
.stat-card .glow {
    position: absolute;
    width: 120px; height: 120px;
    border-radius: 50%;
    right: -30px; bottom: -30px;
    pointer-events: none;
    transition: transform 0.3s;
}
.stat-card:hover .glow { transform: scale(1.3); }

.s-income .glow  { background: radial-gradient(circle, rgba(0,232,122,0.12), transparent 70%); }
.s-expense .glow { background: radial-gradient(circle, rgba(255,77,109,0.12), transparent 70%); }
.s-balance .glow { background: radial-gradient(circle, rgba(7,31,20,0.08), transparent 70%); }

.stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; }
.stat-icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.stat-icon svg { width: 18px; height: 18px; position: relative; z-index: 1; }
.s-income .stat-icon  { background: #dcfce7; color: #16a34a; }
.s-expense .stat-icon { background: #fff1f2; color: var(--red); }
.s-balance .stat-icon { background: rgba(7,31,20,0.07); color: var(--g1); animation: pulse-glow 3s infinite; }

.stat-badge {
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 20px;
    letter-spacing: 0.3px;
}
.badge-green { background: #dcfce7; color: #16a34a; }
.badge-red   { background: #fff1f2; color: var(--red); }
.badge-gray  { background: var(--bg); color: var(--muted); border: 1px solid var(--border); }

.stat-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--muted); margin-bottom: 4px; }
.stat-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 24px; font-weight: 600;
    letter-spacing: -1px; line-height: 1;
    transition: color 0.3s;
}
.s-income .stat-value  { color: #16a34a; }
.s-expense .stat-value { color: var(--red); }
.s-balance .stat-value { color: var(--text); }
.stat-sub { font-size: 11px; color: var(--muted); margin-top: 5px; }

/* ═══════════════════════════ CHARTS ROW ═══════════════════════════ */
.charts-row {
    display: grid;
    grid-template-columns: 1.8fr 1fr;
    gap: 14px;
    margin-bottom: 16px;
}

/* Donut center label */
.donut-wrap { position: relative; }
.donut-center {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -54%);
    text-align: center; pointer-events: none;
}
.donut-center-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px; font-weight: 600; color: var(--text);
    line-height: 1;
}
.donut-center-label { font-size: 9px; color: var(--muted); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }

/* Pie legend */
.pie-legend { margin-top: 12px; display: flex; flex-direction: column; gap: 7px; }
.legend-row { display: flex; align-items: center; gap: 8px; font-size: 12px; padding: 4px 6px; border-radius: 7px; transition: background 0.15s; }
.legend-row:hover { background: var(--bg); }
.legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.legend-name { flex: 1; color: var(--text); font-weight: 500; }
.legend-val  { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--muted); }

/* ═══════════════════════════ BOTTOM ROW ═══════════════════════════ */
.bottom-row {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 14px;
}

/* Transaksi */
.trx-list { padding: 2px 18px 6px; }
.trx-item {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 0;
    border-bottom: 1px solid var(--border);
    transition: transform 0.15s;
    cursor: default;
}
.trx-item:last-child { border-bottom: none; }
.trx-item:hover { transform: translateX(3px); }

.trx-ico {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: transform 0.2s;
}
.trx-item:hover .trx-ico { transform: scale(1.1); }
.trx-ico.income  { background: #dcfce7; }
.trx-ico.expense { background: #fff1f2; }
.trx-ico svg { width: 14px; height: 14px; }
.trx-ico.income svg  { color: #16a34a; }
.trx-ico.expense svg { color: var(--red); }

.trx-info { flex: 1; min-width: 0; }
.trx-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.trx-meta { font-size: 10.5px; color: var(--muted); margin-top: 2px; }
.trx-amt  { font-family: 'JetBrains Mono', monospace; font-size: 12.5px; font-weight: 600; white-space: nowrap; }
.trx-amt.income  { color: #16a34a; }
.trx-amt.expense { color: var(--red); }

/* Anggaran */
.budget-list { padding: 4px 18px 8px; }
.budget-item { padding: 10px 0; border-bottom: 1px solid var(--border); }
.budget-item:last-child { border-bottom: none; padding-bottom: 0; }
.budget-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px; }
.budget-name { font-size: 12.5px; font-weight: 600; }
.budget-pct  { font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: var(--muted); }

.prog-track { height: 6px; background: var(--bg); border-radius: 10px; overflow: hidden; border: 1px solid var(--border); }
.prog-fill {
    height: 100%; border-radius: 10px;
    width: 0;
    transition: width 1.2s cubic-bezier(0.4,0,0.2,1);
    position: relative; overflow: hidden;
}
/* Shimmer inside progress bar */
.prog-fill::after {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}
.prog-fill.ok      { background: linear-gradient(90deg, #00e87a, #00c468); }
.prog-fill.warning { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.prog-fill.danger  { background: linear-gradient(90deg, #fb7185, #ff4d6d); }

.budget-sub { display: flex; justify-content: space-between; font-size: 10.5px; color: var(--muted); margin-top: 5px; }

/* Empty */
.empty-box { text-align: center; padding: 32px 16px; color: var(--muted); font-size: 13px; }
.empty-box a { color: var(--accent2); font-weight: 700; text-decoration: none; }
.empty-box a:hover { text-decoration: underline; }

/* ═══════════════════════════ RESPONSIVE ═══════════════════════════ */
@media (max-width: 1024px) {
    .charts-row { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
    .stats-grid  { grid-template-columns: 1fr 1fr; }
    .bottom-row  { grid-template-columns: 1fr; }
}
@media (max-width: 520px) {
    .stats-grid  { grid-template-columns: 1fr; }
    .stat-value  { font-size: 20px; }
}
</style>
@endpush

@section('content')

{{-- Greeting --}}
<div class="page-header float-in" style="animation-delay:.05s">
    <div class="page-greeting">
        Halo, <strong>{{ auth('finance')->user()->name }}</strong> 👋
        — Ringkasan keuangan tim bulan ini
    </div>
    <a href="{{ route('finance.transactions.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Transaksi
    </a>
</div>

{{-- Stat Cards --}}
<div class="stats-grid">
    <div class="stat-card s-income float-in" style="animation-delay:.08s">
        <div class="glow"></div>
        <div class="stat-top">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
            <span class="stat-badge badge-green">↑ Masuk</span>
        </div>
        <div class="stat-label">Pemasukan</div>
        <div class="stat-value" id="v-income" data-val="{{ $totalIncome }}">Rp 0</div>
        <div class="stat-sub">{{ now()->isoFormat('MMMM Y') }}</div>
    </div>

    <div class="stat-card s-expense float-in" style="animation-delay:.13s">
        <div class="glow"></div>
        <div class="stat-top">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
            <span class="stat-badge badge-red">↓ Keluar</span>
        </div>
        <div class="stat-label">Pengeluaran</div>
        <div class="stat-value" id="v-expense" data-val="{{ $totalExpense }}">Rp 0</div>
        <div class="stat-sub">{{ now()->isoFormat('MMMM Y') }}</div>
    </div>

    <div class="stat-card s-balance float-in" style="animation-delay:.18s">
        <div class="glow"></div>
        <div class="stat-top">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <span class="stat-badge badge-gray">Saldo</span>
        </div>
        <div class="stat-label">Kas Tunai Tim</div>
        <div class="stat-value" id="v-balance" data-val="{{ $kasBalance }}">Rp 0</div>
        <div class="stat-sub">Total saldo aktif</div>
    </div>
</div>

{{-- Charts --}}
<div class="charts-row float-in" style="animation-delay:.23s">

    {{-- Bar Chart --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">Arus Keuangan</span>
            <span style="font-size:11px;color:var(--muted);font-family:'JetBrains Mono',monospace;">6 Bulan</span>
        </div>
        <div class="card-body" style="padding-top:12px;">
            <canvas id="barChart" style="height:210px;max-height:210px;"></canvas>
        </div>
    </div>

    {{-- Donut Chart --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">Per Kategori</span>
            <span style="font-size:11px;color:var(--muted);">{{ now()->isoFormat('MMMM') }}</span>
        </div>
        <div class="card-body">
            @if($expenseByCategory->isEmpty())
                <div class="empty-box" style="padding:50px 0;">
                    <div style="font-size:28px;margin-bottom:8px;">📊</div>
                    Belum ada pengeluaran
                </div>
            @else
                <div class="donut-wrap">
                    <canvas id="pieChart" style="height:170px;max-height:170px;"></canvas>
                    <div class="donut-center">
                        <div class="donut-center-val">
                            Rp {{ number_format($expenseByCategory->sum('total')/1000, 0, ',', '.') }}rb
                        </div>
                        <div class="donut-center-label">total</div>
                    </div>
                </div>
                <div class="pie-legend">
                    @foreach($expenseByCategory as $item)
                        <div class="legend-row">
                            <div class="legend-dot" style="background:{{ $item['color'] ?? '#00e87a' }}"></div>
                            <span class="legend-name">{{ $item['name'] }}</span>
                            <span class="legend-val">{{ number_format($item['total']/1000, 0, ',', '.') }}rb</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Bottom --}}
<div class="bottom-row float-in" style="animation-delay:.28s">

    {{-- Transaksi Terbaru --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">Transaksi Terbaru</span>
            <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary" style="font-size:11px;padding:5px 10px;">
                Lihat Semua →
            </a>
        </div>
        <div class="trx-list">
            @forelse($recentTransactions as $trx)
                <div class="trx-item">
                    <div class="trx-ico {{ $trx->type }}">
                        @if($trx->type === 'income')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        @endif
                    </div>
                    <div class="trx-info">
                        <div class="trx-name">{{ $trx->description }}</div>
                        <div class="trx-meta">{{ $trx->transaction_date->format('d M Y') }} &middot; {{ $trx->category->name }}</div>
                    </div>
                    <div class="trx-amt {{ $trx->type }}">
                        {{ $trx->type === 'income' ? '+' : '−' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <div style="font-size:28px;margin-bottom:8px;">💸</div>
                    Belum ada transaksi.<br>
                    <a href="{{ route('finance.transactions.create') }}">Tambah sekarang →</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Anggaran --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">Anggaran {{ now()->isoFormat('MMMM') }}</span>
            @if($period)
                <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--muted);">
                    Rp {{ number_format($period->total_budget/1000, 0, ',', '.') }}rb
                </span>
            @endif
        </div>
        <div class="budget-list">
            @if($budgets->isEmpty())
                <div class="empty-box">
                    <div style="font-size:28px;margin-bottom:8px;">📋</div>
                    Belum ada anggaran.<br>
                    <a href="{{ route('finance.budgets.create') }}">Buat anggaran →</a>
                </div>
            @else
                @foreach($budgets as $b)
                    @php $pct = $b->percentage_used; $cls = $pct >= 90 ? 'danger' : ($pct >= 75 ? 'warning' : 'ok'); @endphp
                    <div class="budget-item">
                        <div class="budget-top">
                            <span class="budget-name">{{ $b->category->name }}</span>
                            <span class="budget-pct">{{ $pct }}%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill {{ $cls }}" data-width="{{ min($pct, 100) }}"></div>
                        </div>
                        <div class="budget-sub">
                            <span>Rp {{ number_format($b->used_amount, 0, ',', '.') }}</span>
                            <span>/ Rp {{ number_format($b->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// ── Smooth counter ────────────────────────────────────────────────
function countUp(el, target, delay = 0) {
    setTimeout(() => {
        const dur = 1400;
        const start = performance.now();
        const fmt = v => 'Rp ' + Math.round(v).toLocaleString('id-ID');
        function step(now) {
            const p    = Math.min((now - start) / dur, 1);
            const ease = 1 - Math.pow(1 - p, 4);
            el.textContent = fmt(target * ease);
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = fmt(target);
        }
        requestAnimationFrame(step);
    }, delay);
}
countUp(document.getElementById('v-income'),  {{ $totalIncome  }}, 400);
countUp(document.getElementById('v-expense'), {{ $totalExpense }}, 500);
countUp(document.getElementById('v-balance'), {{ $kasBalance   }}, 600);

// ── Progress bars ─────────────────────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.prog-fill[data-width]').forEach(el => {
        el.style.width = el.dataset.width + '%';
    });
}, 600);

// ── Chart.js defaults ─────────────────────────────────────────────
Chart.defaults.font.family = 'Sora';
Chart.defaults.color = '#706b64';

// ── Bar Chart ─────────────────────────────────────────────────────
const months   = @json($chartMonths);
const incomes  = @json($chartIncome);
const expenses = @json($chartExpense);

const barCtx = document.getElementById('barChart');
if (barCtx) {
    const barGrad1 = barCtx.getContext('2d').createLinearGradient(0,0,0,220);
    barGrad1.addColorStop(0, 'rgba(0,232,122,0.9)');
    barGrad1.addColorStop(1, 'rgba(0,196,104,0.6)');

    const barGrad2 = barCtx.getContext('2d').createLinearGradient(0,0,0,220);
    barGrad2.addColorStop(0, 'rgba(255,77,109,0.85)');
    barGrad2.addColorStop(1, 'rgba(255,77,109,0.4)');

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomes,
                    backgroundColor: barGrad1,
                    borderRadius: { topLeft: 6, topRight: 6 },
                    borderSkipped: false,
                    hoverBackgroundColor: '#00e87a',
                    barPercentage: 0.7,
                },
                {
                    label: 'Pengeluaran',
                    data: expenses,
                    backgroundColor: barGrad2,
                    borderRadius: { topLeft: 6, topRight: 6 },
                    borderSkipped: false,
                    hoverBackgroundColor: '#ff4d6d',
                    barPercentage: 0.7,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                delay: ctx => ctx.dataIndex * 80,
                duration: 700,
                easing: 'easeOutQuart',
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11, weight: '600' },
                        boxWidth: 10, boxHeight: 10, padding: 18,
                        usePointStyle: true, pointStyle: 'circle',
                    }
                },
                tooltip: {
                    backgroundColor: '#071f14',
                    titleColor: '#ffffff',
                    bodyColor: '#a3e6c8',
                    padding: 12, cornerRadius: 10,
                    titleFont: { size: 11, weight: '600' },
                    bodyFont: { size: 12, family: 'JetBrains Mono' },
                    callbacks: { label: ctx => '  Rp ' + ctx.raw.toLocaleString('id-ID') }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    border: { display: false, dash: [4,4] },
                    ticks: {
                        font: { size: 10, family: 'JetBrains Mono' },
                        callback: v => v === 0 ? '0' : (v >= 1000000
                            ? (v/1000000).toFixed(1)+'jt'
                            : (v/1000).toFixed(0)+'rb')
                    }
                }
            }
        }
    });
}

// ── Donut Chart ───────────────────────────────────────────────────
const pieCtx  = document.getElementById('pieChart');
const pieData = @json($expenseByCategory);
const fallbackColors = ['#00e87a','#ff4d6d','#ffb703','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316'];

if (pieCtx && pieData.length) {
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: pieData.map(d => d.name),
            datasets: [{
                data: pieData.map(d => d.total),
                backgroundColor: pieData.map((d,i) => d.color || fallbackColors[i % fallbackColors.length]),
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 10,
                hoverBorderWidth: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            animation: {
                animateRotate: true,
                animateScale: false,
                duration: 900,
                easing: 'easeOutQuart',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#071f14',
                    titleColor: '#ffffff',
                    bodyColor: '#a3e6c8',
                    padding: 12, cornerRadius: 10,
                    bodyFont: { size: 12, family: 'JetBrains Mono' },
                    callbacks: { label: ctx => '  Rp ' + ctx.raw.toLocaleString('id-ID') }
                }
            }
        }
    });
}
</script>
@endpush