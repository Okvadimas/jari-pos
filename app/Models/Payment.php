<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Payment extends Model
{
    use HasFactory, SoftDeletesWithUser;

    protected $table = 'payment_methods';
    protected $fillable = ['name', 'type', 'status', 'created_by', 'updated_by'];
    protected $casts = [
        'status' => 'boolean',
    ];
}
