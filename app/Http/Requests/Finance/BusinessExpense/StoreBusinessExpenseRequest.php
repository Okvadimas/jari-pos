<?php

namespace App\Http\Requests\Finance\BusinessExpense;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer|exists:business_expenses,id',
            'category' => 'required|string|in:server,production,other',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|string',
            'vendor_name' => 'nullable|string|max:255',
            'reference_note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Kategori wajib dipilih',
            'category.in' => 'Kategori tidak valid',
            'description.required' => 'Deskripsi wajib diisi',
            'amount.required' => 'Nominal wajib diisi',
            'amount.min' => 'Nominal tidak boleh negatif',
            'expense_date.required' => 'Tanggal wajib diisi',
        ];
    }
}
