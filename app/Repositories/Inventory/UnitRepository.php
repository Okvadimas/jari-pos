<?php

namespace App\Repositories\Inventory;

use App\Models\Unit;

class UnitRepository {

    public static function datatable() {
        $query = Unit::select('id', 'code', 'name', 'created_by', 'updated_by')
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
