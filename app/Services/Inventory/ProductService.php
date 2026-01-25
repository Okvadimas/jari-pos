<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;

use App\Repositories\Inventory\ProductRepository;

use App\Models\Product;

class ProductService
{
    public static function datatable()
    {
        $data = ProductRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="' . url('inventory/product/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function store($data)
    {
        try {
            DB::beginTransaction();

            if (!empty($data['id'])) {
                $product = Product::find($data['id']);
                $data['updated_by'] = Auth::user()->id;
                $product->update($data);
            } else {
                $data['created_by'] = Auth::user()->id;
                $product = Product::create($data);
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return false;
        }
    }

    public static function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return false;
        }
    }

    public static function generateSku()
    {
        $company = Session::get('company_code');
        $sku = DB::selectOne('CALL generate_kode(?, ?)', ['PRD', $company])->code;
        return $sku;
    }
}
