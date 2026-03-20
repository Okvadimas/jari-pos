<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagePrice extends Model
{
    protected $fillable = ['package_id', 'duration_months', 'price', 'is_active'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
