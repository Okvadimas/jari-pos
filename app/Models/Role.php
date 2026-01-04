<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $table = 'role';
    protected $fillable = [
        'id',
        'nama',
        'slug',
        'insert_at',
        'insert_by',
        'update_at',
        'update_by',
        'status'
    ];

    public function dataTableRole()
    {
        return self::select('id', 'nama', 'status')->where('status', 'active');
    }
}
