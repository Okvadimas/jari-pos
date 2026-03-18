<?php

namespace App\Repositories\Finance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BusinessExpenseRepository
{
    public static function datatable($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('business_expenses as be')
            ->whereNull('be.deleted_at')
            ->whereBetween('be.expense_date', [$startDate, $endDate])
            ->where('be.company_id', $user->company_id)
            ->select(
                'be.id',
                'be.expense_number',
                'be.category',
                'be.description',
                'be.amount',
                'be.expense_date',
                'be.vendor_name',
                'be.reference_note'
            );
    }

    public static function getSummary($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('business_expenses')
            ->whereNull('deleted_at')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('company_id', $user->company_id)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(amount), 0) as total_pengeluaran,
                COALESCE(SUM(CASE WHEN category = "server" THEN amount ELSE 0 END), 0) as total_server,
                COALESCE(SUM(CASE WHEN category = "production" THEN amount ELSE 0 END), 0) as total_production
            ')
            ->first();
    }
}
