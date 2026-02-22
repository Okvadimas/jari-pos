<?php

namespace App\Repositories\Stock;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockDailyBalanceRepository
{
    /**
     * Get all active variant IDs + current_stock for a company.
     */
    public static function getActiveVariantsWithStock(int $companyId): Collection
    {
        return DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('p.company_id', $companyId)
            ->whereNull('pv.deleted_at')
            ->whereNull('p.deleted_at')
            ->select('pv.id', 'pv.current_stock')
            ->get();
    }

    /**
     * Get balances for given variant IDs on a specific date, keyed by product_variant_id.
     * Shared by StockDailyBalanceService and StockOpnameService.
     */
    public static function getBalancesByDate(array $variantIds, string $date, bool $lock = false): Collection
    {
        $query = DB::table('stock_daily_balances')
            ->whereIn('product_variant_id', $variantIds)
            ->where('date', $date)
            ->whereNull('deleted_at');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get()->keyBy('product_variant_id');
    }

    /**
     * Get total sales qty per variant for a specific date.
     */
    public static function getDailySalesMap(array $variantIds, string $date): Collection
    {
        return DB::table('sales_order_details as sod')
            ->join('sales_orders as so', 'so.id', '=', 'sod.sales_order_id')
            ->whereIn('sod.product_variant_id', $variantIds)
            ->whereDate('so.order_date', $date)
            ->whereNull('so.deleted_at')
            ->whereNull('sod.deleted_at')
            ->groupBy('sod.product_variant_id')
            ->selectRaw('sod.product_variant_id, COALESCE(SUM(sod.quantity), 0) as total_qty')
            ->pluck('total_qty', 'product_variant_id');
    }

    /**
     * Get total purchase qty per variant for a specific date.
     */
    public static function getDailyPurchasesMap(array $variantIds, string $date): Collection
    {
        return DB::table('purchase_details as pd')
            ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
            ->whereIn('pd.product_variant_id', $variantIds)
            ->whereDate('p.purchase_date', $date)
            ->whereNull('p.deleted_at')
            ->whereNull('pd.deleted_at')
            ->groupBy('pd.product_variant_id')
            ->selectRaw('pd.product_variant_id, COALESCE(SUM(pd.quantity), 0) as total_qty')
            ->pluck('total_qty', 'product_variant_id');
    }

    /**
     * Upsert stock_daily_balances rows in chunks.
     * Shared by StockDailyBalanceService and StockOpnameService.
     */
    public static function upsertBalances(array $rows, array $updateColumns): void
    {
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('stock_daily_balances')->upsert(
                $chunk,
                ['product_variant_id', 'date'],
                $updateColumns
            );
        }
    }

    /**
     * Bulk set product_variants.current_stock using CASE WHEN.
     * Used for drift correction (absolute values).
     */
    public static function bulkSetCurrentStock(array $corrections, string $now): void
    {
        foreach (array_chunk($corrections, 500, true) as $chunk) {
            $caseWhen = '';
            $ids = [];

            foreach ($chunk as $variantId => $trueClosing) {
                $ids[] = $variantId;
                $caseWhen .= "WHEN {$variantId} THEN {$trueClosing} ";
            }

            $idList = implode(',', $ids);

            DB::statement("
                UPDATE product_variants
                SET current_stock = CASE id {$caseWhen} END,
                    updated_at = '{$now}'
                WHERE id IN ({$idList})
            ");
        }
    }
}
