<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Category extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'categories';
    protected $fillable = ['name', 'status', 'created_by', 'updated_by'];
    protected $casts = [
        'status' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
