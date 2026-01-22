<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Role extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'roles';
    protected $fillable = [
        'id',
        'name',
        'slug',
        'status',
        'created_by',
        'updated_by'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'role_id');
    }

    public function dataTableRole()
    {
        return self::select('id', 'slug', 'name', 'status')->where('status', 1);
    }
}
