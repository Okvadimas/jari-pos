<?php

namespace App\Repositories\Inventory;

use App\Models\Unit;

class UnitRepository {

    public static function datatable() {
        $query = Unit::select('id', 'code', 'name', 'status', 'created_by', 'updated_by')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
