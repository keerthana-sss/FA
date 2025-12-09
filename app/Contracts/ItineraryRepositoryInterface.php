<?php

namespace App\Contracts;

use App\Models\Trip;
use App\Models\Itinerary;
use App\Models\User;
use Illuminate\Support\Collection;

interface ItineraryRepositoryInterface
{
    public function allByTrip(int $tripId);
    public function find(int $id): ?Itinerary;
    public function create(array $data): Itinerary;
    public function update(Itinerary $itinerary, array $data): Itinerary;
    public function delete(Itinerary $itinerary): bool;
}
