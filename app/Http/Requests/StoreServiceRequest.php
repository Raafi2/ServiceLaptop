<?php

namespace App\Http\Requests;

use App\Helpers\CustomerKey;
use App\Models\Technician;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('biaya')) {
            $rawBiaya = preg_replace('/\D/', '', (string) $this->input('biaya'));
            $this->merge([
                'biaya' => $rawBiaya !== '' ? (int) $rawBiaya : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'hp' => [
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
                    }
                },
            ],
            'laptop' => ['required', 'string', 'max:100'],
            'keluhan' => ['required', 'string', 'max:500'],
            'biaya' => ['required', 'integer', 'min:1'],
            'teknisi_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $tech = Technician::find($value);
                    if (! $tech || ! $tech->is_active) {
                        $fail('Pilih Teknisi penanggung jawab yang aktif');
                    }
                },
            ],
            'foto' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:10240'],
            'foto2' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'hp.required' => 'No HP wajib diisi',
            'laptop.required' => 'Laptop wajib diisi',
            'keluhan.required' => 'Keluhan wajib diisi',
            'biaya.required' => 'Biaya harus lebih dari 0',
            'biaya.min' => 'Biaya harus lebih dari 0',
            'teknisi_id.required' => 'Pilih Teknisi penanggung jawab',
            'foto.required' => 'Foto kondisi wajib diunggah',
            'foto.mimes' => 'Format foto harus JPG atau PNG',
            'foto.max' => 'Ukuran foto maksimal 10 MB',
            'foto2.mimes' => 'Format foto kedua harus JPG atau PNG',
            'foto2.max' => 'Ukuran foto kedua maksimal 10 MB',
        ];
    }
}
