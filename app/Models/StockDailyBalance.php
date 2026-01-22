<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class StockDailyBalance extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'stock_daily_balances';
    protected $fillable = ['product_variant_id', 'date', 'opening_stock', 'in_stock', 'out_stock', 'adjustment_stock', 'closing_stock', 'status', 'created_by', 'updated_by'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
