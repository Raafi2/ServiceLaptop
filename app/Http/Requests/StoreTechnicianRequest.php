<?php

namespace App\Http\Requests;

use App\Helpers\CustomerKey;
use App\Models\Technician;
use Illuminate\Foundation\Http\FormRequest;

class StoreTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:60',
                function ($attribute, $value, $fail) {
                    $clean = trim($value);
                    if (Technician::whereRaw('LOWER(name) = ?', [mb_strtolower($clean, 'UTF-8')])->exists()) {
                        $fail('Nama Teknisi sudah ada');
                    }
                },
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
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
                    if (Technician::where('phone', $normalized)->exists()) {
                        $fail('Nomor sudah digunakan');
                    }
                },
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Teknisi wajib diisi',
            'phone.required' => 'No. HP wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
        ];
    }
}
