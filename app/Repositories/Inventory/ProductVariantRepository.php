<?php

namespace App\Repositories\Inventory;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductVariantRepository {

    public static function datatable() {
        $query = DB::table('products as p')
                    ->join('categories as c', 'c.id', '=', 'p.category_id')
                    ->join('product_variants as pv', 'pv.product_id', '=', 'p.id')
                    ->leftJoin('units as u', 'u.id', '=', 'pv.unit_id')
                    ->leftJoinSub(DB::table('product_prices')->where('is_active', 1), 'price', 'price.product_variant_id', '=', 'pv.id')
                    ->whereNull('p.deleted_at')
                    ->whereNull('c.deleted_at')
                    ->whereNull('pv.deleted_at')
                    ->whereNull('price.deleted_at')
                    ->select('pv.id', 'p.name as nama_produk', 'c.name as nama_kategori', 'pv.name as nama_varian', 'pv.sku as sku', DB::raw('COALESCE(u.name, \'-\') as nama_satuan'), DB::raw('COALESCE(price.purchase_price, 0) as harga_beli'), DB::raw('COALESCE(price.sell_price, 0) as harga_jual'))
                    ->orderBy('p.name', 'asc');
        
        return $query;
    }

}
