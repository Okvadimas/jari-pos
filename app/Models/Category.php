<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Category extends Model
{
    use SoftDeletesWithUser;
    protected $table = 'categories';
    protected $fillable = ['company_id', 'code', 'name', 'created_by', 'updated_by'];
    protected $casts = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
