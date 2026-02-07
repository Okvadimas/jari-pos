<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;
use App\Models\User;
use App\Models\PaymentMethod;

class SalesOrder extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'sales_orders';
    protected $fillable = ['invoice_number', 'company_id', 'customer_name', 'order_date', 'total_amount', 'applied_promo_id', 'total_discount_manual', 'final_amount', 'created_by', 'updated_by', 'payment_method_id'];

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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}

