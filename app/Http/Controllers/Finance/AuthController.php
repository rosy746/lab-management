<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ── Tampilkan Form Login ───────────────────────────────

    public function showLogin()
    {
        return view('finance.auth.login');
    }

    // ── Proses Login ──────────────────────────────────────

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Gunakan guard 'finance', bukan default 'web'
        if (Auth::guard('finance')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('finance')->user();

            // Cek akun aktif
            if (!$user->is_active) {
                Auth::guard('finance')->logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi admin.',
                ]);
            }

            // Update last_login_at
            $user->update(['last_login_at' => now()]);

            $request->session()->regenerate();

            return redirect()->intended(route('finance.dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    // ── Logout ────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::guard('finance')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('finance.login')->with('success', 'Berhasil logout.');
    }
}
