<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Permission extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'permissions';
    protected $fillable = ['role_id', 'menu_id', 'status', 'created_by', 'updated_by'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
