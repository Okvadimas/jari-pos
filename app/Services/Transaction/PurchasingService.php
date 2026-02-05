<?php

namespace App\Services\Transaction;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Transaction\PurchasingRepository;

class PurchasingService
{
    public static function datatable($startDate, $endDate)
    {
        $data = PurchasingRepository::datatable($startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('purchase_date', function ($row) {
                return Carbon::parse($row->purchase_date)->format('d M Y');
            })
            ->addColumn('supplier_display', function ($row) {
                return $row->supplier_name ?: ($row->company_name ?: '-');
            })
            ->editColumn('total_cost', function ($row) {
                return 'Rp ' . number_format($row->total_cost, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-dim btn-sm btn-outline-info" onclick="detail(' . $row->id . ')"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return PurchasingRepository::getSummary($startDate, $endDate);
    }
}
