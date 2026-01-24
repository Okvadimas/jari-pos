<?php

namespace App\Repositories\Inventory;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductRepository {

    public static function datatable() {
        $query = DB::table('products as p')
                    ->join('categories as c', 'c.id', '=', 'p.category_id')
                    ->join('product_variants as v', 'v.product_id', '=', 'p.id')
                    ->leftJoinSub(
                        DB::table('product_prices'),
                        'price', 
                        'price.product_variant_id', '=', 'v.id'
                    )
                    ->whereNull('p.deleted_at')
                    ->whereNull('c.deleted_at')
                    ->whereNull('v.deleted_at')
                    ->select('p.id', 'p.name as nama_produk', 'c.name as nama_kategori', 'v.name as nama_varian', 'price.purchase_price as harga_beli', 'price.sell_price as harga_jual')
                    ->orderBy('p.name', 'asc');
        
        return $query;
    }

}
