<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class PurchaseDetail extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'purchase_details';
    protected $fillable = ['purchase_id', 'product_variant_id', 'quantity', 'cost_price_per_item', 'created_by', 'updated_by'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
