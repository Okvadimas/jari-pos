<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_orders';
    protected $fillable = ['company_id', 'order_date', 'total_amount', 'applied_promo_id', 'total_discount_manual', 'final_amount', 'status', 'created_by', 'updated_by'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }
}
