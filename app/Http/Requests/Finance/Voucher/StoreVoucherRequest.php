<?php

namespace App\Http\Requests\Finance\Voucher;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->input('id');

        return [
            'id' => 'nullable|integer|exists:vouchers,id',
            'code' => 'required|string|max:50|unique:vouchers,code,' . $couponId,
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|string',
            'valid_until' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode kupon wajib diisi',
            'code.unique' => 'Kode kupon sudah digunakan',
            'name.required' => 'Nama kupon wajib diisi',
            'type.required' => 'Tipe diskon wajib dipilih',
            'type.in' => 'Tipe diskon tidak valid',
            'value.required' => 'Nilai diskon wajib diisi',
            'value.min' => 'Nilai diskon tidak boleh negatif',
        ];
    }
}
