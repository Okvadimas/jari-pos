<?php

namespace App\Services\Transaction;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Transaction\SalesRepository;

class SalesService
{
    public static function datatable($startDate, $endDate)
    {
        $data = SalesRepository::datatable($startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('order_date', function ($row) {
                return Carbon::parse($row->order_date)->format('d M Y');
            })
            ->addColumn('customer_display', function ($row) {
                return $row->customer_name ?: ($row->company_name ?: 'Guest');
            })
            ->editColumn('total_amount', function ($row) {
                return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
            })
            ->editColumn('final_amount', function ($row) {
                return 'Rp ' . number_format($row->final_amount, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-icon btn-trigger btn-detail" data-id="' . $row->id . '" title="Lihat Detail">
                            <em class="icon ni ni-eye"></em>
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return SalesRepository::getSummary($startDate, $endDate);
    }
}
