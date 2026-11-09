<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan' => ['required', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan.required' => 'Alasan pembatalan wajib diisi',
            'alasan.max' => 'Alasan pembatalan maksimal 200 karakter',
        ];
    }
}
