<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Resource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Cache stats 60 detik — tidak perlu query setiap halaman login dibuka
        $stats = Cache::remember('login_page_stats', 60, function () {
            return [
                'labs'    => Resource::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'today'   => Booking::whereDate('created_at', today())->count(),
            ];
        });

        return view('auth.login', compact('stats'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Rate limiting — max 5 percobaan per menit per username+IP
        $key = 'login.' . $request->input('username') . '.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('username'));
        }

        // Cek user
        $user = User::where('username', $request->username)
                    ->where('is_active', 1)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            RateLimiter::hit($key, 60);
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username'));
        }

        // Login berhasil — clear cache stats karena booking pending mungkin berubah
        RateLimiter::clear($key);
        Cache::forget('login_page_stats');

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}