@extends('finance.layouts.app')
@section('title', 'Edit User — Keuangan Tim')
@section('page-title', 'Edit User')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

.form-wrap { max-width: 540px; }

/* Avatar */
.avatar-preview-wrap { display: flex; justify-content: center; margin-bottom: 24px; }
.avatar-preview {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; font-weight: 700; color: white;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.avatar-preview.role-admin     { background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 6px 20px rgba(124,58,237,0.35); }
.avatar-preview.role-bendahara { background: linear-gradient(135deg, #1d4ed8, #1e40af); box-shadow: 0 6px 20px rgba(29,78,216,0.35); }

/* Role toggle */
.role-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.role-opt { position: relative; }
.role-opt input { position: absolute; opacity:0; width:0; height:0; }
.role-lbl {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 14px 12px;
    border: 2px solid var(--border); border-radius: 12px;
    font-size: 13px; font-weight: 600; color: var(--muted);
    cursor: pointer; transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    text-align: center;
}
.role-lbl .role-ico { font-size: 20px; transition: transform 0.25s; }
.role-opt input[value="admin"]:checked ~ .role-lbl {
    border-color: #7c3aed; background: #faf5ff; color: #7c3aed;
    transform: scale(1.03); box-shadow: 0 4px 16px rgba(124,58,237,0.2);
}
.role-opt input[value="admin"]:checked ~ .role-lbl .role-ico { transform: scale(1.2); }
.role-opt input[value="bendahara"]:checked ~ .role-lbl {
    border-color: #1d4ed8; background: #eff6ff; color: #1d4ed8;
    transform: scale(1.03); box-shadow: 0 4px 16px rgba(29,78,216,0.2);
}
.role-opt input[value="bendahara"]:checked ~ .role-lbl .role-ico { transform: scale(1.2); }
.role-lbl-disabled { opacity: 0.5; cursor: not-allowed; }

/* Active toggle */
.active-toggle { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; background: var(--bg); border-radius: 10px; border: 1px solid var(--border); }
.active-info .toggle-title { font-size: 13px; font-weight: 600; color: var(--text); }
.active-info .toggle-desc  { font-size: 11px; color: var(--muted); margin-top: 2px; }
.switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute; inset: 0; border-radius: 24px;
    background: var(--border); cursor: pointer;
    transition: background 0.25s;
}
.slider::before {
    content: ''; position: absolute;
    width: 18px; height: 18px; border-radius: 50%;
    background: white; left: 3px; top: 3px;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.switch input:checked + .slider { background: var(--accent2); }
.switch input:checked + .slider::before { transform: translateX(20px); }

/* Password note */
.pw-note {
    background: rgba(0,232,122,0.05); border: 1px solid rgba(0,232,122,0.15);
    border-radius: 9px; padding: 10px 14px;
    font-size: 12px; color: var(--g2);
    display: flex; gap: 7px; align-items: flex-start;
    margin-bottom: 6px;
}
.pw-note svg { width: 13px; height: 13px; flex-shrink: 0; margin-top: 1px; }

/* Password strength */
.pw-strength { display: flex; gap: 4px; margin-top: 6px; }
.pw-bar { flex: 1; height: 3px; border-radius: 3px; background: var(--border); transition: background 0.3s; }
.pw-bar.weak   { background: var(--red); }
.pw-bar.medium { background: var(--amber); }
.pw-bar.strong { background: var(--accent2); }

.form-actions { display: flex; gap: 10px; margin-top: 24px; }

/* Self badge */
.self-note {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(0,232,122,0.08); border: 1px solid rgba(0,232,122,0.2);
    color: var(--accent2); padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; margin-left: 8px;
}
</style>
@endpush

@section('content')
<div class="form-wrap float-in" style="animation-delay:.05s">
    <div class="card">
        <div class="card-head">
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="card-title">Edit User</span>
                @if($user->id === auth('finance')->id())
                    <span class="self-note">✦ Akun Saya</span>
                @endif
            </div>
            <a href="{{ route('finance.users.index') }}" class="btn btn-secondary" style="font-size:11.5px;padding:6px 12px;">← Kembali</a>
        </div>
        <div class="card-body">

            {{-- Avatar Preview --}}
            <div class="avatar-preview-wrap">
                <div class="avatar-preview role-{{ $user->role }}" id="avatarPreview">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>

            <form method="POST" action="{{ route('finance.users.update', $user) }}">
                @csrf @method('PUT')

                {{-- Role --}}
                <div class="form-group">
                    <label class="form-label">Role</label>
                    @if($user->id === auth('finance')->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div class="role-toggle" style="opacity:0.6;pointer-events:none;">
                    @else
                        <div class="role-toggle">
                    @endif
                        <div class="role-opt">
                            <input type="radio" name="role" id="r_admin" value="admin"
                                   {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}
                                   onchange="updateAvatar()" {{ $user->id === auth('finance')->id() ? 'disabled' : '' }}>
                            <label for="r_admin" class="role-lbl">
                                <div class="role-ico">👑</div>
                                Admin
                            </label>
                        </div>
                        <div class="role-opt">
                            <input type="radio" name="role" id="r_bend" value="bendahara"
                                   {{ old('role', $user->role) === 'bendahara' ? 'checked' : '' }}
                                   onchange="updateAvatar()" {{ $user->id === auth('finance')->id() ? 'disabled' : '' }}>
                            <label for="r_bend" class="role-lbl">
                                <div class="role-ico">📊</div>
                                Bendahara
                            </label>
                        </div>
                    </div>
                    @if($user->id === auth('finance')->id())
                        <p class="form-hint">Tidak bisa mengubah role akun sendiri</p>
                    @endif
                </div>

                {{-- Nama --}}
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $user->name) }}" required
                           oninput="updateAvatar()">
                    @error('name')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                {{-- No HP --}}
                <div class="form-group">
                    <label class="form-label" for="phone">No. HP <span style="font-weight:400;color:var(--muted)">(opsional)</span></label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                </div>

                {{-- Status Aktif --}}
                @if($user->id !== auth('finance')->id())
                    <div class="form-group">
                        <div class="active-toggle">
                            <div class="active-info">
                                <div class="toggle-title">Status Akun</div>
                                <div class="toggle-desc">Nonaktifkan untuk blokir login user ini</div>
                            </div>
                            <label class="switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password Baru</label>
                    <div class="pw-note">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Kosongkan jika tidak ingin mengganti password
                    </div>
                    <input type="password" name="password" id="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Isi untuk mengganti password"
                           oninput="checkStrength(this.value)">
                    <div class="pw-strength">
                        <div class="pw-bar" id="b1"></div>
                        <div class="pw-bar" id="b2"></div>
                        <div class="pw-bar" id="b3"></div>
                        <div class="pw-bar" id="b4"></div>
                    </div>
                    @error('password')<p class="invalid-feedback">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
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
    const name = document.getElementById('name').value.trim();
    const role = document.querySelector('input[name="role"]:checked')?.value || '{{ $user->role }}';
    const el   = document.getElementById('avatarPreview');
    el.textContent = name ? name.charAt(0).toUpperCase() : '?';
    el.className   = 'avatar-preview role-' + role;
}

function checkStrength(pw) {
    let score = 0;
    if (pw.length >= 8)           score++;
    if (/[A-Z]/.test(pw))        score++;
    if (/[0-9]/.test(pw))        score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    const cls = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
    ['b1','b2','b3','b4'].forEach((id, i) => {
        document.getElementById(id).className = 'pw-bar' + (i < score ? ' ' + cls : '');
    });
}
</script>
@endpush