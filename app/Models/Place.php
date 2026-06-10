<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Like;
use App\Models\Bookmark;
use App\Models\PlacePhoto;
use App\Models\Review;

class Place extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'address',
        'city',
        'province',
        'latitude',
        'longitude',
        'google_maps_url',
        'image',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function photos()
    {
        return $this->hasMany(PlacePhoto::class);
    }

    public function coverPhoto()
    {
        return $this->hasOne(PlacePhoto::class)->where('is_cover', true)->oldest('sort_order');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
