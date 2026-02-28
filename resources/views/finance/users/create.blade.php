@extends('finance.layouts.app')
@section('title', 'Tambah User — Keuangan Tim')
@section('page-title', 'Tambah User')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

.form-wrap { max-width: 540px; }

/* ── Avatar Preview ── */
.avatar-preview-wrap {
    display: flex; justify-content: center; margin-bottom: 24px;
}
.avatar-preview {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; font-weight: 700; color: white;
    background: linear-gradient(135deg, #6b7280, #4b5563);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    letter-spacing: -1px;
}
.avatar-preview.role-admin     { background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 6px 20px rgba(124,58,237,0.35); }
.avatar-preview.role-bendahara { background: linear-gradient(135deg, #1d4ed8, #1e40af); box-shadow: 0 6px 20px rgba(29,78,216,0.35); }

/* ── Role Toggle ── */
.role-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.role-opt { position: relative; }
.role-opt input { position: absolute; opacity:0; width:0; height:0; }
.role-lbl {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 16px 12px;
    border: 2px solid var(--border); border-radius: 12px;
    font-size: 13px; font-weight: 600; color: var(--muted);
    cursor: pointer; transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    text-align: center;
}
.role-lbl .role-ico {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; background: var(--bg);
    transition: all 0.25s;
}
.role-lbl .role-desc { font-size: 10.5px; font-weight: 400; color: var(--muted); }

.role-opt input[value="admin"]:checked ~ .role-lbl {
    border-color: #7c3aed; background: #faf5ff; color: #7c3aed;
    transform: scale(1.03); box-shadow: 0 4px 16px rgba(124,58,237,0.2);
}
.role-opt input[value="admin"]:checked ~ .role-lbl .role-ico {
    background: #7c3aed; transform: scale(1.1);
}

.role-opt input[value="bendahara"]:checked ~ .role-lbl {
    border-color: #1d4ed8; background: #eff6ff; color: #1d4ed8;
    transform: scale(1.03); box-shadow: 0 4px 16px rgba(29,78,216,0.2);
}
.role-opt input[value="bendahara"]:checked ~ .role-lbl .role-ico {
    background: #1d4ed8; transform: scale(1.1);
}

/* ── Password strength ── */
.pw-strength { display: flex; gap: 4px; margin-top: 6px; }
.pw-bar { flex: 1; height: 3px; border-radius: 3px; background: var(--border); transition: background 0.3s; }
.pw-bar.weak   { background: var(--red); }
.pw-bar.medium { background: var(--amber); }
.pw-bar.strong { background: var(--accent2); }
.pw-label { font-size: 10.5px; color: var(--muted); margin-top: 4px; }

.form-actions { display: flex; gap: 10px; margin-top: 24px; }
</style>
@endpush

@section('content')
<div class="form-wrap float-in" style="animation-delay:.05s">
    <div class="card">
        <div class="card-head">
            <span class="card-title">User Baru</span>
            <a href="{{ route('finance.users.index') }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">← Kembali</a>
        </div>
        <div class="card-body">

            {{-- Avatar Preview --}}
            <div class="avatar-preview-wrap">
                <div class="avatar-preview" id="avatarPreview">?</div>
            </div>

            <form method="POST" action="{{ route('finance.users.store') }}">
                @csrf

                {{-- Role Toggle --}}
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <div class="role-toggle">
                        <div class="role-opt">
                            <input type="radio" name="role" id="r_admin" value="admin"
                                   {{ old('role') === 'admin' ? 'checked' : '' }}
                                   onchange="updateAvatar()">
                            <label for="r_admin" class="role-lbl">
                                <div class="role-ico">👑</div>
                                Admin
                                <span class="role-desc">Akses penuh + kelola user & WA</span>
                            </label>
                        </div>
                        <div class="role-opt">
                            <input type="radio" name="role" id="r_bendahara" value="bendahara"
                                   {{ old('role', 'bendahara') === 'bendahara' ? 'checked' : '' }}
                                   onchange="updateAvatar()">
                            <label for="r_bendahara" class="role-lbl">
                                <div class="role-ico">📊</div>
                                Bendahara
                                <span class="role-desc">Input & lihat transaksi, anggaran</span>
                            </label>
                        </div>
                    </div>
                    @error('role')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Nama --}}
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" required placeholder="Nama lengkap user"
                           oninput="updateAvatar()">
                    @error('name')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" required placeholder="email@contoh.com">
                    @error('email')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- No HP --}}
                <div class="form-group">
                    <label class="form-label" for="phone">
                        No. HP <span style="font-weight:400;color:var(--muted)">(opsional)</span>
                    </label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           required placeholder="Min. 8 karakter"
                           oninput="checkStrength(this.value)">
                    <div class="pw-strength">
                        <div class="pw-bar" id="b1"></div>
                        <div class="pw-bar" id="b2"></div>
                        <div class="pw-bar" id="b3"></div>
                        <div class="pw-bar" id="b4"></div>
                    </div>
                    <div class="pw-label" id="pwLabel"></div>
                    @error('password')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Simpan User
                    </button>
                    <a href="{{ route('finance.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateAvatar() {
    const name  = document.getElementById('name').value.trim();
    const role  = document.querySelector('input[name="role"]:checked')?.value || '';
    const el    = document.getElementById('avatarPreview');
    el.textContent = name ? name.charAt(0).toUpperCase() : '?';
    el.className = 'avatar-preview' + (role ? ' role-' + role : '');
}

function checkStrength(pw) {
    let score = 0;
    if (pw.length >= 8)                score++;
    if (/[A-Z]/.test(pw))             score++;
    if (/[0-9]/.test(pw))             score++;
    if (/[^A-Za-z0-9]/.test(pw))      score++;

    const bars  = ['b1','b2','b3','b4'];
    const cls   = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
    const label = score <= 1 ? '🔴 Lemah' : score <= 2 ? '🟡 Sedang' : score === 3 ? '🟢 Kuat' : '🟢 Sangat Kuat';

    bars.forEach((id, i) => {
        const el = document.getElementById(id);
        el.className = 'pw-bar' + (i < score ? ' ' + cls : '');
    });
    document.getElementById('pwLabel').textContent = pw.length ? label : '';
}

// Init
updateAvatar();
</script>
@endpush