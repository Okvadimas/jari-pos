<?php

namespace App\Repositories\Dashboard;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\Promotion;
use App\Models\Category;

class DashboardRepository
{
    /**
     * Get revenue metrics (daily and monthly)
     */
    public static function getRevenueMetrics($companyId, $today, $startOfMonth)
    {
        $dailyRevenue = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', $today)
            ->whereNull('deleted_at')
            ->sum('final_amount');

        $dailyTransactionCount = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', $today)
            ->whereNull('deleted_at')
            ->count();

        $monthlyRevenue = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', '>=', $startOfMonth)
            ->whereNull('deleted_at')
            ->sum('final_amount');

        $monthlyTransactionCount = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', '>=', $startOfMonth)
            ->whereNull('deleted_at')
            ->count();

        return [
            'dailyRevenue' => $dailyRevenue,
            'dailyTransactionCount' => $dailyTransactionCount,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyTransactionCount' => $monthlyTransactionCount,
        ];
    }

    /**
     * Get monthly purchase expenses
     */
    public static function getPurchaseExpenses($companyId, $startOfMonth)
    {
        return Purchase::where('company_id', $companyId)
            ->whereDate('purchase_date', '>=', $startOfMonth)
            ->whereNull('deleted_at')
            ->sum('total_cost');
    }

    /**
     * Get average cost per variant using Weighted Average Cost
     */
    public static function getVariantAverageCosts($companyId)
    {
        return DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->where('purchases.company_id', $companyId)
            ->whereNull('purchase_details.deleted_at')
            ->select('purchase_details.product_variant_id', DB::raw('AVG(purchase_details.cost_price_per_item) as avg_cost'))
            ->groupBy('purchase_details.product_variant_id')
            ->pluck('avg_cost', 'product_variant_id');
    }

    /**
     * Get monthly sales details for COGS calculation
     */
    public static function getMonthlySalesDetails($companyId, $startOfMonth)
    {
        return SalesOrderDetail::query()
            ->with(['salesOrder'])
            ->whereHas('salesOrder', function($q) use ($companyId, $startOfMonth) {
                $q->where('company_id', $companyId)
                  ->whereDate('order_date', '>=', $startOfMonth)
                  ->whereNull('deleted_at');
            })
            ->get();
    }

    /**
     * Get inventory overview (total products, categories, variants)
     */
    public static function getInventoryOverview($companyId)
    {
        $totalProducts = Product::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->count();

        $totalCategories = Category::whereNull('deleted_at')->count();

        $totalVariants = ProductVariant::whereHas('product', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereNull('deleted_at')
            ->count();

        return [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalVariants' => $totalVariants,
        ];
    }

    /**
     * Get low stock products
     */
    public static function getLowStockProducts($companyId, $threshold = 10)
    {
        return DB::table('stock_daily_balances as sdb')
            ->join('product_variants', 'sdb.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.company_id', $companyId)
            ->where('sdb.date', function($query) {
                $query->selectRaw('MAX(date)')
                      ->from('stock_daily_balances as sub')
                      ->whereColumn('sub.product_variant_id', 'sdb.product_variant_id');
            })
            ->where('sdb.closing_stock', '<=', $threshold)
            ->select(
                'products.name as product_name',
                'product_variants.name as variant_name',
                'sdb.closing_stock',
                'sdb.date as last_updated'
            )
            ->orderBy('sdb.closing_stock', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top selling products
     */
    public static function getTopProducts($companyId, $startOfMonth, $limit = 5)
    {
        return SalesOrderDetail::query()
            ->join('sales_orders', 'sales_order_details.sales_order_id', '=', 'sales_orders.id')
            ->join('product_variants', 'sales_order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('sales_orders.company_id', $companyId)
            ->whereNull('sales_orders.deleted_at')
            ->whereDate('sales_orders.order_date', '>=', $startOfMonth)
            ->select(
                'products.name as product_name',
                'product_variants.name as variant_name',
                DB::raw('SUM(sales_order_details.quantity) as total_qty'),
                DB::raw('SUM(sales_order_details.subtotal) as total_revenue')
            )
            ->groupBy('sales_order_details.product_variant_id', 'products.name', 'product_variants.name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent transactions
     */
    public static function getRecentTransactions($companyId, $limit = 5)
    {
        return SalesOrder::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'customer_name', 'order_date', 'final_amount', 'created_at']);
    }

    /**
     * Get active promotions
     */
    public static function getActivePromotions($companyId, $now)
    {
        return Promotion::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('priority', 'asc')
            ->get(['id', 'name', 'discount_value', 'start_date', 'end_date']);
    }

    /**
     * Get sales chart data
     */
    public static function getSalesChartData($companyId, $startDate)
    {
        return SalesOrder::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->whereDate('order_date', '>=', $startDate)
            ->selectRaw('DATE(order_date) as date, SUM(final_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');
    }
}
