<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

use App\Models\SalesOrder;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'js' => 'resources/js/pages/transaction/sales/index.js',
        ];

        return view('transaction.sales.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = SalesOrder::with(['company'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('order_date', function ($row) {
                return Carbon::parse($row->order_date)->format('d M Y');
            })
            ->addColumn('customer_name', function ($row) {
                return optional($row->company)->name ?? 'Guest';
            })
            ->editColumn('total_amount', function ($row) {
                return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
            })
            ->editColumn('final_amount', function ($row) {
                return 'Rp ' . number_format($row->final_amount, 0, ',', '.');
            })
            ->rawColumns(['action']) // In case we add actions later
            ->make(true);
    }
}
