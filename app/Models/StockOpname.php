<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class StockOpname extends Model
{
    use SoftDeletesWithUser;

    protected $table = 'stock_opnames';

    protected $fillable = [
        'opname_number', 'company_id', 'opname_date', 'status', 'notes',
        'approved_by', 'approved_at', 'created_by', 'updated_by'
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
