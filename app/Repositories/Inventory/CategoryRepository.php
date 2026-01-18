<?php

namespace App\Repositories\Inventory;

use App\Models\Category;

class CategoryRepository {

    public static function datatable() {
        $query = Category::select('id', 'name', 'status', 'created_by', 'updated_by')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
