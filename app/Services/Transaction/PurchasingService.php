<?php

namespace App\Services\Transaction;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Transaction\PurchasingRepository;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Services\TransactionNumberService;

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
                return '<a href="' . url('transaction/purchasing/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return PurchasingRepository::getSummary($startDate, $endDate);
    }

    /**
     * Store or update purchase with transaction locking
     */
    public static function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = Auth::user();
                $purchaseDate = Carbon::createFromFormat('d/m/Y', $data['purchase_date']);

                // Calculate total cost
                $totalCost = collect($data['details'])->sum(function ($detail) {
                    return $detail['quantity'] * $detail['cost_price_per_item'];
                });

                if (!empty($data['id'])) {
                    // Update existing purchase - lock the row
                    $purchase = Purchase::lockForUpdate()->find($data['id']);
                    
                    if (!$purchase) {
                        throw new \Exception('Data pembelian tidak ditemukan');
                    }

                    $purchase->update([
                        'supplier_name' => $data['supplier_name'],
                        'purchase_date' => $purchaseDate,
                        'total_cost' => $totalCost,
                        'reference_note' => $data['reference_note'] ?? null,
                        'updated_by' => $user->id,
                    ]);

                    // Delete existing details (soft delete)
                    PurchaseDetail::where('purchase_id', $purchase->id)->delete();
                } else {
                    // Create new purchase with auto-generated order number
                    $orderNumber = TransactionNumberService::generatePurchaseOrder(
                        $user->company_id,
                        $purchaseDate
                    );

                    $purchase = Purchase::create([
                        'order_number' => $orderNumber,
                        'company_id' => $user->company_id,
                        'supplier_name' => $data['supplier_name'],
                        'purchase_date' => $purchaseDate,
                        'total_cost' => $totalCost,
                        'reference_note' => $data['reference_note'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }

                // Create new details
                foreach ($data['details'] as $detail) {
                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_variant_id' => $detail['product_variant_id'],
                        'quantity' => $detail['quantity'],
                        'cost_price_per_item' => $detail['cost_price_per_item'],
                        'created_by' => $user->id,
                    ]);
                }

                return $purchase;
            });
        } catch (\Throwable $th) {
            Log::error('PurchasingService::store - ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Soft delete purchase
     */
    public static function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $purchase = Purchase::find($id);

                if (!$purchase) {
                    return false;
                }

                // Soft delete details first
                PurchaseDetail::where('purchase_id', $id)->delete();

                // Soft delete purchase
                $purchase->delete();

                return true;
            });
        } catch (\Throwable $th) {
            Log::error('PurchasingService::destroy - ' . $th->getMessage());
            return false;
        }
    }
}
