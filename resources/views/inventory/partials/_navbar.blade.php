<nav class="pub-navbar anim-navbar">
    <div class="pub-inner">
        <a href="{{ route('home') }}" class="pub-brand">
            <div class="pub-brand-icon">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#ACC8A2" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="pub-brand-name">Lab Management</div>
                <div class="pub-brand-sub">Nuris Jember</div>
            </div>
        </a>
        <div class="pub-links">
            <a href="{{ route('home') }}"              class="pub-link">Jadwal</a>
            <a href="{{ route('inventory.public') }}"  class="pub-link on">Inventaris</a>
            <a href="{{ route('rekap.public') }}"      class="pub-link">Rekap</a>
            <a href="{{ route('assignment.public') }}" class="pub-link">Tugas</a>
            @auth
                <a href="{{ route('dashboard') }}" class="pub-btn">Dashboard →</a>
            @else
                <a href="{{ route('login') }}"     class="pub-btn">Login →</a>
            @endauth
        </div>
    </div>
</nav>