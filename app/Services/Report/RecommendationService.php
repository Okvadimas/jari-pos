<?php

namespace App\Services\Transaction;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Transaction\SalesRepository;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Services\Utilities\TransactionNumberService;

class RecommendationService
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
                return '<button class="btn btn-dim btn-sm btn-outline-info" onclick="detail(' . $row->id . ')"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</button>
                        <a href="' . url('transaction/sales/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return SalesRepository::getSummary($startDate, $endDate);
    }

    /**
     * Store or update sales order with transaction locking
     */
    public static function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = Auth::user();
                $orderDate = Carbon::createFromFormat('d/m/Y', $data['order_date']);

                // Calculate totals
                $totalAmount = collect($data['details'])->sum(function ($detail) {
                    return $detail['quantity'] * $detail['sell_price'];
                });

                $discountAmount = $data['discount_amount'] ?? 0;
                $finalAmount = $totalAmount - $discountAmount;

                if (!empty($data['id'])) {
                    // Update existing sales order - lock the row
                    $salesOrder = SalesOrder::lockForUpdate()->find($data['id']);

                    if (!$salesOrder) {
                        throw new \Exception('Data penjualan tidak ditemukan');
                    }

                    $salesOrder->update([
                        'customer_name' => $data['customer_name'] ?? null,
                        'order_date' => $orderDate,
                        'total_amount' => $totalAmount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'payment_method_id' => $data['payment_method_id'] ?? null,
                        'updated_by' => $user->id,
                    ]);

                    // Delete existing details (soft delete)
                    SalesOrderDetail::where('sales_order_id', $salesOrder->id)->delete();
                } else {
                    // Create new sales order with auto-generated invoice number
                    $invoiceNumber = TransactionNumberService::generateSalesInvoice(
                        $user->company_id,
                        $orderDate
                    );

                    $salesOrder = SalesOrder::create([
                        'invoice_number' => $invoiceNumber,
                        'company_id' => $user->company_id,
                        'customer_name' => $data['customer_name'] ?? null,
                        'order_date' => $orderDate,
                        'total_amount' => $totalAmount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'payment_method_id' => $data['payment_method_id'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }

                // Create new details
                foreach ($data['details'] as $detail) {
                    $subtotal = ($detail['quantity'] * $detail['sell_price']) - ($detail['discount_amount'] ?? 0);

                    SalesOrderDetail::create([
                        'sales_order_id' => $salesOrder->id,
                        'invoice_number' => $salesOrder->invoice_number,
                        'product_variant_id' => $detail['product_variant_id'],
                        'quantity' => $detail['quantity'],
                        'sell_price' => $detail['sell_price'],
                        'discount_amount' => $detail['discount_amount'] ?? 0,
                        'subtotal' => $subtotal,
                        'created_by' => $user->id,
                    ]);
                }

                return $salesOrder;
            });
        } catch (\Throwable $th) {
            Log::error('SalesService::store - ' . $th->getMessage());
            return false;
        }
    }

    public static function destroy($id)
    {
        $salesOrder = SalesOrder::find($id);

        if (!$salesOrder) {
            return false;
        }

        $salesOrder->deleted_by = Auth::id();
        $salesOrder->deleted_at = now();
        $salesOrder->save();

        return true;
    }
}
