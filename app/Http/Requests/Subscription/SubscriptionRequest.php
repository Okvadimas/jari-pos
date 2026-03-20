<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'duration_months' => 'required|integer',
            'package_id' => 'required|integer',
            'voucher_code' => 'nullable|string',
            'affiliate_code' => 'nullable|string',
        ];
    }
}
