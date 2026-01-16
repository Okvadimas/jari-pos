<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Inventory\UnitService;

// Load Request
use App\Http\Requests\Inventory\Unit\StoreUnitRequest;
use App\Http\Requests\Inventory\Unit\UpdateUnitRequest;
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

    public function store(StoreUnitRequest $request)
    {
        $validated = $request->validated();

        $unit = UnitService::store($validated);
        if (!$unit) {
            return $this->errorResponse('Satuan gagal ditambahkan');
        }

        return $this->successResponse('Satuan berhasil ditambahkan', $unit);
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

    public function update(UpdateUnitRequest $request)
    {
        $validated = $request->validated();

        $unit = UnitService::update($validated, $request->id);
        if (!$unit) {
            return $this->errorResponse('Satuan gagal diupdate');
        }

        return $this->successResponse('Satuan berhasil diupdate', $unit);
    }

    public function destroy(Request $request)
    {
        $unit = Unit::find($request->id);
        if ($unit) {
             $unit->update([
                'status' => 0,
                'updated_by' => auth()->user()->id,
             ]);
        } else {
            return $this->errorResponse('Satuan gagal dihapus');
        }

        return $this->successResponse('Satuan berhasil dihapus', $unit);
    }
}
