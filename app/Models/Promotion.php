<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UserSignatureTrait; // Assuming this trait exists based on other models, but if not sure, I can omit or check. IndexController used models without showing traits. Safe to stick to basics first.

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Helper to check if active
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now);
    }
}
