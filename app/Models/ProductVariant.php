<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;
use App\Models\Unit;
use App\Models\Product;
use App\Models\StockDailyBalance;
use App\Models\SalesOrderDetail;
use App\Models\ProductPrice;

class ProductVariant extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'product_variants';
    protected $fillable = ['product_id', 'unit_id', 'name', 'sku', 'created_by', 'updated_by'];

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

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
