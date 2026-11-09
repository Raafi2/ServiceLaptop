<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Models\Technician;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nomor = $this->route('nomor') ?? $this->route('service');
        $service = is_object($nomor) ? $nomor : Service::where('tracking_no', (string) $nomor)->first();

        return [
            'laptop' => ['required', 'string', 'max:100'],
            'keluhan' => ['required', 'string', 'max:500'],
            'teknisi_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($service) {
                    $tech = Technician::find($value);
                    if (! $tech) {
                        $fail('Teknisi tidak ditemukan');

                        return;
                    }

                    // Pilihan hanya Teknisi Aktif, ditambah Teknisi yang sedang tercatat di servis itu
                    if (! $tech->is_active && $service && $service->technician_id !== $tech->id) {
                        $fail('Pilih Teknisi penanggung jawab yang aktif');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'laptop.required' => 'Laptop wajib diisi',
            'keluhan.required' => 'Keluhan wajib diisi',
            'teknisi_id.required' => 'Pilih Teknisi penanggung jawab',
        ];
    }
}
