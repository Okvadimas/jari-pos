<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;

use App\Repositories\Inventory\ProductVariantRepository;

use App\Models\ProductVariant;
use App\Models\ProductPrice;

class ProductVariantService
{
    public static function datatable()
    {
        $data = ProductVariantRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="' . url('inventory/product-variant/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
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

                $productVariant = ProductVariant::find($data['id']);

                $dataProductVariant = [
                    'product_id'    => $data['product'],
                    'name'          => $data['name'],
                    'sku'           => $data['sku'],
                    'updated_by'    => Auth::user()->id,
                ];

                $productVariant->update($dataProductVariant);

                // Jika edit_price == on & purchase_price atau sell_price tidak sama dengan data sebelumnya
                $productPrice = ProductPrice::where('product_variant_id', $productVariant->id)->first();
                $editPrice = $data['edit_price'] ?? null;

                if ($editPrice === 'on' && ($productPrice->purchase_price != str_replace('.', '', $data['purchase_price']) || $productPrice->sell_price != str_replace('.', '', $data['sell_price']))) {
                    
                    ProductPrice::where('product_variant_id', $productVariant->id)->update([
                        'is_active'     => 0,
                        'updated_by'    => Auth::user()->id,
                    ]);

                    $dataProductPrice = [
                        'product_variant_id'    => $productVariant->id,
                        'purchase_price'        => str_replace('.', '', $data['purchase_price']),
                        'sell_price'            => str_replace('.', '', $data['sell_price']),
                        'updated_by'            => Auth::user()->id,
                        'is_active'             => 1,
                        'created_by'            => Auth::user()->id,
                    ];

                    ProductPrice::create($dataProductPrice);
                }
            } else {

                $dataProductVariant = [
                    'product_id'    => $data['product'],
                    'name'          => $data['name'],
                    'sku'           => $data['sku'],
                    'created_by'    => Auth::user()->id,
                ];

                $productVariant     = ProductVariant::create($dataProductVariant);

                $dataProductPrice = [
                    'product_variant_id'    => $productVariant->id,
                    'purchase_price'        => str_replace('.', '', $data['purchase_price']),
                    'sell_price'            => str_replace('.', '', $data['sell_price']),
                    'created_by'            => Auth::user()->id,
                    'is_active'             => 1
                ];

                ProductPrice::create($dataProductPrice);
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return false;
        }
    }

    public static function generateSku()
    {
        $company = Session::get('company_code');
        $sku = DB::selectOne('CALL generate_kode(?, ?)', ['PRV', $company])->code;
        return $sku;
    }

    public static function destroy($id)
    {
        try {
            $productVariant = ProductVariant::find($id);
            $productVariant->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return false;
        }
    }
}
