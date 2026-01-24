<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\ProductService;

// Load Request
use App\Http\Requests\Inventory\Product\StoreProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    private $pageTitle = 'Inventori - Produk';

    public function index(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/product/index.js',
        ];

        return view('inventory.product.index', $data);
    }

    public function datatable()
    {
        return ProductService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/product/form.js',
        ];

        return view('inventory.product.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/product/form.js',
            'product' => Product::find($id),
        ];

        return view('inventory.product.form', $data);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        ProductService::store($validated);

        $message = !empty($validated['id']) ? 'Produk berhasil diupdate' : 'Produk berhasil ditambahkan';

        return $this->successResponse($message);
    }

    public function destroy(Request $request)
    {
        ProductService::destroy($request->id);

        return $this->successResponse('Produk berhasil dihapus');
    }
}
