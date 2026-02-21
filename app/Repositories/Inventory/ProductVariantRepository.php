<?php

namespace App\Repositories\Inventory;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductVariantRepository {

    public static function datatable() {
        $query = DB::table('products as p')
                    ->join('categories as c', 'c.id', '=', 'p.category_id')
                    ->join('product_variants as pv', 'pv.product_id', '=', 'p.id')
                    ->leftJoinSub(DB::table('product_prices')->where('is_active', 1), 'price', 'price.product_variant_id', '=', 'pv.id')
                    ->whereNull('p.deleted_at')
                    ->whereNull('c.deleted_at')
                    ->whereNull('pv.deleted_at')
                    ->whereNull('price.deleted_at')
                    ->select('pv.id', 'p.name as nama_produk', 'c.name as nama_kategori', 'pv.name as nama_varian', 'pv.sku as sku', DB::raw('COALESCE(price.purchase_price, 0) as harga_beli'), DB::raw('COALESCE(price.sell_price, 0) as harga_jual'))
                    ->orderBy('p.name', 'asc');
        
        return $query;
    }

    /**
     * Get all active (non-deleted) product variants for a company.
     * Returns variant ID, product name, variant name, SKU.
     */
    public static function getAllActive(int $companyId)
    {
        return DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('p.company_id', $companyId)
            ->whereNull('pv.deleted_at')
            ->whereNull('p.deleted_at')
            ->select(
                'pv.id',
                'pv.product_id',
                'p.name as product_name',
                'pv.name as variant_name',
                'pv.sku'
            )
            ->orderBy('p.name', 'asc')
            ->get();
    }

    /**
     * Get the latest closing_stock per product variant from stock_daily_balances.
     * Returns a keyed collection: product_variant_id => closing_stock
     */
    public static function getLatestStock(int $companyId)
    {
        // Subquery to get max date per variant
        $latestDates = DB::table('stock_daily_balances as sdb')
            ->join('product_variants as pv', 'pv.id', '=', 'sdb.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('p.company_id', $companyId)
            ->whereNull('sdb.deleted_at')
            ->groupBy('sdb.product_variant_id')
            ->select(
                'sdb.product_variant_id',
                DB::raw('MAX(sdb.date) as max_date')
            );

        return DB::table('stock_daily_balances as sdb2')
            ->joinSub($latestDates, 'latest', function ($join) {
                $join->on('sdb2.product_variant_id', '=', 'latest.product_variant_id')
                     ->on('sdb2.date', '=', 'latest.max_date');
            })
            ->select('sdb2.product_variant_id', 'sdb2.closing_stock')
            ->get()
            ->keyBy('product_variant_id');
    }

}
