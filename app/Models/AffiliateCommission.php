<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class AffiliateCommission extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'affiliate_commissions';
    protected $fillable = [
        'commission_number',
        'company_id',
        'app_sale_id',
        'affiliate_name',
        'affiliate_coupon_code',
        'sale_amount',
        'commission_rate',
        'commission_amount',
        'status',
        'paid_date',
        'reference_note',
        'created_by',
        'updated_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function appSale()
    {
        return $this->belongsTo(AppSale::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
