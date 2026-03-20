<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Voucher extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'vouchers';
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];



    /**
     * Check if coupon is valid for use
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_until && now()->gt($this->valid_until)) return false;
        return true;
    }

    /**
     * Calculate discount amount for a given price
     */
    public function calculateDiscount($originalAmount): float
    {
        if ($this->type === 'percentage') {
            return round($originalAmount * ($this->value / 100));
        }
        return min($this->value, $originalAmount);
    }
}
