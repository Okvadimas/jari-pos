<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseDocument extends Model
{
    protected $fillable = [
        'company_id',
        'filename',
        'file_path',
        'type',
        'status',
        'error_message',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
