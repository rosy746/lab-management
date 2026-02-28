@extends('finance.layouts.app')
@section('title', 'Kelola User — Keuangan Tim')
@section('page-title', 'Kelola User')

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
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

/* ── Page Top ── */
.page-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }

/* ── User Cards Grid ── */
.user-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.user-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s;
    animation: rowIn 0.4s ease both;
}
.user-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.09);
}
.user-card.is-me {
    border-color: rgba(0,196,104,0.3);
    box-shadow: 0 0 0 2px rgba(0,232,122,0.1);
}

/* Card head */
.ucard-head {
    padding: 20px 20px 16px;
    display: flex; flex-direction: column; align-items: center;
    text-align: center;
    position: relative; overflow: hidden;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
}
.ucard-head::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, transparent 60%, rgba(0,232,122,0.05));
    pointer-events: none;
}

/* Avatar */
.u-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 700;
    margin-bottom: 12px;
    position: relative;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    transition: transform 0.2s;
}
.user-card:hover .u-avatar { transform: scale(1.05); }
.u-avatar.role-admin     { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; }
.u-avatar.role-bendahara { background: linear-gradient(135deg, #1d4ed8, #1e40af); color: white; }

/* Online dot */
.u-status {
    position: absolute; bottom: 2px; right: 2px;
    width: 14px; height: 14px; border-radius: 50%;
    border: 2px solid var(--bg);
}
.u-status.active   { background: var(--accent); box-shadow: 0 0 6px var(--accent); }
.u-status.inactive { background: var(--muted); }

.u-name { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.u-email { font-size: 11.5px; color: var(--muted); margin-bottom: 10px; }

.u-badges { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
.role-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 10.5px; font-weight: 700;
}
.role-badge.admin     { background: #ede9fe; color: #7c3aed; }
.role-badge.bendahara { background: #dbeafe; color: #1d4ed8; }
.me-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 9px; border-radius: 20px;
    font-size: 10px; font-weight: 700;
    background: rgba(0,196,104,0.12); color: var(--accent2);
    border: 1px solid rgba(0,196,104,0.2);
}

/* Card body */
.ucard-body { padding: 14px 18px; }
.u-info-row {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: var(--muted);
    margin-bottom: 8px;
}
.u-info-row:last-child { margin-bottom: 0; }
.u-info-row svg { width: 13px; height: 13px; flex-shrink: 0; }
.u-info-val { color: var(--text); font-weight: 500; }

/* Card footer */
.ucard-foot {
    padding: 10px 14px;
    border-top: 1px solid var(--border);
    display: flex; gap: 7px; justify-content: flex-end;
    background: var(--bg);
}

/* Empty */
.empty-state {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 60px 24px;
    text-align: center; box-shadow: var(--shadow-sm);
}

@media (max-width: 600px) {
    .user-grid { grid-template-columns: 1fr; }
    .page-top { flex-direction: column; gap: 10px; align-items: stretch; }
    .page-top .btn { justify-content: center; }
}
</style>
@endpush

@section('content')

<div class="page-top float-in" style="animation-delay:.05s">
    <div style="font-size:13px;color:var(--muted);">
        <strong style="color:var(--text);">{{ $users->count() }} user</strong> terdaftar di sistem keuangan
    </div>
    <a href="{{ route('finance.users.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Tambah User
    </a>
</div>

@if($users->isEmpty())
    <div class="empty-state float-in" style="animation-delay:.1s">
        <div style="font-size:40px;margin-bottom:12px;">👥</div>
        <div style="font-size:16px;font-weight:700;margin-bottom:6px;">Belum ada user</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:20px;">Tambahkan anggota tim ke sistem keuangan</div>
        <a href="{{ route('finance.users.create') }}" class="btn btn-primary">Tambah User Pertama</a>
    </div>
@else
    <div class="user-grid">
        @foreach($users as $i => $user)
            <div class="user-card {{ $user->id === auth('finance')->id() ? 'is-me' : '' }}" style="animation-delay:{{ 0.08 + $i * 0.06 }}s">

                {{-- Head --}}
                <div class="ucard-head">
                    <div class="u-avatar role-{{ $user->role }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                        <div class="u-status {{ $user->is_active ? 'active' : 'inactive' }}"></div>
                    </div>
                    <div class="u-name">{{ $user->name }}</div>
                    <div class="u-email">{{ $user->email }}</div>
                    <div class="u-badges">
                        <span class="role-badge {{ $user->role }}">
                            {{ $user->role === 'admin' ? '👑 Admin' : '📊 Bendahara' }}
                        </span>
                        @if($user->id === auth('finance')->id())
                            <span class="me-badge">✦ Saya</span>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="ucard-body">
                    <div class="u-info-row">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="u-info-val">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="u-info-row">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Status:&nbsp;<span class="u-info-val" style="color:{{ $user->is_active ? '#16a34a' : 'var(--red)' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="u-info-row">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Login:&nbsp;<span class="u-info-val">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                        </span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="ucard-foot">
                    <a href="{{ route('finance.users.edit', $user) }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    @if($user->id !== auth('finance')->id())
                        <form method="POST" action="{{ route('finance.users.destroy', $user) }}"
                              onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="font-size:11.5px;padding:6px 12px;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection