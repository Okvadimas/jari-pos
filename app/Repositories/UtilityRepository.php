<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UtilityRepository {

    protected $company;

    public function __construct(Company $company) {
        $this->company = $company;
    }

    public function dataCompanies($search)
    {
        $query = $this->company->select('id', 'name')->whereNull('deleted_at');

        if($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query;
    }

    public function dataProductVariants($search = null)
    {
        $user = Auth::user();

        $query = DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->whereNull('pv.deleted_at')
            ->whereNull('p.deleted_at')
            ->where('p.company_id', $user->company_id)
            ->select(
                'pv.id',
                'pv.name as variant_name',
                'pv.sku',
                'p.name as product_name'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.name', 'like', '%' . $search . '%')
                  ->orWhere('pv.name', 'like', '%' . $search . '%')
                  ->orWhere('pv.sku', 'like', '%' . $search . '%');
            });
        }

        $result = $query->orderBy('p.name')
            ->orderBy('pv.name')
            ->get();

        return $result->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : '') . ' (' . $item->sku . ')',
                'sku' => $item->sku,
            ];
        })->values();
    }

}
