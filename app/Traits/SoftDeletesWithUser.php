<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait SoftDeletesWithUser
{
    use SoftDeletes;

    /**
     * Boot the soft deleting with user trait.
     */
    protected static function bootSoftDeletesWithUser()
    {
        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }
}
