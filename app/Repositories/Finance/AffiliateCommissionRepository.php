<?php

namespace App\Repositories\Finance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AffiliateCommissionRepository
{
    public static function datatable($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('affiliate_commissions as ac')
            ->leftJoin('app_sales as s', 's.id', '=', 'ac.app_sale_id')
            ->whereNull('ac.deleted_at')
            ->whereNull('s.deleted_at')
            ->where('ac.company_id', $user->company_id)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('s.sale_date', [$startDate, $endDate]);
            })
            ->select(
                'ac.id',
                'ac.commission_number',
                'ac.affiliate_name',
                'ac.affiliate_coupon_code',
                'ac.sale_amount',
                'ac.commission_rate',
                'ac.commission_amount',
                'ac.status',
                'ac.paid_date',
                's.sale_number',
                's.customer_name',
                's.sale_date',
                's.is_renewal'
            );
    }

    public static function getSummary($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('affiliate_commissions as ac')
            ->leftJoin('app_sales as s', 's.id', '=', 'ac.app_sale_id')
            ->whereNull('ac.deleted_at')
            ->whereNull('s.deleted_at')
            ->where('ac.company_id', $user->company_id)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('s.sale_date', [$startDate, $endDate]);
            })
            ->selectRaw('
                COUNT(*) as total_komisi,
                COALESCE(SUM(ac.commission_amount), 0) as total_nominal,
                COALESCE(SUM(CASE WHEN ac.status = "pending" THEN ac.commission_amount ELSE 0 END), 0) as total_pending,
                COALESCE(SUM(CASE WHEN ac.status = "paid" THEN ac.commission_amount ELSE 0 END), 0) as total_paid
            ')
            ->first();
    }
}
