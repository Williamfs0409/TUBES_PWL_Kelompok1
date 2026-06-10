<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacePhoto extends Model
{
    protected $fillable = [
        'place_id',
        'user_id',
        'image_path',
        'image_mime',
        'image_data',
        'caption',
        'sort_order',
        'is_cover',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
