<?php

namespace App\Http\Requests;

use App\Helpers\CustomerKey;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $techId = $this->route('id') ?? $this->route('technician');
        if (is_object($techId)) {
            $techId = $techId->id;
        }

        return [
            'name' => [
                'required',
                'string',
                'max:60',
                function ($attribute, $value, $fail) use ($techId) {
                    $clean = trim($value);
                    $exists = Technician::whereRaw('LOWER(name) = ?', [mb_strtolower($clean, 'UTF-8')])
                        ->where('id', '!=', $techId)
                        ->exists();

                    if ($exists) {
                        $fail('Nama Teknisi sudah ada');
                    }
                },
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($techId) {
                    $digits = preg_replace('/\D/', '', $value);

                    if (empty($digits)) {
                        $fail('No. HP harus berupa nomor');

                        return;
                    }

                    if (! CustomerKey::isValidPhone($value)) {
                        $fail('No. HP harus 8–15 digit');

                        return;
                    }
                    $normalized = CustomerKey::normalizePhone($value);
                    $exists = Technician::where('phone', $normalized)
                        ->where('id', '!=', $techId)
                        ->exists();

                    if ($exists) {
                        $fail('Nomor sudah digunakan');
                    }
                },
            ],
            'is_active' => ['required', 'boolean'],
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                $tech = Technician::find($this->route('id') ?? $this->route('technician'));
                if ($tech?->user && $tech->user->email !== $value) {
                    if (User::where('email', $value)->exists()) {
                        $fail('Email sudah digunakan');
                    }
                }
            }],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Teknisi wajib diisi',
            'phone.required' => 'No. HP wajib diisi',
            'is_active.required' => 'Status wajib dipilih',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.min' => 'Kata sandi minimal 8 karakter',
        ];
    }
}
