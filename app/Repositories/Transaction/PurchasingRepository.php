<?php

namespace App\Repositories\Transaction;

use Illuminate\Support\Facades\DB;

class PurchasingRepository
{
    public static function datatable($startDate, $endDate)
    {
        $query = DB::table('purchases as p')
                    ->leftJoin('companies as c', 'c.id', '=', 'p.company_id')
                    ->whereNull('p.deleted_at')
                    ->whereBetween('p.purchase_date', [$startDate, $endDate])
                    ->select(
                        'p.id',
                        'p.purchase_date',
                        'p.supplier_name',
                        'p.total_cost',
                        'p.reference_note',
                        'c.name as company_name'
                    )
                    ->orderBy('p.purchase_date', 'desc')
                    ->orderBy('p.id', 'desc');

        return $query;
    }

    public static function getSummary($startDate, $endDate)
    {
        return DB::table('purchases')
            ->whereNull('deleted_at')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total_cost), 0) as total_pembelian
            ')
            ->first();
    }
}
