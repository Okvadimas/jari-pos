<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class AppSale extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'app_sales';
    protected $fillable = [
        'sale_number',
        'company_id',
        'customer_name',
        'customer_email',
        'plan_name',
        'duration_months',
        'is_renewal',
        'original_amount',
        'discount_amount',
        'affiliate_discount_amount',
        'final_amount',
        'affiliate_coupon_code',
        'discount_coupon_code',
        'status',
        'confirmed_by',
        'confirmed_at',
        'sale_date',
        'reference_note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_renewal' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function affiliateCommission()
    {
        return $this->hasOne(AffiliateCommission::class);
    }
}
