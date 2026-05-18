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
    <p>Masukkan PIN kelas untuk melihat tugasmu</p>
</div>

{{-- ═══ MAIN WRAP ═══ --}}
<div class="wrap">

    @if(session('success'))
    <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->has('error'))
    <div class="flash flash-err">⚠ {{ $errors->first('error') }}</div>
    @endif

    @if(!$activeClass)
    {{-- ══════════════════════════════════════════════════════
         LAYAR 1 — Entry PIN
         ══════════════════════════════════════════════════════ --}}
    <div class="pin-screen">

        <div class="pin-icon">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#3d6b3d" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <div class="pin-title">Masukkan PIN Kelas</div>
        <div class="pin-sub">
            PIN 6 digit diberikan oleh gurumu.<br>
            Tanyakan ke guru jika belum punya.
        </div>

        @if($errors->has('pin'))
        <div class="pin-error-msg">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ $errors->first('pin') }}
        </div>
        @endif

        {{-- PIN Display Boxes --}}
        <div class="pin-boxes" id="pin-boxes" aria-live="polite">
            @for($i = 0; $i < 6; $i++)
            <div class="pin-box" id="pin-box-{{ $i }}"></div>
            @endfor
        </div>

        {{-- Hidden form --}}
        <form method="POST" action="{{ route('assignment.pin.verify') }}" id="pin-form">
            @csrf
            <input type="hidden" name="pin" id="pin-value">

            {{-- Keypad --}}
            <div class="pin-keypad" role="group" aria-label="Keypad PIN">
                @foreach([1,2,3,4,5,6,7,8,9] as $num)
                <button type="button" class="pin-key" onclick="pinPress('{{ $num }}')" aria-label="Angka {{ $num }}">
                    {{ $num }}
                </button>
                @endforeach
                <button type="button" class="pin-key" style="visibility:hidden" aria-hidden="true"></button>
                <button type="button" class="pin-key pin-key-0" onclick="pinPress('0')" aria-label="Angka 0">0</button>
                <button type="button" class="pin-key pin-key-del" onclick="pinDelete()" aria-label="Hapus">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                    </svg>
                </button>
            </div>

            <button type="submit" class="btn-pin-submit" id="btn-submit" disabled>
                Lihat Tugasku →
            </button>
        </form>

        <div class="pin-hint">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            PIN tersimpan di sesi browser ini
        </div>
    </div>

    @else
    {{-- ══════════════════════════════════════════════════════
         LAYAR 2 — Daftar tugas kelas
         ══════════════════════════════════════════════════════ --}}

    {{-- Header kelas aktif --}}
    <div class="class-header">
        <div class="class-header-left">
            <div class="class-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="class-name">{{ $activeClass->name }}</div>
                <div class="class-sub">
                    {{ $activeClass->organization->name ?? '-' }}
                    · {{ $assignments->count() }} tugas aktif
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('assignment.pin.clear') }}">
            @csrf
            <button type="submit" class="btn-ganti">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Ganti Kelas
            </button>
        </form>
    </div>

    {{-- Daftar tugas --}}
    @if($assignments->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <div style="font-weight:700;color:#374151;margin-bottom:4px">Tidak ada tugas aktif</div>
        <div style="font-size:13px;color:#9ca3af">Belum ada tugas yang diberikan untuk kelas ini. Cek lagi nanti.</div>
    </div>
    @else
    <div class="assignment-grid">
        @foreach($assignments as $a)
        @php
            $expired       = $a->isExpired();
            $diffHrs       = now()->diffInHours($a->deadline, false);
            $isUrgent      = !$expired && $diffHrs < 24;
            $badgeClass    = $expired ? 'badge-closed' : ($isUrgent ? 'badge-urgent' : 'badge-open');
            $badgeLabel    = $expired ? 'Ditutup' : ($isUrgent ? 'Mendesak' : 'Buka');

            $deadlineClass = $expired ? 'pill-neutral' : ($diffHrs < 6 ? 'pill-urgent' : ($diffHrs < 24 ? 'pill-soon' : 'pill-ok'));
            $deadlineLabel = $expired
                ? 'Deadline terlewat'
                : ($diffHrs < 1
                    ? 'Kurang dari 1 jam!'
                    : ($diffHrs < 24
                        ? 'Sisa ' . $diffHrs . ' jam'
                        : $a->deadline->translatedFormat('d M Y, H:i')));
        @endphp
        <div class="acard {{ $expired ? 'acard-closed' : '' }}"
             data-deadline="{{ $a->deadline->toISOString() }}">

            <div class="acard-head">
                <div>
                    <div class="acard-title">{{ $a->title }}</div>
                    <div class="acard-subject">{{ $a->subject_name }} · {{ $a->teacher->name }}</div>
                </div>
                <span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
            </div>

            <div class="acard-body">
                <div class="acard-meta">
                    {{-- Deadline --}}
                    <span class="meta-pill {{ $deadlineClass }} countdown-item" data-time="{{ $a->deadline->toISOString() }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $deadlineLabel }}</span>
                    </span>

                    {{-- Jumlah dikumpulkan --}}
                    <span class="meta-pill pill-neutral">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $a->submissions_count }} dikumpulkan
                    </span>

                    {{-- Ada soal --}}
                    @if($a->attachment_path)
                    <span class="meta-pill pill-blue">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Ada soal
                    </span>
                    @endif
                </div>

                <div class="acard-actions">
                    {{-- Unduh soal --}}
                    @if($a->attachment_path)
                    <a href="{{ route('assignment.download.attachment', $a) }}" class="btn-unduh">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Soal
                    </a>
                    @endif

                    {{-- Kumpulkan / Ditutup --}}
                    @if(!$expired)
                    <a href="{{ route('assignment.show', $a) }}" class="btn-kumpul">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Kumpulkan
                    </a>
                    @else
                    <span class="btn-disabled">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Ditutup
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @endif {{-- end if activeClass --}}

</div>

{{-- JS PIN & Countdown --}}
<script>
var pinValue = '';
var MAX_PIN  = 6;

function updateBoxes() {
    for (var i = 0; i < MAX_PIN; i++) {
        var box = document.getElementById('pin-box-' + i);
        if (!box) return;
        if (i < pinValue.length) {
            box.textContent = '●';
            box.className = 'pin-box filled';
        } else if (i === pinValue.length) {
            box.textContent = '';
            box.className = 'pin-box active';
        } else {
            box.textContent = '';
            box.className = 'pin-box';
        }
    }
    var btn = document.getElementById('btn-submit');
    if (btn) btn.disabled = pinValue.length < MAX_PIN;
    var inp = document.getElementById('pin-value');
    if (inp) inp.value = pinValue;
}

function pinPress(digit) {
    if (pinValue.length >= MAX_PIN) return;
    pinValue += digit;
    updateBoxes();
    if (pinValue.length === MAX_PIN) {
        setTimeout(function() {
            document.getElementById('pin-form').submit();
        }, 120);
    }
}

function pinDelete() {
    if (pinValue.length === 0) return;
    pinValue = pinValue.slice(0, -1);
    updateBoxes();
}

// Keyboard support
document.addEventListener('keydown', function(e) {
    if (e.key >= '0' && e.key <= '9') { pinPress(e.key); }
    else if (e.key === 'Backspace')    { pinDelete(); }
    else if (e.key === 'Enter' && pinValue.length === MAX_PIN) {
        document.getElementById('pin-form')?.submit();
    }
});

// Init boxes kalau ada form PIN
if (document.getElementById('pin-boxes')) updateBoxes();

// ─── COUNTDOWN ──────────────────────────────────────────────
function updateCountdowns() {
    var now = new Date();
    document.querySelectorAll('.countdown-item').forEach(function(el) {
        var deadline = new Date(el.dataset.time);
        var diff = deadline - now;
        if (diff < 0) {
            el.querySelector('span').textContent = 'Deadline terlewat';
            el.className = 'meta-pill pill-neutral countdown-item';
            return;
        }
        var hrs  = Math.floor(diff / 3600000);
        var mins = Math.floor((diff % 3600000) / 60000);
        var secs = Math.floor((diff % 60000) / 1000);
        if (hrs > 48) return;
        var label = hrs > 0
            ? 'Sisa ' + hrs + 'j ' + mins + 'm'
            : 'Sisa ' + mins + 'm ' + secs + 'd';
        el.querySelector('span').textContent = label;
        el.className = 'meta-pill countdown-item ' + (hrs < 6 ? 'pill-urgent' : (hrs < 24 ? 'pill-soon' : 'pill-ok'));
    });
}

if (document.querySelector('.countdown-item')) {
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
}
</script>

@endsection