<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class SalesOrderDetail extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'sales_order_details';
    protected $fillable = ['sales_order_id', 'invoice_number', 'product_variant_id', 'quantity', 'sell_price', 'purchase_price', 'discount_amount', 'subtotal', 'created_by', 'updated_by'];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
