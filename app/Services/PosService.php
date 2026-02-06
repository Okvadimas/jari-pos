<?php

namespace App\Services;

use App\Repositories\PosRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PosService
{
    public static function getProducts($request)
    {
        $products = PosRepository::getProducts($request);
        return $products;
    }

    public static function getCategories()
    {
        $products = PosRepository::getCategories();
        return $products;
    }

    public static function getTopSelling()
    {
        $topSelling = PosRepository::getTopSelling();
        return $topSelling;
    }

    public static function getVouchers()
    {
        $vouchers = PosRepository::getVouchers();
        return $vouchers;
    }

    public static function getPaymentMethods()
    {
        $paymentMethods = PosRepository::getPaymentMethods();
        return $paymentMethods;
    }

    public static function store($request)
    {
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            $items = $request['items'];
            $totalAmount = 0;
            
            // 1. Calculate Total & Validate Items
            foreach ($items as $item) {
                $variant = PosRepository::findVariant($item['variant_id']);
                if (!$variant) {
                    throw new \Exception('Produk tidak ditemukan');
                }
                
                $price = 0;
                if ($variant->prices->isNotEmpty()) {
                    $price = $variant->prices->first()->sell_price;
                }
                
                $totalAmount += $price * $item['quantity'];
                
                // Store price for later use to avoid re-fetching (optional optimization)
            }
            
            // 2. Calculate Discount
            $discount = 0;
            $promoId = $request['voucher_id'] ?? null;
            if ($promoId) {
                $promo = PosRepository::findPromo($promoId);
                if ($promo) {
                    // Check min order?
                    if ($promo->min_order_amount && $totalAmount < $promo->min_order_amount) {
                         throw new \Exception('Total belanja kurang untuk voucher ini');
                    }
                    
                    if ($promo->type == 'percentage') {
                         $discount = $totalAmount * ($promo->discount_value / 100);
                    } else {
                         $discount = $promo->discount_value;
                    }
                }
            }
            
            // 3. Create Order
            $orderData = [
                'company_id' => $user->company_id,
                'customer_name' => $request['customer_name'],
                'order_date' => date('Y-m-d'),
                'total_amount' => $totalAmount,
                'applied_promo_id' => $promoId,
                'total_discount_manual' => $discount,
                'final_amount' => $totalAmount - $discount,
                'payment_method_id' => $request['payment_method_id'],
                'created_by' => $user->id,
            ];
            
            $order = PosRepository::storeOrder($orderData);
            
            // 4. Create Order Details
            foreach ($items as $item) {
                $variant = PosRepository::findVariant($item['variant_id']);
                $price = 0;
                if ($variant->prices->isNotEmpty()) {
                    $price = $variant->prices->first()->sell_price;
                }
                
                $detailData = [
                    'sales_order_id' => $order->id,
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'subtotal' => $price * $item['quantity'],
                    'created_by' => $user->id
                ];
                
                PosRepository::storeOrderDetail($detailData);
            }
            
            DB::commit();
            
            return [
                'status' => true,
                'data' => $order
            ];
            
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}
