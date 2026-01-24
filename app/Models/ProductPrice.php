<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class ProductPrice extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'product_prices';
    protected $fillable = ['product_variant_id', 'purchase_price', 'sell_price', 'created_by', 'updated_by'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
