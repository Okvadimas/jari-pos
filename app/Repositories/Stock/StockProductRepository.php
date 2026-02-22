<?php

namespace App\Repositories\Stock;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockProductRepository
{
    /**
     * Atomic decrement of current_stock for a variant.
     * Returns number of affected rows.
     */
    public static function decrementStock(int $variantId, int $qty): int
    {
        return DB::table('product_variants')
            ->where('id', $variantId)
            ->whereNull('deleted_at')
            ->decrement('current_stock', $qty);
    }

    /**
     * Atomic increment of current_stock for a variant.
     * Returns number of affected rows.
     */
    public static function incrementStock(int $variantId, int $qty): int
    {
        return DB::table('product_variants')
            ->where('id', $variantId)
            ->whereNull('deleted_at')
            ->increment('current_stock', $qty);
    }

    /**
     * Get sales order details for stock restoration.
     */
    public static function getSalesOrderDetails(int $salesOrderId): Collection
    {
        return DB::table('sales_order_details')
            ->where('sales_order_id', $salesOrderId)
            ->whereNull('deleted_at')
            ->select('product_variant_id', 'quantity')
            ->get();
    }

    /**
     * Get purchase details for stock restoration.
     */
    public static function getPurchaseDetails(int $purchaseId): Collection
    {
        return DB::table('purchase_details')
            ->where('purchase_id', $purchaseId)
            ->whereNull('deleted_at')
            ->select('product_variant_id', 'quantity')
            ->get();
    }
}
