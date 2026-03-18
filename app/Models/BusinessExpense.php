<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class BusinessExpense extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'business_expenses';
    protected $fillable = [
        'expense_number',
        'company_id',
        'category',
        'description',
        'amount',
        'expense_date',
        'vendor_name',
        'reference_note',
        'created_by',
        'updated_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
