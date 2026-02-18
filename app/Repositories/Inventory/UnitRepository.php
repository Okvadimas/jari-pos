<?php

namespace App\Repositories\Inventory;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class UnitRepository {

    public static function datatable() {
        $query = Unit::select('id', 'company_id', 'code', 'name', 'created_by', 'updated_by')
                    ->where('company_id', Auth::user()->company_id)
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
