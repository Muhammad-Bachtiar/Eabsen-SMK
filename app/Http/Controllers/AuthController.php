<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRoute());
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(array_merge($credentials, ['status_aktif' => true]))) {
            $request->session()->regenerate();
            return redirect()->route($this->dashboardRoute());
        }

        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun Anda dinonaktifkan.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Tentukan route dashboard berdasarkan role user.
     */
    private function dashboardRoute(): string
    {
        $user = Auth::user();
        $role = $user->role->nama_role ?? 'admin';

        return match ($role) {
            'guru'            => 'guru.dashboard',
            'bk'              => $user->is_koordinator_bk ? 'koordinator-bk.dashboard' : 'bk.dashboard',
            'waka_kesiswaan'  => 'waka.dashboard',
            'kepala_sekolah'  => 'kepsek.dashboard',
            default           => 'admin.dashboard',
        };
    }
}
