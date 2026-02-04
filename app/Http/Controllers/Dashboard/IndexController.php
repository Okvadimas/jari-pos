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
use App\Models\Purchase;
use App\Models\Promotion;
use App\Models\Category;

class IndexController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $companyId = $user->company_id;
        $today = Carbon::today();
        $now = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();

        // =====================================================
        // 1. REVENUE METRICS (Pendapatan)
        // =====================================================
        
        // Pendapatan Hari Ini
        $dailyRevenue = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', $today)
            ->whereNull('deleted_at')
            ->sum('final_amount');

        // Jumlah Transaksi Hari Ini
        $dailyTransactionCount = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', $today)
            ->whereNull('deleted_at')
            ->count();

        // Pendapatan Bulan Ini
        $monthlyRevenue = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', '>=', $startOfMonth)
            ->whereNull('deleted_at')
            ->sum('final_amount');

        // Jumlah Transaksi Bulan Ini
        $monthlyTransactionCount = SalesOrder::where('company_id', $companyId)
            ->whereDate('order_date', '>=', $startOfMonth)
            ->whereNull('deleted_at')
            ->count();

        // Rata-rata Nilai Order (AOV) Bulan Ini
        $averageOrderValue = $monthlyTransactionCount > 0 
            ? $monthlyRevenue / $monthlyTransactionCount 
            : 0;

        // =====================================================
        // 2. PURCHASING EXPENSES (Pengeluaran Pembelian)
        // =====================================================
        
        $monthlyPurchaseExpenses = Purchase::where('company_id', $companyId)
            ->whereDate('purchase_date', '>=', $startOfMonth)
            ->whereNull('deleted_at')
            ->sum('total_cost');

        // =====================================================
        // 3. PROFIT CALCULATION (Estimasi Profit)
        // =====================================================
        
        // Hitung HPP menggunakan Weighted Average Cost (WAC)
        $variantAvgCosts = DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->where('purchases.company_id', $companyId)
            ->whereNull('purchase_details.deleted_at')
            ->select('purchase_details.product_variant_id', DB::raw('AVG(purchase_details.cost_price_per_item) as avg_cost'))
            ->groupBy('purchase_details.product_variant_id')
            ->pluck('avg_cost', 'product_variant_id');

        // Hitung HPP untuk penjualan bulan ini
        $monthlySalesDetails = SalesOrderDetail::query()
            ->with(['salesOrder'])
            ->whereHas('salesOrder', function($q) use ($companyId, $startOfMonth) {
                $q->where('company_id', $companyId)
                  ->whereDate('order_date', '>=', $startOfMonth)
                  ->whereNull('deleted_at');
            })
            ->get();

        $monthlyCOGS = 0;
        foreach ($monthlySalesDetails as $detail) {
            $cost = $variantAvgCosts[$detail->product_variant_id] ?? 0;
            $monthlyCOGS += ($cost * $detail->quantity);
        }

        $monthlyProfit = $monthlyRevenue - $monthlyCOGS;

        // =====================================================
        // 4. INVENTORY OVERVIEW (Ringkasan Inventori)
        // =====================================================
        
        // Total Produk
        $totalProducts = Product::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->count();

        // Total Kategori
        $totalCategories = Category::whereNull('deleted_at')->count();

        // Total Varian Produk
        $totalVariants = ProductVariant::whereHas('product', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereNull('deleted_at')
            ->count();

        // Produk Stok Rendah (threshold = 10)
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

        // =====================================================
        // 5. TOP SELLING PRODUCTS (Produk Terlaris)
        // =====================================================
        
        $topProducts = SalesOrderDetail::query()
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
            ->limit(5)
            ->get();

        // =====================================================
        // 6. RECENT TRANSACTIONS (Transaksi Terbaru)
        // =====================================================
        
        $recentTransactions = SalesOrder::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'customer_name', 'order_date', 'final_amount', 'created_at']);

        // =====================================================
        // 7. ACTIVE PROMOTIONS (Promo Aktif)
        // =====================================================
        
        $activePromotions = Promotion::whereNull('deleted_at')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('priority', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'discount_value', 'start_date', 'end_date']);

        // =====================================================
        // 8. SALES CHART DATA (Grafik Penjualan 7 Hari Terakhir)
        // =====================================================
        
        $chartStartDate = Carbon::today()->subDays(6);
        
        $salesData = SalesOrder::where('company_id', $companyId)
            ->whereNull('deleted_at')
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

        // =====================================================
        // RETURN VIEW
        // =====================================================
        
        return view('dashboard.index', compact(
            // Revenue Metrics
            'dailyRevenue',
            'dailyTransactionCount',
            'monthlyRevenue',
            'monthlyTransactionCount',
            'averageOrderValue',
            // Expenses
            'monthlyPurchaseExpenses',
            // Profit
            'monthlyProfit',
            'monthlyCOGS',
            // Inventory
            'totalProducts',
            'totalCategories',
            'totalVariants',
            'lowStockProducts',
            // Products & Transactions
            'topProducts',
            'recentTransactions',
            // Promotions
            'activePromotions',
            // Chart
            'chartDates',
            'chartValues'
        ));
    }
}