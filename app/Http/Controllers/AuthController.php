<?php

namespace App\Http\Controllers;

use App\Helpers\CustomerKey;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('services.index');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $throttleKey = 'login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Terlalu banyak percobaan, coba sebentar lagi.']);
        }

        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau No. HP wajib diisi',
            'password.required' => 'Kata sandi wajib diisi',
        ]);

        $loginInput = trim($credentials['email']);
        $password = $credentials['password'];

        $user = User::where('email', $loginInput)->first();

        if (! $user) {
            $normalizedPhone = CustomerKey::normalizePhone($loginInput);
            $tech = Technician::where('phone', $normalizedPhone)->first();
            if ($tech && $tech->user_id) {
                $user = User::find($tech->user_id);
            }
        }

        if ($user && Hash::check($password, $user->password)) {
            if ($user->isTechnician() && $user->technician && ! $user->technician->is_active) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Akun teknisi dinonaktifkan. Hubungi admin toko.']);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            return redirect()->intended(route('services.index'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau kata sandi salah']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi',
            'current_password.current_password' => 'Kata sandi saat ini salah',
            'password.required' => 'Kata sandi baru wajib diisi',
            'password.min' => 'Kata sandi baru minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui');
    }
}
