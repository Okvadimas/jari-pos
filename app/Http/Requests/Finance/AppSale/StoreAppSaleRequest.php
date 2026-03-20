<?php

namespace App\Http\Requests\Finance\AppSale;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer|exists:app_sales,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'plan_name' => 'required|string|max:100',
            'duration_months' => 'required|integer|min:1',
            'is_renewal' => 'nullable|boolean',
            'original_amount' => 'required|numeric|min:0',
            'affiliate_coupon_code' => 'nullable|string|max:50',
            'voucher_code' => 'nullable|string|max:50',
            'sale_date' => 'required|string',
            'reference_note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pelanggan wajib diisi',
            'plan_name.required' => 'Nama paket wajib diisi',
            'duration_months.required' => 'Durasi langganan wajib diisi',
            'duration_months.min' => 'Durasi minimal 1 bulan',
            'original_amount.required' => 'Harga wajib diisi',
            'original_amount.min' => 'Harga tidak boleh negatif',
            'sale_date.required' => 'Tanggal penjualan wajib diisi',
        ];
    }
}
