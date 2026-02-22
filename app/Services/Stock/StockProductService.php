<?php

namespace App\Services\Stock;

use Illuminate\Support\Facades\Log;
use App\Repositories\Stock\StockProductRepository;

class StockProductService
{
    /**
     * Decrease stock (sales / POS checkout).
     * Uses atomic decrement to prevent race conditions.
     */
    public static function decrease(int $variantId, int $qty): void
    {
        if ($qty <= 0) return;

        $affected = StockProductRepository::decrementStock($variantId, $qty);

        if ($affected === 0) {
            Log::warning("StockProductService::decrease - Variant {$variantId} not found or deleted.");
        }
    }

    /**
     * Increase stock (purchasing / stock in).
     * Uses atomic increment to prevent race conditions.
     */
    public static function increase(int $variantId, int $qty): void
    {
        if ($qty <= 0) return;

        $affected = StockProductRepository::incrementStock($variantId, $qty);

        if ($affected === 0) {
            Log::warning("StockProductService::increase - Variant {$variantId} not found or deleted.");
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
        $details = StockProductRepository::getSalesOrderDetails($salesOrderId);

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
        $details = StockProductRepository::getPurchaseDetails($purchaseId);

        foreach ($details as $detail) {
            self::decrease($detail->product_variant_id, $detail->quantity);
        }
    }
}
