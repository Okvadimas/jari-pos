<?php

namespace App\Repositories\Inventory;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductRepository {

    public static function datatable() {
        $query = DB::table('products as p')
                    ->join('categories as c', 'c.id', '=', 'p.category_id')
                    ->whereNull('p.deleted_at')
                    ->whereNull('c.deleted_at')
                    ->select('p.id', 'p.name as nama_produk', 'c.name as nama_kategori')
                    ->orderBy('p.name', 'asc');
        
        return $query;
    }

    /**
     * Generate SKU via stored procedure.
     */
    public static function generateSku(string $categoryCode, string $companyId): string
    {
        $result = DB::selectOne('CALL generate_sku(?, ?)', [
            $categoryCode,
            $companyId,
        ]);

        return $result->sku;
    }

}
