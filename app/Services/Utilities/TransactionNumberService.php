<?php

namespace App\Services\Utilities;

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
     * Prefix for business expense
     */
    const PREFIX_BUSINESS_EXPENSE = 'BE';

    /**
     * Prefix for app sale
     */
    const PREFIX_APP_SALE = 'AS';

    /**
     * Prefix for affiliate commission
     */
    const PREFIX_AFFILIATE_COMMISSION = 'AC';

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

    /**
     * Generate expense number for business expense
     * Format: BE/2026/03/0001
     */
    public static function generateBusinessExpense(int $companyId, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        return Counter::getNextTransactionNumber(
            self::PREFIX_BUSINESS_EXPENSE,
            $companyId,
            $date->year,
            $date->month
        );
    }

    /**
     * Generate sale number for app sale
     * Format: AS/2026/03/0001
     */
    public static function generateAppSale(int $companyId, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        return Counter::getNextTransactionNumber(
            self::PREFIX_APP_SALE,
            $companyId,
            $date->year,
            $date->month
        );
    }

    /**
     * Generate commission number for affiliate commission
     * Format: AC/2026/03/0001
     */
    public static function generateAffiliateCommission(int $companyId, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        
        return Counter::getNextTransactionNumber(
            self::PREFIX_AFFILIATE_COMMISSION,
            $companyId,
            $date->year,
            $date->month
        );
    }

}
