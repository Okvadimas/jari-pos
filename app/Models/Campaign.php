<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletesWithUser;

class Campaign extends Model
{
    use HasFactory, SoftDeletesWithUser;

    protected $table = 'campaigns';
    protected $fillable = ['title', 'image', 'type', 'status'];
}
