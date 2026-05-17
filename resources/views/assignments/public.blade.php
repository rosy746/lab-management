{{-- resources/views/assignments/public.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Pengumpulan Tugas')

@section('vite')
@vite(['resources/css/assignment.css'])
@endsection

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="hero">
    <p class="hero-eyebrow">Sistem Pengumpulan Tugas</p>
    <h1>📋 Kumpulkan Tugasmu</h1>
    <p>Pilih tugas · Upload file · Tanpa perlu login</p>
</div>

{{-- ═══ MAIN WRAP ═══ --}}
<div class="wrap">

    @if(session('success'))
    <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->has('error'))
    <div class="flash flash-err">⚠ {{ $errors->first('error') }}</div>
    @endif

    {{-- Filter --}}
    <div class="filter-row">
        <div style="flex:1;min-width:240px">
            <label class="filter-label">Cari Tugas (Kelas / Guru / Judul)</label>
            <div style="position:relative">
                <input type="text" id="search-global" class="inp"
                    style="width:100%;padding-left:36px"
                    placeholder="Ketik nama kelas atau guru..."
                    oninput="filterGlobal()">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9ca3af"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <div>
                <label class="filter-label" for="filter-org">Lembaga</label>
                <select id="filter-org" class="inp" style="min-width:180px" onchange="filterByOrg(this.value)">
                    <option value="">— Semua Lembaga —</option>
                    @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label" for="filter-kelas">Kelas</label>
                <select id="filter-kelas" class="inp" style="min-width:180px"
                    onchange="filterByKelas(this.value)" disabled>
                    <option value="">— Pilih Lembaga Dulu —</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Data kelas per org untuk JS --}}
    <script>
        window.CLASSES_BY_ORG = @json($classes->groupBy('organization_id'));
    </script>

    {{-- STATE 1: Belum pilih (default) --}}
    <div id="state-pick" class="state-box">
        <div class="state-pick-wrap" style="background:#fff;border-radius:24px;border:1px solid #e8f0e6;box-shadow:0 1px 10px rgba(26,37,23,.04)">
            <div style="font-size:64px;margin-bottom:20px;filter:drop-shadow(0 4px 12px rgba(0,0,0,0.1))">✨</div>
            <div style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:24px;color:#1A2517">Halo, Selamat Datang!</div>
            <div style="font-size:14px;color:#6b7280;margin-top:10px;max-width:340px;margin-left:auto;margin-right:auto;line-height:1.7">
                Gunakan <strong>Pencarian Cepat</strong> di atas atau pilih <strong>Lembaga & Kelas</strong> untuk melihat daftar tugas yang harus kamu kumpulkan hari ini.
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;max-width:400px;margin:32px auto 0;text-align:left">
                @foreach(['Cari kelas atau namamu di kolom pencarian', 'Pilih tugas yang sesuai dan klik "Kumpulkan"', 'Upload file tugasmu dan selesai!'] as $i => $step)
                <div style="display:flex;align-items:center;gap:14px;padding:16px;background:#fcfdfb;border-radius:16px;border:1px solid #f0f4ee">
                    <div style="width:32px;height:32px;border-radius:10px;background:#1A2517;color:#ACC8A2;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0">{{ $i + 1 }}</div>
                    <div style="font-size:13px;font-weight:600;color:#1A2517">{{ $step }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- STATE 2: Tidak ada tugas --}}
    <div id="state-empty" class="empty-noresult" style="display:none">
        <div class="empty-noresult-icon">📭</div>
        <div style="font-weight:700;color:#374151;margin-bottom:4px">Tidak ada tugas untuk kelas ini</div>
        <div style="font-size:13px;color:#9ca3af">Belum ada tugas yang diberikan untuk kelas yang dipilih</div>
    </div>

    {{-- STATE 3: Grid tugas --}}
    @if(!$assignments->isEmpty())
    <div class="assignment-grid" id="assignment-grid" style="display:none">
        @foreach($assignments as $a)
        @php
            $expired       = $a->isExpired();
            $diffHrs       = now()->diffInHours($a->deadline, false);
            $deadlineClass = $expired ? 'deadline-urgent' : ($diffHrs < 24 ? 'deadline-soon' : 'deadline-ok');
            $deadlineLabel = $expired
                ? 'Deadline terlewat'
                : ($diffHrs < 1
                    ? 'Kurang dari 1 jam!'
                    : ($diffHrs < 24 ? "Sisa {$diffHrs} jam" : $a->deadline->translatedFormat('d M Y, H:i')));
        @endphp
        <div class="acard"
             data-kelas="{{ strtolower($a->class_name) }}"
             data-search="{{ strtolower($a->title . ' ' . $a->subject_name . ' ' . $a->teacher->name . ' ' . $a->class_name) }}"
             data-deadline="{{ $a->deadline->toISOString() }}"
             style="display:none">

            <div class="acard-head">
                <div>
                    <div class="acard-title">{{ $a->title }}</div>
                    <div class="acard-subject">{{ $a->subject_name }} · {{ $a->teacher->name }}</div>
                </div>
                <span class="badge-status {{ $expired ? 'badge-closed' : 'badge-open' }}">
                    {{ $expired ? 'Ditutup' : 'Buka' }}
                </span>
            </div>

            <div class="acard-body">
                <div class="acard-meta">
                    <span class="meta-item">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        {{ $a->class_name }}
                    </span>
                    <span class="meta-item {{ $deadlineClass }} countdown-timer" data-time="{{ $a->deadline->toISOString() }}">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $deadlineLabel }}</span>
                    </span>
                    <span class="meta-item">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $a->submissions_count }} dikumpulkan
                    </span>
                </div>
                @if($a->description)
                <div class="acard-desc">{{ Str::limit($a->description, 120) }}</div>
                @endif
            </div>

            <div class="acard-foot">
                <span class="sub-count">PDF, Word, PPT, Excel, ZIP · maks 10MB</span>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    @if($a->attachment_path)
                    <a href="{{ route('assignment.download.attachment', $a) }}"
                       class="btn-kumpul"
                       style="background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#93c5fd">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4 4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        📎 Unduh Soal ({{ $a->attachment_size }})
                    </a>
                    @endif
                    @if(!$expired)
                    <a href="{{ route('assignment.show', $a) }}" class="btn-kumpul">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Kumpulkan
                    </a>
                    @else
                    <span class="btn-kumpul btn-kumpul-disabled">Ditutup</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- JS assignment — inline karena kecil dan tidak perlu file terpisah --}}
<script>
function showState(state) {
    document.getElementById('state-pick').style.display  = state === 'pick'  ? '' : 'none';
    document.getElementById('state-empty').style.display = state === 'empty' ? '' : 'none';
    var grid = document.getElementById('assignment-grid');
    if (grid) grid.style.display = state === 'grid' ? '' : 'none';
}

function filterByOrg(orgId) {
    var kelasSel = document.getElementById('filter-kelas');
    if (orgId && window.CLASSES_BY_ORG[orgId]) {
        kelasSel.innerHTML = '<option value="">— Pilih Kelas —</option>';
        window.CLASSES_BY_ORG[orgId].forEach(function(k) {
            var opt = document.createElement('option');
            opt.value       = k.name.toLowerCase();
            opt.textContent = k.name;
            kelasSel.appendChild(opt);
        });
        kelasSel.disabled = false;
    } else {
        kelasSel.innerHTML = '<option value="">— Pilih Lembaga Dulu —</option>';
        kelasSel.disabled = true;
    }
    showState('pick');
}

function filterByKelas(val) {
    if (!val) { showState('pick'); return; }
    document.getElementById('search-global').value = '';
    var cards = document.querySelectorAll('.acard');
    var shown = 0;
    cards.forEach(function(c) {
        var match = c.dataset.kelas === val.toLowerCase();
        c.style.display = match ? '' : 'none';
        if (match) shown++;
    });
    showState(shown === 0 ? 'empty' : 'grid');
}

function filterGlobal() {
    var search = document.getElementById('search-global').value.toLowerCase();
    if (!search || search.length < 2) {
        document.getElementById('filter-org').value   = '';
        document.getElementById('filter-kelas').value = '';
        document.getElementById('filter-kelas').disabled = true;
        showState('pick');
        return;
    }
    document.getElementById('filter-org').value   = '';
    document.getElementById('filter-kelas').value = '';
    var cards = document.querySelectorAll('.acard');
    var shown = 0;
    cards.forEach(function(c) {
        var match = c.dataset.search.includes(search);
        c.style.display = match ? '' : 'none';
        if (match) shown++;
    });
    showState(shown === 0 ? 'empty' : 'grid');
}

// ─── COUNTDOWN TIMER ──────────────────────────────────────
function updateCountdowns() {
    var now = new Date();
    document.querySelectorAll('.countdown-timer').forEach(function(el) {
        var deadline = new Date(el.dataset.time);
        var diff = deadline - now;
        if (diff < 0) {
            el.querySelector('span').textContent = 'Deadline terlewat';
            el.className = 'meta-item deadline-urgent';
            return;
        }
        var hrs  = Math.floor(diff / (1000 * 60 * 60));
        var mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var secs = Math.floor((diff % (1000 * 60)) / 1000);
        if (hrs > 48) return; // biarkan label default kalau masih lama
        var label = hrs > 0 ? ('Sisa ' + hrs + 'j ' + mins + 'm') : ('Sisa ' + mins + 'm ' + secs + 'd');
        el.querySelector('span').textContent = label;
        el.className = 'meta-item ' + (hrs < 24 ? 'deadline-soon' : 'deadline-ok');
    });
}

setInterval(updateCountdowns, 1000);

document.addEventListener('DOMContentLoaded', function() {
    showState('pick');
    updateCountdowns();
});
</script>

@endsection