<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockDailyBalance;
use App\Models\PurchaseDetail;

class IndexController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $companyId = $user->company_id;
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Revenue Metrics (Daily & Monthly)
        // Filter by company_id and status (1 = Active)
        $dailyRevenue = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', $today)
            ->where('status', 1)
            ->sum('final_amount');

        $monthlyRevenue = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', '>=', $startOfMonth)
            ->where('status', 1)
            ->sum('final_amount');

        // 2. Profit Calculation (Revenue - COGS)
        // Strategy: Since sales_order_details doesn't track cost at time of sale,
        // we estimate COGS using Weighted Average Cost (WAC) from purchase_details.
        
        // Get Average Cost per Variant for this Company
        $variantAvgCosts = DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->where('purchases.company_id', $companyId)
            ->where('purchase_details.status', 1)
            ->select('purchase_details.product_variant_id', DB::raw('AVG(purchase_details.cost_price_per_item) as avg_cost'))
            ->groupBy('purchase_details.product_variant_id')
            ->pluck('avg_cost', 'product_variant_id');

        // Calculate COGS for this month's sales
        $monthlySalesDetails = SalesOrderDetail::query()
            ->with(['salesOrder'])
            ->whereHas('salesOrder', function($q) use ($companyId, $startOfMonth) {
                $q->where('company_id', $companyId)
                  ->whereDate('order_date', '>=', $startOfMonth)
                  ->where('status', 1);
            })
            ->get();

        $monthlyCOGS = 0;
        foreach ($monthlySalesDetails as $detail) {
            // Default to 0 if no purchase history found
            $cost = $variantAvgCosts[$detail->product_variant_id] ?? 0;
            $monthlyCOGS += ($cost * $detail->quantity);
        }

        $monthlyProfit = $monthlyRevenue - $monthlyCOGS;

        // 3. Top Selling Products (This Month)
        // Join sales_orders to filter by company and date
        $topProducts = SalesOrderDetail::query()
            ->join('sales_orders', 'sales_order_details.sales_order_id', '=', 'sales_orders.id')
            ->join('product_variants', 'sales_order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('sales_orders.company_id', $companyId)
            ->where('sales_orders.status', 1)
            ->whereDate('sales_orders.order_date', '>=', $startOfMonth)
            ->select(
                'products.name as product_name',
                'product_variants.name as variant_name',
                DB::raw('SUM(sales_order_details.quantity) as total_qty'),
                DB::raw('SUM(sales_order_details.subtotal) as total_revenue')
            )
            ->groupBy('sales_order_details.product_variant_id', 'products.name', 'product_variants.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 4. Inventory Warning (Low Stock)
        // Logic: Get the latest closing_stock from stock_daily_balances for each variant
        // Using a subquery to find the Max Date per Variant
        $lowStockThreshold = 10;
        
        $lowStockProducts = DB::table('stock_daily_balances as sdb')
            ->join('product_variants', 'sdb.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.company_id', $companyId)
            ->where('sdb.date', function($query) {
                $query->selectRaw('MAX(date)')
                      ->from('stock_daily_balances as sub')
                      ->whereColumn('sub.product_variant_id', 'sdb.product_variant_id');
            })
            ->where('sdb.closing_stock', '<=', $lowStockThreshold)
            ->select(
                'products.name as product_name',
                'product_variants.name as variant_name',
                'sdb.closing_stock',
                'sdb.date as last_updated'
            )
            ->orderBy('sdb.closing_stock', 'asc')
            ->limit(10)
            ->get();

        // 5. Chart Data (Last 7 Days Sales)
        // Optimized for Chart.js or ApexCharts consumption
        $chartStartDate = Carbon::today()->subDays(6);
        
        $salesData = SalesOrder::where('company_id', $companyId)
            ->where('status', 1)
            ->whereDate('order_date', '>=', $chartStartDate)
            ->selectRaw('DATE(order_date) as date, SUM(final_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $chartDates = [];
        $chartValues = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $chartStartDate->copy()->addDays($i)->format('Y-m-d');
            $formattedDate = Carbon::parse($date)->format('d M');
            
            $chartDates[] = $formattedDate;
            $chartValues[] = $salesData[$date] ?? 0;
        }

        return view('dashboard.index', compact(
            'dailyRevenue',
            'monthlyRevenue',
            'monthlyProfit',
            'topProducts',
            'lowStockProducts', // Pass to view
            'chartDates',    // X-Axis
            'chartValues'    // Y-Axis
        ));
    }
}