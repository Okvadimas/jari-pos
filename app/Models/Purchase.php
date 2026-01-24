<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Purchase extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'purchases';
    protected $fillable = ['company_id', 'purchase_date', 'supplier_name', 'total_cost', 'reference_note', 'created_by', 'updated_by'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
