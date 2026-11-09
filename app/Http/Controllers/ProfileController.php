<?php

namespace App\Http\Controllers;

use App\Helpers\CustomerKey;
use App\Models\Technician;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'technician' => $request->user()->technician,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:60'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi',
            'current_password.required_with' => 'Kata sandi saat ini wajib diisi untuk mengubah kata sandi',
            'current_password.current_password' => 'Kata sandi saat ini salah',
            'password.min' => 'Kata sandi baru minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
        ];

        if ($user->isTechnician()) {
            $rules['phone'] = [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($user) {
                    $digits = preg_replace('/\D/', '', $value);

                    // Cek apakah tidak ada digit sama sekali
                    if (empty($digits)) {
                        $fail('No. HP harus berupa nomor');

                        return;
                    }

                    if (! CustomerKey::isValidPhone($value)) {
                        $fail('No. HP harus 8–15 digit');

                        return;
                    }

                    $normalized = CustomerKey::normalizePhone($value);
                    $techId = $user->technician?->id;

                    if (Technician::where('phone', $normalized)->where('id', '!=', $techId)->exists()) {
                        $fail('No. HP sudah dipakai Teknisi lain');
                    }
                },
            ];
            $messages['phone.required'] = 'No. HP wajib diisi';
        } else {
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ];
            $messages['email.required'] = 'Email wajib diisi';
            $messages['email.email'] = 'Format email tidak valid';
            $messages['email.unique'] = 'Email sudah digunakan akun lain';
        }

        $validated = $request->validate($rules, $messages);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));
        $user->name = $name;

        if (! $user->isTechnician() && isset($validated['email'])) {
            $user->email = strtolower(trim($validated['email']));
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->isTechnician() && $user->technician) {
            $normalizedPhone = CustomerKey::normalizePhone($validated['phone']);
            $user->technician->update([
                'name' => $name,
                'phone' => $normalizedPhone,
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile diperbarui');
    }
}
