<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Purchase;

class PurchasingController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'js' => 'resources/js/pages/transaction/purchasing/index.js',
        ];

        return view('transaction.purchasing.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = Purchase::with(['company'])
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('purchase_date', function ($row) {
                return Carbon::parse($row->purchase_date)->format('d M Y');
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier_name ?? optional($row->company)->name ?? '-';
            })
            ->editColumn('total_cost', function ($row) {
                return 'Rp ' . number_format($row->total_cost, 0, ',', '.');
            })
            ->rawColumns(['action']) // In case we add actions later
            ->make(true);
    }
}
