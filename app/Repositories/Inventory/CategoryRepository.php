<?php

namespace App\Repositories\Inventory;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryRepository {

    public static function datatable() {
        $query = Category::select('id', 'code', 'company_id', 'name', 'created_by', 'updated_by')
                    ->where('company_id', Auth::user()->company_id)
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc');
        
        return $query;
    }

}
