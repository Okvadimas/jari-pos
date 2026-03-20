<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'description', 'role_id', 'is_active'];

    public function prices()
    {
        return $this->hasMany(PackagePrice::class);
    }

    public function details()
    {
        return $this->hasMany(PackageDetail::class);
    }
}
