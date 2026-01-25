<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\UnitService;

// Load Request
use App\Http\Requests\Inventory\Unit\StoreUnitRequest;
use App\Models\Unit;

class UnitController extends Controller
{
    private $pageTitle = 'Inventori - Satuan';

    public function index(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/unit/index.js',
        ];

        return view('inventory.unit.index', $data);
    }

    public function datatable()
    {
        return UnitService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/unit/form.js',
        ];

        return view('inventory.unit.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/inventory/unit/form.js',
            'unit' => Unit::find($id),
        ];

        return view('inventory.unit.form', $data);
    }

    public function store(StoreUnitRequest $request)
    {
        $validated = $request->validated();

        $process = UnitService::store($validated);

        $message = !empty($validated['id']) ? 'Satuan berhasil diupdate' : 'Satuan berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = UnitService::destroy($request->id);

        return $process ? $this->successResponse('Satuan berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
