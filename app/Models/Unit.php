<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Unit extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'units';
    protected $fillable = ['code', 'name', 'created_by', 'updated_by'];
    protected $casts = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
