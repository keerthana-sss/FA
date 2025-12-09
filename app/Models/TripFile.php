<?php

namespace App\Models;

use App\Enums\TripFileType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'uploaded_by',
        'path',
        'type',
    ];

    protected $casts = [
        // 'metadata' => 'array',
        'type' => TripFileType::class,
    ];

    // Relationships
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeForTrip($q, $tripId)
    {
        return $q->where('trip_id', $tripId);
    }
}
