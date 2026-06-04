<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'place_id',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
