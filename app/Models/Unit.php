<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Unit extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'units';
    protected $fillable = ['code', 'name', 'status', 'created_by', 'updated_by'];
    protected $casts = [
        'status' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
