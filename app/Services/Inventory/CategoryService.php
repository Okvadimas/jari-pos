<?php

namespace App\Services\Inventory;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Inventory\CategoryRepository;

class CategoryService
{
    public static function datatable()
    {
        $data = CategoryRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if(!$row->status) {
                    return '';
                }
                return '<a href="' . url('inventory/category/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->editColumn('status', function ($row) {
                return $row->status == 1 ? 'Active' : 'Inactive';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public static function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = [
                'name' => $request['name'],
                'created_by' => auth()->user()->id,
            ];

            return Category::create($data);
        });
    }

    public static function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $category = Category::findOrFail($id);
            
            $data = [
                'name' => $request['name'],
                'updated_by' => auth()->user()->id,
            ];

            $category->update($data);

            return $category;
        });
    }
}
