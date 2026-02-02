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
            'title' => 'Laporan Pembelian',
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
        $purchase = Purchase::with(['company', 'details.variant.product'])->findOrFail($id);
        
        // Format response data
        $details = $purchase->details->map(function($detail) {
            $productName = optional($detail->variant->product)->name ?? '-';
            $variantName = optional($detail->variant)->name ?? '';
            
            return [
                'product_name' => trim($productName . ' ' . $variantName),
                'sku' => optional($detail->variant)->sku ?? '-',
                'quantity' => $detail->quantity,
                'cost' => $detail->cost_price_per_item,
                'total' => $detail->quantity * $detail->cost_price_per_item
            ];
        });

        return response()->json([
            'purchase' => $purchase,
            'company_name' => $purchase->company ? $purchase->company->name : $purchase->supplier_name,
            'purchase_date_formatted' => Carbon::parse($purchase->purchase_date)->format('d M Y H:i'),
            'details' => $details
        ]);
    }
}
