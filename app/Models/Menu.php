<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

// Load Service
use App\Services\Management\MenuService;

class Menu extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'menu';
    protected $fillable = [
        'id',
        'code',
        'parent',
        'name',
        'icon',
        'url',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'menu_id');
    }
}
