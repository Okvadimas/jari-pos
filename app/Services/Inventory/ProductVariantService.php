<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

use App\Repositories\Inventory\ProductVariantRepository;

use App\Models\Product;
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

                $sku = self::generateSku($data['product'], $data['name']);

                $dataProductVariant = [
                    'product_id'    => $data['product'],
                    'name'          => $data['name'],
                    'sku'           => $sku,
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

    /**
     * Generate SKU untuk produk varian
     * Format: KAT-0001-VARIAN (product SKU + nama varian uppercase)
     * Contoh: FOD-0025-PEDAS
     *
     * @param int $productId
     * @param string $variantName
     * @return string
     */
    public static function generateSku(int $productId, string $variantName): string
    {
        $product = Product::findOrFail($productId);

        // Ambil SKU produk induk (misal: FOD-0025)
        $productSku = $product->sku;

        // Bersihkan nama varian: uppercase, spasi → strip, hapus karakter spesial
        $cleanVariant = strtoupper(Str::slug($variantName, '-'));

        return $productSku . '-' . $cleanVariant;
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
