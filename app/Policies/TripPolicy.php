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

    /**
     * Check if they are not owner of that trip
     */
    public function checkIsOwner(User $authUser, Trip $trip): bool
    {
        if ($trip->owner_id !== $authUser->id) {
            throw new AuthorizationException('Only the trip owner can update/detele this trip.');
        }

        return true;
    }


    /**
     * Check if they  are owner of that trip
     */
    public function checkIsNotOwner(User $user, Trip $trip, User $member): bool
    {
        if ($trip->owner_id == $member->id) {
            throw new AuthorizationException('Trip owner cannot be removed.');
        }

        return true;
    }

}
