<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\ProductVariantService;

// Load Request
use App\Http\Requests\Inventory\ProductVariant\StoreProductVariantRequest;

// Load Model
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrice;

class ProductVariantController extends Controller
{
    private $pageTitle = 'Inventori - Produk Varian';

    public function index(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/product-variant/index.js',
        ];

        return view('inventory.product-variant.index', $data);
    }

    public function datatable()
    {
        return ProductVariantService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title'         => $this->pageTitle,
            'products'      => Product::select('id', 'name')->get(),
            'js'            => 'resources/js/pages/inventory/product-variant/form.js',
        ];

        return view('inventory.product-variant.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title'             => $this->pageTitle,
            'js'                => 'resources/js/pages/inventory/product-variant/form.js',
            'productVariant'    => ProductVariant::find($id),
            'productPrices'     => ProductPrice::where('product_variant_id', $id)->where('is_active', 1)->first(),
            'products'          => Product::select('id', 'name')->get(),
        ];

        return view('inventory.product-variant.form', $data);
    }

    public function store(StoreProductVariantRequest $request)
    {
        $validated = $request->validated();

        $process = ProductVariantService::store($validated);

        $message = !empty($validated['id']) ? 'Produk varian berhasil diupdate' : 'Produk varian berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan');
    }

    public function destroy(Request $request)
    {
        $process = ProductVariantService::destroy($request->id);

        return $process ? $this->successResponse('Produk varian berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
