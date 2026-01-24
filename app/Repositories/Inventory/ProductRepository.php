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
                        DB::table('product_prices')->where('status', 1),
                        'price', 
                        'price.product_variant_id', '=', 'v.id'
                    )
                    ->select('p.id', 'p.name as nama_produk', 'c.name as nama_kategori', 'v.name as nama_varian', 'price.purchase_price as harga_beli', 'price.sell_price as harga_jual', 'p.status')
                    ->orderBy('p.name', 'asc');
        
        return $query;
    }

}
