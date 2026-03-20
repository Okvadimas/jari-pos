<?php

namespace App\Repositories\Affiliator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AffiliateDashboardRepository
{
    /**
     * Get per-affiliate summary (grouped by affiliate_coupon_code)
     */
    public static function affiliateSummary($startDate = null, $endDate = null)
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
            ->groupBy('ac.affiliate_coupon_code', 'ac.affiliate_name')
            ->select(
                'ac.affiliate_coupon_code',
                'ac.affiliate_name',
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('COALESCE(SUM(ac.commission_amount), 0) as total_komisi'),
                DB::raw('COALESCE(SUM(CASE WHEN ac.status = "pending" THEN ac.commission_amount ELSE 0 END), 0) as komisi_pending'),
                DB::raw('COALESCE(SUM(CASE WHEN ac.status = "paid" THEN ac.commission_amount ELSE 0 END), 0) as komisi_paid'),
                DB::raw('COALESCE(SUM(CASE WHEN s.is_renewal = 0 THEN 1 ELSE 0 END), 0) as total_baru'),
                DB::raw('COALESCE(SUM(CASE WHEN s.is_renewal = 1 THEN 1 ELSE 0 END), 0) as total_perpanjangan'),
                DB::raw('COALESCE(SUM(ac.sale_amount), 0) as total_penjualan')
            );
    }

    /**
     * Get overall dashboard stats
     */
    public static function dashboardStats($startDate = null, $endDate = null)
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
                COUNT(DISTINCT ac.affiliate_coupon_code) as total_affiliate,
                COUNT(*) as total_transaksi,
                COALESCE(SUM(ac.commission_amount), 0) as total_komisi,
                COALESCE(SUM(CASE WHEN ac.status = "pending" THEN ac.commission_amount ELSE 0 END), 0) as komisi_pending,
                COALESCE(SUM(CASE WHEN ac.status = "paid" THEN ac.commission_amount ELSE 0 END), 0) as komisi_paid,
                COALESCE(SUM(ac.sale_amount), 0) as total_penjualan
            ')
            ->first();
    }

    /**
     * Get detail commissions for a specific affiliate
     */
    public static function affiliateDetail($affiliateCode, $startDate = null, $endDate = null)
    {
        $user = Auth::user();

        return DB::table('affiliate_commissions as ac')
            ->leftJoin('app_sales as s', 's.id', '=', 'ac.app_sale_id')
            ->whereNull('ac.deleted_at')
            ->whereNull('s.deleted_at')
            ->where('ac.company_id', $user->company_id)
            ->where('ac.affiliate_coupon_code', $affiliateCode)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('s.sale_date', [$startDate, $endDate]);
            })
            ->select(
                'ac.commission_number',
                'ac.sale_amount',
                'ac.commission_rate',
                'ac.commission_amount',
                'ac.status',
                'ac.paid_date',
                's.sale_number',
                's.customer_name',
                's.plan_name',
                's.sale_date',
                's.is_renewal'
            )
            ->orderByDesc('s.sale_date');
    }
}
