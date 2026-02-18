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
}
