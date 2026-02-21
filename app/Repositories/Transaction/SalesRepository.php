<?php

namespace App\Repositories\Transaction;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
    
class SalesRepository
{
    public static function datatable($startDate, $endDate)
    {
        $user = Auth::user();

        $query = DB::table('sales_orders as so')
                    ->leftJoin('companies as c', 'c.id', '=', 'so.company_id')
                    ->whereNull('so.deleted_at')
                    ->whereBetween('so.order_date', [$startDate, $endDate])
                    ->where('so.company_id', $user->company_id)
                    ->select(
                        'so.id',
                        'so.invoice_number',
                        'so.order_date',
                        'so.customer_name',
                        'so.total_amount',
                        'so.discount_amount',
                        'so.final_amount',
                        'so.applied_promo_id',
                        'c.name as company_name'
                    );

        return $query;
    }

    public static function getSummary($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('sales_orders')
            ->whereNull('deleted_at')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('company_id', $user->company_id)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total_amount), 0) as total_penjualan,
                COALESCE(SUM(discount_amount), 0) as total_diskon,
                COALESCE(SUM(final_amount), 0) as total_pendapatan
            ')
            ->first();
    }

    /**
     * Get total qty sold and total revenue per product variant within a date range.
     * Used by MovingStatusService for moving status calculation.
     */
    public static function getSalesPerVariant(int $companyId, string $startDate, string $endDate)
    {
        return DB::table('sales_order_details as sod')
            ->join('sales_orders as so', 'so.id', '=', 'sod.sales_order_id')
            ->where('so.company_id', $companyId)
            ->whereBetween('so.order_date', [$startDate, $endDate])
            ->whereNull('so.deleted_at')
            ->whereNull('sod.deleted_at')
            ->groupBy('sod.product_variant_id')
            ->select(
                'sod.product_variant_id',
                DB::raw('SUM(sod.quantity) as total_qty_sold'),
                DB::raw('SUM(sod.subtotal) as total_revenue'),
                DB::raw('SUM(sod.purchase_price * sod.quantity) as total_cogs')
            )
            ->get()
            ->keyBy('product_variant_id');
    }
}
