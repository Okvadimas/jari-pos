<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Model
use App\Models\Company;

class CompanyRepository {

    public static function datatable() {
        $query = Company::select('id', 'name', 'email', 'phone', 'address')
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
