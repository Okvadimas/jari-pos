<?php

namespace App\Repositories\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOpnameRepository
{
    /**
     * Get datatable query
     */
    public static function datatable($startDate, $endDate, $status = null)
    {
        $user = Auth::user();

        $query = DB::table('stock_opnames as so')
                    ->leftJoin('users as u', 'u.id', '=', 'so.approved_by')
                    ->whereNull('so.deleted_at')
                    ->whereBetween('so.opname_date', [$startDate, $endDate])
                    ->where('so.company_id', $user->company_id)
                    ->select(
                        'so.id',
                        'so.opname_number',
                        'so.opname_date',
                        'so.status',
                        'so.notes',
                        'u.name as approved_by_name',
                        'so.approved_at',
                        DB::raw('(SELECT COUNT(*) FROM stock_opname_details sod WHERE sod.stock_opname_id = so.id AND sod.deleted_at IS NULL) as total_items'),
                        DB::raw('(SELECT COALESCE(SUM(sod.difference), 0) FROM stock_opname_details sod WHERE sod.stock_opname_id = so.id AND sod.deleted_at IS NULL) as total_difference')
                    );

        if ($status && $status !== 'all') {
            $query->where('so.status', $status);
        }

        return $query;
    }

    /**
     * Get summary data
     */
    public static function getSummary($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('stock_opnames as so')
            ->leftJoin('stock_opname_details as sod', function ($join) {
                $join->on('sod.stock_opname_id', '=', 'so.id')
                     ->whereNull('sod.deleted_at');
            })
            ->whereNull('so.deleted_at')
            ->whereBetween('so.opname_date', [$startDate, $endDate])
            ->where('so.company_id', $user->company_id)
            ->selectRaw('
                COUNT(DISTINCT so.id) as total_opname,
                COALESCE(SUM(CASE WHEN sod.difference > 0 THEN sod.difference ELSE 0 END), 0) as total_selisih_plus,
                COALESCE(SUM(CASE WHEN sod.difference < 0 THEN ABS(sod.difference) ELSE 0 END), 0) as total_selisih_minus
            ')
            ->first();
    }

    /**
     * Get current system stock for a product variant
     * Takes the latest closing_stock from stock_daily_balances
     */
    public static function getSystemStock($productVariantId)
    {
        $result = DB::table('stock_daily_balances')
            ->where('product_variant_id', $productVariantId)
            ->whereNull('deleted_at')
            ->orderByDesc('date')
            ->select('closing_stock', 'date')
            ->first();

        return $result ? (int) $result->closing_stock : 0;
    }

    /**
     * Get latest balance before a given date for variants without existing record.
     */
    public static function getLatestBalancesBefore(array $variantIds, string $date): \Illuminate\Support\Collection
    {
        return DB::table('stock_daily_balances as sdb')
            ->joinSub(
                DB::table('stock_daily_balances')
                    ->whereIn('product_variant_id', $variantIds)
                    ->whereNull('deleted_at')
                    ->where('date', '<', $date)
                    ->groupBy('product_variant_id')
                    ->selectRaw('product_variant_id, MAX(date) as max_date'),
                'latest',
                fn($join) => $join
                    ->on('sdb.product_variant_id', '=', 'latest.product_variant_id')
                    ->on('sdb.date', '=', 'latest.max_date')
            )
            ->select('sdb.product_variant_id', 'sdb.closing_stock')
            ->get()
            ->keyBy('product_variant_id');
    }

    /**
     * Bulk adjust product_variants.current_stock by adding differences.
     */
    public static function bulkAdjustCurrentStock(array $corrections, string $now): void
    {
        foreach (array_chunk($corrections, 500, true) as $chunk) {
            $caseWhen = '';
            $ids = [];

            foreach ($chunk as $vid => $diff) {
                $ids[] = $vid;
                $caseWhen .= "WHEN {$vid} THEN current_stock + ({$diff}) ";
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

    /**
     * Bulk insert stock opname details.
     */
    public static function bulkInsertDetails(array $details): void
    {
        foreach (array_chunk($details, 500) as $chunk) {
            DB::table('stock_opname_details')->insert($chunk);
        }
    }

    /**
     * Delete all details for a stock opname.
     */
    public static function deleteDetailsByOpnameId(int $opnameId): void
    {
        DB::table('stock_opname_details')
            ->where('stock_opname_id', $opnameId)
            ->update(['deleted_at' => now()]);
    }
}
