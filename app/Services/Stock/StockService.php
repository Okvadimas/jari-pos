<?php

namespace App\Services\Stock;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Decrease stock (sales / POS checkout).
     * Uses atomic decrement to prevent race conditions.
     */
    public static function decrease(int $variantId, int $qty): void
    {
        if ($qty <= 0) return;

        $affected = DB::table('product_variants')
            ->where('id', $variantId)
            ->whereNull('deleted_at')
            ->decrement('current_stock', $qty);

        if ($affected === 0) {
            Log::warning("StockService::decrease - Variant {$variantId} not found or deleted.");
        }
    }

    /**
     * Increase stock (purchasing / stock in).
     * Uses atomic increment to prevent race conditions.
     */
    public static function increase(int $variantId, int $qty): void
    {
        if ($qty <= 0) return;

        $affected = DB::table('product_variants')
            ->where('id', $variantId)
            ->whereNull('deleted_at')
            ->increment('current_stock', $qty);

        if ($affected === 0) {
            Log::warning("StockService::increase - Variant {$variantId} not found or deleted.");
        }
    }

    /**
     * Adjust stock (stock opname, correction).
     * Positive = add, negative = subtract.
     */
    public static function adjust(int $variantId, int $qty): void
    {
        if ($qty === 0) return;

        if ($qty > 0) {
            self::increase($variantId, $qty);
        } else {
            self::decrease($variantId, abs($qty));
        }
    }

    /**
     * Restore stock from cancelled/deleted sales order.
     * Reads the details and increments back.
     */
    public static function restoreFromSales(int $salesOrderId): void
    {
        $details = DB::table('sales_order_details')
            ->where('sales_order_id', $salesOrderId)
            ->whereNull('deleted_at')
            ->select('product_variant_id', 'quantity')
            ->get();

        foreach ($details as $detail) {
            self::increase($detail->product_variant_id, $detail->quantity);
        }
    }

    /**
     * Restore stock from cancelled/deleted purchase.
     * Reads the details and decrements back.
     */
    public static function restoreFromPurchase(int $purchaseId): void
    {
        $details = DB::table('purchase_details')
            ->where('purchase_id', $purchaseId)
            ->whereNull('deleted_at')
            ->select('product_variant_id', 'quantity')
            ->get();

        foreach ($details as $detail) {
            self::decrease($detail->product_variant_id, $detail->quantity);
        }
    }
}
