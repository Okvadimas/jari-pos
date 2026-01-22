<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Company extends Model
{
    use HasFactory, SoftDeletesWithUser;

    protected $table = 'companies';
    protected $fillable = ['name', 'email', 'phone', 'address', 'logo', 'status', 'created_by', 'updated_by'];
    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

}
