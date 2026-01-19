<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Load Service
use App\Services\Management\MenuService;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = [
        'id',
        'code',
        'parent',
        'name',
        'icon',
        'url',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_by'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'menu_id');
    }
}
