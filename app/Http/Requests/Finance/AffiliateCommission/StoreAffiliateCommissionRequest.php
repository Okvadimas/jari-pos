<?php

namespace App\Http\Requests\Finance\AffiliateCommission;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffiliateCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:affiliate_commissions,id',
            'action' => 'required|string|in:pay,cancel',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID komisi wajib diisi',
            'action.required' => 'Aksi wajib dipilih',
            'action.in' => 'Aksi tidak valid',
        ];
    }
}
