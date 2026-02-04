<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class SalesOrder extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'sales_orders';
    protected $fillable = ['company_id', 'customer_name', 'order_date', 'total_amount', 'applied_promo_id', 'total_discount_manual', 'final_amount', 'created_by', 'updated_by'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    public function appliedPromo()
    {
        return $this->belongsTo(Promotion::class, 'applied_promo_id');
    }
}

