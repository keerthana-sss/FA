<?php

namespace App\Repositories;

use App\Models\Trip;
use App\Models\User;
use App\Models\Itinerary;
use Illuminate\Support\Collection;
use App\Contracts\ItineraryRepositoryInterface;

class ItineraryRepository implements ItineraryRepositoryInterface
{
    public function allByTrip(int $tripId)
    {
        return Itinerary::where('trip_id', $tripId)->get();
    }

    public function find(int $id): ?Itinerary
    {
        return Itinerary::find($id);
    }

    public function create(array $data): Itinerary
    {
        return Itinerary::create($data);
    }

    public function update(Itinerary $itinerary, array $data): Itinerary
    {
        $itinerary->update($data);
        return $itinerary;
    }

    public function delete(Itinerary $itinerary): bool
    {
        return $itinerary->delete();
    }
}
