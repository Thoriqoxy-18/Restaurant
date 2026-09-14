<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap terlalu panjang.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'terms.required' => 'Anda harus menyetujui syarat & ketentuan.',
        ];
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->to($this->homePath(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], $this->messages());

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::warning('Percobaan login gagal', ['email' => $request->input('email'), 'ip' => $request->ip()]);

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Akun yang dinonaktifkan tidak boleh login.
        if (! Auth::user()->is_active) {
            Log::warning('Login diblokir: akun nonaktif', ['email' => $request->input('email'), 'ip' => $request->ip()]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homePath(Auth::user()));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function homePath(User $user): string
    {
        return $user->role === 'admin' ? route('admin.dashboard') : route('kasir.dashboard');
    }
}
