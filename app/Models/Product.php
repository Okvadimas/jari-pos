<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['company_id', 'unit_id', 'name', 'sku', 'stock', 'purchase_price', 'sell_price', 'status', 'created_by', 'updated_by'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
