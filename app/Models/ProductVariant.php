<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $fillable = ['product_id', 'name', 'sku', 'status', 'created_by', 'updated_by'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockDailyBalances()
    {
        return $this->hasMany(StockDailyBalance::class);
    }

    public function salesOrderDetails()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }
}
