<?php

namespace App\Repositories\Transaction;

use Illuminate\Support\Facades\DB;

class SalesRepository
{
    public static function datatable($startDate, $endDate)
    {
        $query = DB::table('sales_orders as so')
                    ->leftJoin('companies as c', 'c.id', '=', 'so.company_id')
                    ->whereNull('so.deleted_at')
                    ->whereBetween('so.order_date', [$startDate, $endDate])
                    ->select(
                        'so.id',
                        'so.order_date',
                        'so.customer_name',
                        'so.total_amount',
                        'so.total_discount_manual',
                        'so.final_amount',
                        'so.applied_promo_id',
                        'c.name as company_name'
                    )
                    ->orderBy('so.order_date', 'desc')
                    ->orderBy('so.id', 'desc');

        return $query;
    }

    public static function getSummary($startDate, $endDate)
    {
        return DB::table('sales_orders')
            ->whereNull('deleted_at')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total_amount), 0) as total_penjualan,
                COALESCE(SUM(total_discount_manual), 0) as total_diskon,
                COALESCE(SUM(final_amount), 0) as total_pendapatan
            ')
            ->first();
    }
}
