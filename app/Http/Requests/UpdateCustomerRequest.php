<?php

namespace App\Http\Requests;

use App\Helpers\CustomerKey;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('nama') && ! $this->has('name')) {
            $this->merge(['name' => $this->input('nama')]);
        }
        if ($this->has('hp') && ! $this->has('phone')) {
            $this->merge(['phone' => $this->input('hp')]);
        }
    }

    public function rules(): array
    {
        $customer = $this->route('id') ?? $this->route('customer');
        $custId = is_object($customer) ? $customer->id : $customer;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'nama' => [
                'nullable',
                'string',
                'max:100',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($custId) {
                    if (! CustomerKey::isValidPhone($value)) {
                        $fail('No. HP harus 8–15 digit');

                        return;
                    }

                    $rawName = (string) ($this->input('name') ?: $this->input('nama'));
                    $nameKey = CustomerKey::normalizeName($rawName);
                    $phone = CustomerKey::normalizePhone($value);

                    $exists = Customer::where('name_key', $nameKey)
                        ->where('phone', $phone)
                        ->where('id', '!=', $custId)
                        ->exists();

                    if ($exists) {
                        $fail('Pelanggan dengan nama dan no. HP ini sudah ada');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'phone.required' => 'No. HP wajib diisi',
        ];
    }
}
