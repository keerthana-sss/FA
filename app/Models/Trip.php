<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory;
    protected $fillable = [
        'owner_id',
        'title',
        'destination',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status'     => TripStatus::class,
    ];

    //RELATIONS

    // Trip owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Trip members (pivot)
    public function users()
    {
        return $this->belongsToMany(User::class, 'trip_user')
            ->withPivot('role')->withTimestamps();
    }

    //ROLE & PERMISSION HELPERS
    
    // public function isAdmin(User $user): bool
    // {
    //     return $this->users()
    //         ->wherePivot('user_id', $user->id)
    //         ->wherePivot('role', 'admin')
    //         ->exists();
    // }

    // public function isTraveler(User $user): bool
    // {
    //     return $this->users()
    //         ->wherePivot('user_id', $user->id)
    //         ->wherePivot('role', 'traveler')
    //         ->exists();
    // }
    


    //SCOPES

    public function scopeActive($query)
    {
        return $query->where('status', TripStatus::Active);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('owner_id', $userId);
    }

    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('users', fn($q) => $q->wherePivot('role', $role));
    }
}
