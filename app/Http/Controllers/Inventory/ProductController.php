<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\ProductService;

// Load Request
use App\Http\Requests\Inventory\Product\StoreProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;

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
            'title'         => $this->pageTitle,
            'categories'    => Category::select('id', 'name')->get(),
            'companies'     => Company::select('id', 'name')->get(),
            'js'            => 'resources/js/pages/inventory/product/form.js',
        ];

        return view('inventory.product.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title'         => $this->pageTitle,
            'js'            => 'resources/js/pages/inventory/product/form.js',
            'product'       => Product::find($id),
            'categories'    => Category::select('id', 'name')->get(),
            'companies'     => Company::select('id', 'name')->get(),
        ];

        return view('inventory.product.form', $data);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $process = ProductService::store($validated);

        $message = !empty($validated['id']) ? 'Produk berhasil diupdate' : 'Produk berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = ProductService::destroy($request->id);

        return $process ? $this->successResponse('Produk berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
