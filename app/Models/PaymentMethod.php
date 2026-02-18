<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class PaymentMethod extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'payment_methods';
    protected $fillable = ['company_id', 'name', 'type', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by'];
    protected $casts = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
