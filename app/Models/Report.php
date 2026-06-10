<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'place_id',
        'report_category_id',
        'report_status_id',
        'description',
        'admin_note',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'report_category_id');
    }

    public function status()
    {
        return $this->belongsTo(ReportStatus::class, 'report_status_id');
    }

    public function photos()
    {
        return $this->hasMany(ReportPhoto::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
