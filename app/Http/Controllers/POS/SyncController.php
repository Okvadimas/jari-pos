<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Product;

class SyncController extends Controller
{
    /**
     * Conflict Resolution Policies:
     * - 'accept': Terima transaksi bagaimanapun kondisi stok (fast-moving)
     * - 'reject_if_stock_zero': Tolak jika stok tidak cukup (slow-moving)
     * - 'backorder': Terima tapi tandai sebagai backorder untuk fulfillment nanti
     * 
     * @param int $productId
     * @return string
     */
    private function getProductSyncPolicy($productId): string
    {
        $product = Product::find($productId);
        
        // Check product's sync_policy field (default: accept)
        return $product->sync_policy ?? 'accept';
    }

    /**
     * Sync offline transactions
     * POST /pos/sync/transactions
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncTransactions(Request $request)
    {
        $transactions = $request->input('transactions', []);
        $accepted = [];
        $rejected = [];
        $backorders = [];
        
        foreach ($transactions as $txn) {
            $canProcess = true;
            $isBackorder = false;
            $rejectReason = null;
            
            // Check each item in transaction
            if (!empty($txn['items'])) {
                foreach ($txn['items'] as $item) {
                    $policy = $this->getProductSyncPolicy($item['product_id']);
                    $product = Product::find($item['product_id']);
                    $hasStock = $product && ($product->stock ?? 999) >= ($item['quantity'] ?? 1);
                    
                    switch ($policy) {
                        case 'reject_if_stock_zero':
                            if (!$hasStock) {
                                $canProcess = false;
                                $rejectReason = "Stok {$product->name} tidak mencukupi";
                            }
                            break;
                        case 'backorder':
                            if (!$hasStock) {
                                $isBackorder = true;
                            }
                            break;
                        case 'accept':
                        default:
                            // Always proceed
                            break;
                    }
                    
                    if (!$canProcess) break;
                }
            }
            
            if ($canProcess) {
                DB::beginTransaction();
                try {
                    // Create SalesOrder
                    $order = SalesOrder::create([
                        'customer_name' => $txn['customer_name'] ?? 'Walk-in Customer',
                        'order_type' => $txn['order_type'] ?? 'dine_in',
                        'payment_method_id' => $txn['payment_method_id'] ?? null,
                        'voucher_id' => $txn['voucher_id'] ?? null,
                        'subtotal' => $txn['subtotal'] ?? 0,
                        'discount' => $txn['discount'] ?? 0,
                        'tax' => $txn['tax'] ?? 0,
                        'total' => $txn['total'] ?? 0,
                        'is_offline_order' => true,
                        'is_backorder' => $isBackorder,
                        'offline_created_at' => $txn['created_at'] ?? now(),
                        'status' => $isBackorder ? 'backorder' : 'completed',
                    ]);
                    
                    // Create SalesOrderDetails
                    if (!empty($txn['items'])) {
                        foreach ($txn['items'] as $item) {
                            SalesOrderDetail::create([
                                'sales_order_id' => $order->id,
                                'product_id' => $item['product_id'],
                                'product_variant_id' => $item['variant_id'] ?? null,
                                'quantity' => $item['quantity'] ?? 1,
                                'price' => $item['price'] ?? 0,
                                'subtotal' => ($item['quantity'] ?? 1) * ($item['price'] ?? 0),
                            ]);
                            
                            // Reduce stock if not backorder
                            if (!$isBackorder) {
                                $product = Product::find($item['product_id']);
                                if ($product && isset($product->stock)) {
                                    $product->stock = max(0, $product->stock - ($item['quantity'] ?? 1));
                                    $product->save();
                                }
                            }
                        }
                    }
                    
                    DB::commit();
                    
                    $result = [
                        'client_id' => $txn['client_id'] ?? null,
                        'status' => 'success',
                        'server_id' => $order->id
                    ];
                    
                    if ($isBackorder) {
                        $result['is_backorder'] = true;
                        $backorders[] = $result;
                    } else {
                        $accepted[] = $result;
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $rejected[] = [
                        'client_id' => $txn['client_id'] ?? null,
                        'status' => 'error',
                        'reason' => 'Database error: ' . $e->getMessage()
                    ];
                }
            } else {
                $rejected[] = [
                    'client_id' => $txn['client_id'] ?? null,
                    'status' => 'rejected',
                    'reason' => $rejectReason
                ];
            }
        }
        
        return response()->json([
            'status' => 'success',
            'total' => count($transactions),
            'accepted' => count($accepted),
            'backorders' => count($backorders),
            'rejected' => count($rejected),
            'results' => [
                'accepted' => $accepted,
                'backorders' => $backorders,
                'rejected' => $rejected
            ]
        ]);
    }
}
