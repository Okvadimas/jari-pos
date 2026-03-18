<?php

namespace App\Repositories\Finance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DiscountCouponRepository
{
    public static function datatable()
    {
        $user = Auth::user();

        return DB::table('discount_coupons')
            ->whereNull('deleted_at')
            ->where('company_id', $user->company_id)
            ->select(
                'id',
                'code',
                'name',
                'type',
                'value',
                'max_uses',
                'used_count',
                'valid_from',
                'valid_until',
                'is_active'
            );
    }

    public static function getSummary()
    {
        $user = Auth::user();

        return DB::table('discount_coupons')
            ->whereNull('deleted_at')
            ->where('company_id', $user->company_id)
            ->selectRaw('
                COUNT(*) as total_kupon,
                COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) as total_aktif,
                COALESCE(SUM(used_count), 0) as total_digunakan
            ')
            ->first();
    }
}
