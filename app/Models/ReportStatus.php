<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];
}
