<?php

namespace App\Repositories\Management;

use App\Models\Payment;

class PaymentRepository {

    public static function datatable() {
        $query = Payment::select('id', 'name', 'type', 'status')->orderBy('id', 'desc');
        
        return $query;
    }

}
