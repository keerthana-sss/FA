<?php

namespace App\Repositories;

use App\Models\Trip;
use App\Contracts\TripRepositoryInterface;

class TripRepository implements TripRepositoryInterface
{
    public function listAll()
    {
        return Trip::with('owner')->get();
    }
    public function create(array $data): Trip
    {
        return Trip::create($data);
    }

    public function update(Trip $trip, array $data): bool
    {
        return $trip->update($data);
    }

    public function delete(Trip $trip): bool
    {
        return $trip->delete();
    }

    public function findById(int $id): ?Trip
    {
        return Trip::find($id);
    }

    public function listForUser(int $userId): iterable
    {
        return Trip::where('owner_id', $userId)
            ->orWhereHas('users', fn($q) => $q->where('user_id', $userId))
            ->get();
    }
}
