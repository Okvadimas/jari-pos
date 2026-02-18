<?php

namespace App\Services;

use App\Models\Counter;
use Carbon\Carbon;

class TransactionNumberService
{
    /**
     * Prefix for sales invoice
     */
    const PREFIX_SALES = 'INV';

    /**
     * Prefix for purchasing order
     */
    const PREFIX_PURCHASE = 'ORD';

    /**
     * Prefix for stock opname
     */
    const PREFIX_STOCK_OPNAME = 'SO';

    /**
     * Generate invoice number for sales
     * Format: INV/2026/02/0001
     *
     * @param int $companyId
     * @param Carbon|null $date Optional date, defaults to current date
     * @return string
     */
    public static function generateSalesInvoice(int $companyId, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        return Counter::getNextTransactionNumber(
            self::PREFIX_SALES,
            $companyId,
            $date->year,
            $date->month
        );
    }

    /**
     * Generate order number for purchasing
     * Format: ORD/2026/02/0001
     *
     * @param int $companyId
     * @param Carbon|null $date Optional date, defaults to current date
     * @return string
     */
    public static function generatePurchaseOrder(int $companyId, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        return Counter::getNextTransactionNumber(
            self::PREFIX_PURCHASE,
            $companyId,
            $date->year,
            $date->month
        );
    }

    /**
     * Generate opname number for stock opname
     * Format: SO/2026/02/0001
     *
     * @param int $companyId
     * @param Carbon|null $date Optional date, defaults to current date
     * @return string
     */
    public static function generateStockOpnameNumber(int $companyId, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        return Counter::getNextTransactionNumber(
            self::PREFIX_STOCK_OPNAME,
            $companyId,
            $date->year,
            $date->month
        );
    }
}
