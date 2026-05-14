{{--
    Partial: _empty_state.blade.php
    Props:
      $type  — 'no-data' | 'no-result'
      $title — judul pesan
      $desc  — deskripsi pesan
--}}
<div class="empty-state">
    @if($type === 'no-data')
        <div class="empty-icon no-data">
            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2zM8 21h8M12 17v4"/>
            </svg>
        </div>
        <p class="empty-title">{{ $title }}</p>
        <p class="empty-desc">{{ $desc }}</p>
        <div class="btn-group">
            @auth
                <a href="{{ route('dashboard') }}" class="empty-btn primary">Tambah Inventaris →</a>
            @else
                <a href="{{ route('login') }}" class="empty-btn primary">Login sebagai Admin →</a>
            @endauth
        </div>
    @else
        <div class="empty-icon no-result">
            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#92400e" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M8 11h6"/>
            </svg>
        </div>
        <p class="empty-title">{{ $title }}</p>
        <p class="empty-desc">{{ $desc }}</p>
        <div class="btn-group">
            <button class="empty-btn primary" onclick="resetFilter()">Reset Filter</button>
        </div>
    @endif
</div>