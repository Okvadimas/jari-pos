<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['category_id', 'company_id', 'name', 'description', 'status', 'created_by', 'updated_by'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
