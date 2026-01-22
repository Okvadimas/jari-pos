<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\CategoryService;

// Load Request
use App\Http\Requests\Inventory\Category\StoreCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    private $pageTitle = 'Inventori - Kategori';

    public function index(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/category/index.js',
        ];

        return view('inventory.category.index', $data);
    }

    public function datatable()
    {
        return CategoryService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/category/form.js',
        ];

        return view('inventory.category.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/category/form.js',
            'category' => Category::find($id),
        ];

        return view('inventory.category.form', $data);
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        CategoryService::store($validated);

        $message = !empty($validated['id']) ? 'Kategori berhasil diupdate' : 'Kategori berhasil ditambahkan';

        return $this->successResponse($message);
    }

    public function destroy(Request $request)
    {
        $category = Category::find($request->id);
        $category->delete();
        
        return $this->successResponse('Kategori berhasil dihapus');
    }
}
