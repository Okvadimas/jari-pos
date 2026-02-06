<?php

namespace App\Repositories\Transaction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchasingRepository
{
    public static function datatable($startDate, $endDate)
    {
        $user = Auth::user();

        $query = DB::table('purchases as p')
                    ->leftJoin('companies as c', 'c.id', '=', 'p.company_id')
                    ->whereNull('p.deleted_at')
                    ->whereBetween('p.purchase_date', [$startDate, $endDate])
                    ->where('p.company_id', $user->company_id)
                    ->select(
                        'p.id',
                        'p.order_number',
                        'p.purchase_date',
                        'p.supplier_name',
                        'p.total_cost',
                        'p.reference_note',
                        'c.name as company_name'
                    );

        return $query;
    }

    public static function getSummary($startDate, $endDate)
    {
        $user = Auth::user();

        return DB::table('purchases')
            ->whereNull('deleted_at')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('company_id', $user->company_id)
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total_cost), 0) as total_pembelian
            ')
            ->first();
    }

    /**
     * Get product variants for dropdown
     */
    public static function getProductVariants()
    {
        $user = Auth::user();

        return DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->whereNull('pv.deleted_at')
            ->whereNull('p.deleted_at')
            ->where('p.company_id', $user->company_id)
            ->select(
                'pv.id',
                'pv.name as variant_name',
                'pv.sku',
                'p.name as product_name'
            )
            ->orderBy('p.name')
            ->orderBy('pv.name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : '') . ' (' . $item->sku . ')',
                    'sku' => $item->sku,
                ];
            });
    }
}
