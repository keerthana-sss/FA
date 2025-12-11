<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //RELATIONS

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    //
    
    public function roleInTrip(int $tripId): ?string
    {
        return $this->trips()
            ->where('trip_id', $tripId)
            ->first()
            ?->pivot
            ->role;
    }

    public function isTripAdmin(int $tripId): bool
    {
        return $this->roleInTrip($tripId) === 'admin';
    }
}
