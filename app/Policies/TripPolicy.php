<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

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
     * Check if they are not owner of that trip
     */
    public function checkIsNotOwner(User $user, Trip $trip): bool
    {
        if ($trip->owner_id !== $user->id) {
            throw new AuthorizationException('Only the trip owner can update/detele this trip.');
        }

        return true;
    }


    /**
     * Check if they  are owner of that trip
     */
    public function checkIsOwner(User $user, Trip $trip): bool
    {
        if ($trip->owner_id == $user->id) {
            throw new AuthorizationException('Trip owner cannot be removed.');
        }

        return true;
    }

}
