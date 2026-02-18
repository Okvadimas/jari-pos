<?php

namespace App\Repositories\Management;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentRepository {

    public static function datatable() {
        $query = PaymentMethod::select('id', 'company_id', 'name', 'type')
                    ->where('company_id', Auth::user()->company_id)
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
