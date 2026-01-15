<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'status', 'created_by', 'updated_by'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
