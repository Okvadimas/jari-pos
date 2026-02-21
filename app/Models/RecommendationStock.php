<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationStock extends Model
{
    protected $table = 'recommendation_stocks';

    protected $fillable = [
        'company_id',
        'analysis_date',
        'period_days',
        'period_start',
        'period_end',
        'total_variants',
        'total_fast',
        'total_medium',
        'total_slow',
        'total_dead',
        'cogs_balance',
        'gross_profit_balance',
    ];

    protected $casts = [
        'analysis_date'        => 'date',
        'period_start'         => 'date',
        'period_end'           => 'date',
        'cogs_balance'         => 'decimal:0',
        'gross_profit_balance' => 'decimal:0',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function details()
    {
        return $this->hasMany(RecommendationStockDetail::class);
    }
}
