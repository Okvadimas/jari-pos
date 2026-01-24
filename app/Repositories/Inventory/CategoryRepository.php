<?php

namespace App\Repositories\Inventory;

use App\Models\Category;

class CategoryRepository {

    public static function datatable() {
        $query = Category::select('id', 'name', 'created_by', 'updated_by')
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
