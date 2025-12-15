<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Itinerary;
use App\Models\User;
use App\Contracts\ItineraryRepositoryInterface;

class ItineraryService
{
    protected ItineraryRepositoryInterface $repository;

    public function __construct(ItineraryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listItineraries(Trip $trip, User $user)
    {
        return $this->repository->allItinerariesByTrip($trip->id);
    }

    public function createItinerary(array $data, Trip $trip, User $user): Itinerary
    {
        // return $this->repository->create($data, $trip, $user);
        return $this->repository->create([
            "trip_id"     => $trip->id,
            "created_by"  => $user->id,
            "title"       => $data["title"],
            "description" => $data["description"],
            "day_number"  => $data["day_number"],
            "start_time"  => $data["start_time"],
            "end_time"    => $data["end_time"],
        ]);
    }

    public function updateItinerary(Itinerary $itinerary, array $data, User $user): Itinerary
    {
        $data['updated_by'] = $user->id;
        return $this->repository->update($itinerary, $data);
    }

    public function deleteItinerary(Itinerary $itinerary, User $user): bool
    {
        return $this->repository->delete($itinerary);
    }
}
