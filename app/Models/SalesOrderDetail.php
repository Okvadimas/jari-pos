<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class SalesOrderDetail extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'sales_order_details';
    protected $fillable = ['sales_order_id', 'product_variant_id', 'quantity', 'unit_price', 'discount_auto_amount', 'subtotal', 'status', 'created_by', 'updated_by'];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
