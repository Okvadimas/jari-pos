<?php

namespace App\Repositories\Finance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppSaleRepository
{
    public static function datatable($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('app_sales as s')
            ->leftJoin('users as u', 'u.id', '=', 's.confirmed_by')
            ->whereNull('s.deleted_at')
            ->whereBetween('s.sale_date', [$startDate, $endDate])
            ->where('s.company_id', $user->company_id)
            ->select(
                's.id',
                's.sale_number',
                's.customer_name',
                's.customer_email',
                's.plan_name',
                's.duration_months',
                's.is_renewal',
                's.original_amount',
                's.discount_amount',
                's.affiliate_discount_amount',
                's.final_amount',
                's.affiliate_coupon_code',
                's.voucher_code',
                's.status',
                's.sale_date',
                's.reference_note',
                'u.name as confirmed_by_name'
            );
    }

    public static function getSummary($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('app_sales')
            ->whereNull('deleted_at')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('company_id', $user->company_id)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(CASE WHEN status = "confirmed" THEN final_amount ELSE 0 END), 0) as total_pemasukan,
                COALESCE(SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END), 0) as total_pending,
                COALESCE(SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END), 0) as total_confirmed
            ')
            ->first();
    }
}
