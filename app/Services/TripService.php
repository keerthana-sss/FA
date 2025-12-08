<?php

namespace App\Services;

use App\Models\Trip;
use App\Contracts\TripRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TripService
{
    protected TripRepositoryInterface $tripRepository;

    public function __construct(TripRepositoryInterface $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }

    public function listForUser(int $userId)
    {
        return $this->tripRepository->listForUser($userId);
    }

    public function createTrip(array $data, int $ownerId): Trip
    {
        $data['owner_id'] = $ownerId;
        return DB::transaction(fn() => $this->tripRepository->create($data));
    }

    public function updateTrip(Trip $trip, array $data, int $userId): bool
    {
        if ($trip->owner_id !== $userId) {
            throw new \Exception('Unauthorized');
        }

        return DB::transaction(fn() => $this->tripRepository->update($trip, $data));
    }

    public function deleteTrip(Trip $trip, int $userId): bool
    {
        if ($trip->owner_id !== $userId) {
            throw new \Exception('Unauthorized');
        }

        return DB::transaction(fn() => $this->tripRepository->delete($trip));
    }

    public function addMember(Trip $trip, int $memberId, string $role, int $actorId): void
    {
        if ($trip->owner_id !== $actorId) {
            throw new \Exception('Unauthorized');
        }

        DB::transaction(fn() => $trip->users()->syncWithoutDetaching([
            $memberId => ['role' => $role]
        ]));
    }

    public function removeMember(Trip $trip, int $memberId, int $actorId): void
    {
        if ($trip->owner_id !== $actorId) {
            throw new \Exception('Unauthorized');
        }

        DB::transaction(fn() => $trip->users()->detach($memberId));
    }
}
