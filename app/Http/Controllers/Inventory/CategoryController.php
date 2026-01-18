<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\CategoryService;

// Load Request
use App\Http\Requests\Inventory\Category\StoreCategoryRequest;
use App\Http\Requests\Inventory\Category\UpdateCategoryRequest;
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

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        $category = CategoryService::store($validated);
        if (!$category) {
            return $this->errorResponse('Kategori gagal ditambahkan');
        }

        return $this->successResponse('Kategori berhasil ditambahkan', $category);
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

    public function update(UpdateCategoryRequest $request)
    {
        $validated = $request->validated();

        $category = CategoryService::update($validated, $request->id);
        if (!$category) {
            return $this->errorResponse('Kategori gagal diupdate');
        }

        return $this->successResponse('Kategori berhasil diupdate', $category);
    }

    public function destroy(Request $request)
    {
        $category = Category::find($request->id);
        if ($category) {
             $category->update([
                'status' => 0,
                'updated_by' => auth()->user()->id,
             ]);
        } else {
            return $this->errorResponse('Kategori gagal dihapus');
        }

        return $this->successResponse('Kategori berhasil dihapus', $category);
    }
}
