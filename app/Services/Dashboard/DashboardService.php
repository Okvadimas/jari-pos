<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Repositories\Dashboard\DashboardRepository;

class DashboardService
{
    /**
     * Get all dashboard data
     */
    public static function getDashboardData()
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $today = Carbon::today();
        $now = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Revenue Metrics
        $revenueMetrics = DashboardRepository::getRevenueMetrics($companyId, $today, $startOfMonth);
        
        // Calculate Average Order Value
        $averageOrderValue = $revenueMetrics['monthlyTransactionCount'] > 0 
            ? $revenueMetrics['monthlyRevenue'] / $revenueMetrics['monthlyTransactionCount'] 
            : 0;

        // 2. Purchase Expenses
        $monthlyPurchaseExpenses = DashboardRepository::getPurchaseExpenses($companyId, $startOfMonth);

        // 3. Profit Calculation
        $variantAvgCosts = DashboardRepository::getVariantAverageCosts($companyId);
        $monthlySalesDetails = DashboardRepository::getMonthlySalesDetails($companyId, $startOfMonth);
        $monthlyCOGS = self::calculateCOGS($monthlySalesDetails, $variantAvgCosts);
        $monthlyProfit = $revenueMetrics['monthlyRevenue'] - $monthlyCOGS;

        // 4. Inventory Overview
        $inventoryOverview = DashboardRepository::getInventoryOverview($companyId);

        // 5. Low Stock Products
        $lowStockProducts = DashboardRepository::getLowStockProducts($companyId);

        // 6. Top Selling Products
        $topProducts = DashboardRepository::getTopProducts($companyId, $startOfMonth);

        // 7. Recent Transactions
        $recentTransactions = DashboardRepository::getRecentTransactions($companyId);

        // 8. Active Promotions
        $activePromotions = DashboardRepository::getActivePromotions($companyId, $now);

        // 9. Sales Chart Data
        $chartStartDate = Carbon::today()->subDays(6);
        $salesData = DashboardRepository::getSalesChartData($companyId, $chartStartDate);
        $chartData = self::formatChartData($salesData, $chartStartDate);

        return [
            // Revenue Metrics
            'dailyRevenue' => $revenueMetrics['dailyRevenue'],
            'dailyTransactionCount' => $revenueMetrics['dailyTransactionCount'],
            'monthlyRevenue' => $revenueMetrics['monthlyRevenue'],
            'monthlyTransactionCount' => $revenueMetrics['monthlyTransactionCount'],
            'averageOrderValue' => $averageOrderValue,
            // Expenses
            'monthlyPurchaseExpenses' => $monthlyPurchaseExpenses,
            // Profit
            'monthlyProfit' => $monthlyProfit,
            'monthlyCOGS' => $monthlyCOGS,
            // Inventory
            'totalProducts' => $inventoryOverview['totalProducts'],
            'totalCategories' => $inventoryOverview['totalCategories'],
            'totalVariants' => $inventoryOverview['totalVariants'],
            'lowStockProducts' => $lowStockProducts,
            // Products & Transactions
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,
            // Promotions
            'activePromotions' => $activePromotions,
            // Chart
            'chartDates' => $chartData['dates'],
            'chartValues' => $chartData['values'],
        ];
    }

    /**
     * Calculate COGS (Cost of Goods Sold)
     */
    private static function calculateCOGS($salesDetails, $variantCosts)
    {
        $cogs = 0;
        foreach ($salesDetails as $detail) {
            $cost = $variantCosts[$detail->product_variant_id] ?? 0;
            $cogs += ($cost * $detail->quantity);
        }
        return $cogs;
    }

    /**
     * Format chart data for 7 days
     */
    private static function formatChartData($salesData, $startDate)
    {
        $chartDates = [];
        $chartValues = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $formattedDate = Carbon::parse($date)->format('d M');
            
            $chartDates[] = $formattedDate;
            $chartValues[] = $salesData[$date] ?? 0;
        }

        return [
            'dates' => $chartDates,
            'values' => $chartValues,
        ];
    }
}
