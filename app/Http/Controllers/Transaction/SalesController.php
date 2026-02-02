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
        $startDate  = Carbon::now()->startOfMonth()->format('d/m/Y');
        $endDate    = Carbon::now()->endOfMonth()->format('d/m/Y');

        $data = [
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'title'     => 'Laporan Penjualan',
            'js'        => 'resources/js/pages/transaction/sales/index.js',
        ];

        return view('transaction.sales.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate  = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate    = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $data   = SalesOrder::whereBetween('order_date', [$startDate, $endDate])
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
                    ->addColumn('action', function ($row) {
                        return '<button class="btn btn-sm btn-icon btn-trigger btn-detail" data-id="' . $row->id . '" title="View Details">
                                    <em class="icon ni ni-eye"></em>
                                </button>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with(['company', 'details.variant.product'])->findOrFail($id);
        
        // Format response data
        $details = $salesOrder->details->map(function($detail) {
            $productName = optional($detail->variant->product)->name ?? '-';
            $variantName = optional($detail->variant)->name ?? '';

            return [
                'product_name' => trim($productName . ' ' . $variantName),
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'discount' => $detail->discount_auto_amount,
                'subtotal' => $detail->subtotal
            ];
        });

        return response()->json([
            'sales_order' => $salesOrder,
            'customer_name' => optional($salesOrder->company)->name ?? 'Guest',
            'order_date_formatted' => Carbon::parse($salesOrder->order_date)->format('d M Y H:i'),
            'details' => $details
        ]);
    }
}
