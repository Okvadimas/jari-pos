<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class StockOpnameDetail extends Model
{
    use SoftDeletesWithUser;

    protected $table = 'stock_opname_details';

    protected $fillable = [
        'stock_opname_id', 'product_variant_id', 'system_stock',
        'physical_stock', 'difference', 'notes', 'created_by', 'updated_by'
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
