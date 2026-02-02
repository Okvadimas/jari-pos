<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Model
use App\Models\Product;
use App\Models\Category;
use App\Models\SalesOrderDetail;
use App\Models\Promotion;

class IndexController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function getProducts(Request $request)
    {
        $query = Product::with(['category', 'variants.prices', 'company']);

        if ($request->has('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12);
        
        // Transform data to ensure price is easily accessible and prevent NaN
        $products->getCollection()->transform(function ($product) {
            $variant = $product->variants->first();
            $price = 0;
            if ($variant && $variant->prices->isNotEmpty()) {
                $price = $variant->prices->first()->sell_price;
            }
            // Append custom attributes for simpler JS access
            $product->price_display = $price;
            return $product;
        });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function getCategories()
    {
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function getTopSelling()
    {
        // 1. Try to get real top selling data
        // Use toBase() to ensure we get a Support\Collection, preventing Eloquent\Collection::merge issues later
        $topSelling = SalesOrderDetail::select('product_variant_id', \DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_sold')
            ->take(4) // Limit to 4 as requested
            ->with(['variant.product', 'variant.prices'])
            ->get()
            ->map(function ($item) {
                if(!$item->variant || !$item->variant->product) return null;
                $variant = $item->variant;
                $product = $variant->product;
                $price = 0;
                if ($variant->prices->isNotEmpty()) {
                    $price = $variant->prices->first()->sell_price;
                }
                
                return [
                    'id' => $product->id, 
                    'variant_id' => $variant->id,
                    'name' => $product->name,
                    'image' => null, 
                    'price' => (float) $price, // Ensure float
                    'category' => $product->category->name ?? 'Uncategorized',
                    'total_sold' => $item->total_sold
                ];
            })->filter()->values();
            
        // Convert to base collection if it happens to be an Eloquent collection (e.g. if map didn't change type logic effectively enough when empty)
        // Although map() usually returns Support\Collection, explicit cast is safer.
        $topSelling = collect($topSelling);

        // 2. Fallback to dummy data (latest products) if not enough sales data
        if ($topSelling->count() < 4) {
            $needed = 4 - $topSelling->count();
            // Exclude already added IDs if possible, but for simplicity just take latest
            $dummyProducts = \App\Models\Product::with(['variants.prices', 'category'])
                ->latest()
                ->take($needed)
                ->get()
                ->map(function($product) {
                    $variant = $product->variants->first();
                    $price = 0;
                    if ($variant && $variant->prices->isNotEmpty()) {
                        $price = $variant->prices->first()->sell_price;
                    }
                    
                    return [
                        'id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'name' => $product->name,
                        'image' => null,
                        'price' => (float) $price,
                        'category' => $product->category->name ?? 'Uncategorized',
                        'total_sold' => 0 // Dummy
                    ];
                });
            
            $topSelling = $topSelling->merge($dummyProducts);
        }

        return response()->json([
            'status' => 'success',
            'data' => $topSelling
        ]);
    }

    public function getVouchers()
    {
        // Fetch active promotions
        $vouchers = Promotion::active()
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'code' => $p->name, 
                    'name' => $p->name,
                    'type' => 'fixed',
                    'amount' => $p->discount_value,
                    'min_order' => $p->min_order_amount,
                    'description' => "Potongan Rp " . number_format($p->discount_value, 0, ',', '.'),
                    'constraints' => [
                        'category_id' => $p->category_id,
                        'product_id' => $p->product_id,
                        'variant_id' => $p->product_variant_id
                    ]
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $vouchers
        ]);
    }
}