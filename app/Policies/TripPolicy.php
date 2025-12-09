<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Any member of the trip can view it.
     */
    public function view(User $user, Trip $trip): bool
    {
        return $trip->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Any member of the trip can update it.
     */
    public function update(User $user, Trip $trip): bool
    {
        return $trip->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Any member of the trip can delete it.
     */
    public function delete(User $user, Trip $trip): bool
    {
        return $trip->members()->where('user_id', $user->id)->exists();
    }
}
