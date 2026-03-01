<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationStockDetail extends Model
{
    protected $table = 'recommendation_stock_details';
    public $timestamps = false;

    protected $fillable = [
        'recommendation_stock_id',
        'product_variant_id',
        'total_qty_sold',
        'total_revenue',
        'avg_daily_sales',
        'norm_qty',
        'norm_revenue',
        'score',
        'moving_status',
        'current_stock',
        'lead_time',
        'purchase_price',
        'sell_price',
        'safety_stock',
        'moq',
        'qty_restock',
    ];

    protected $casts = [
        'total_revenue'   => 'decimal:2',
        'avg_daily_sales' => 'decimal:4',
        'norm_qty'        => 'decimal:4',
        'norm_revenue'    => 'decimal:4',
        'score'           => 'decimal:4',
        'purchase_price'  => 'decimal:0',
        'sell_price'      => 'decimal:0',
    ];

    public function recommendationStock()
    {
        return $this->belongsTo(RecommendationStock::class, 'recommendation_stock_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
