<?php

namespace App\Services;

use Exception;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use App\Contracts\TripRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class TripService
{
    protected TripRepositoryInterface $tripRepository;

    public function __construct(TripRepositoryInterface $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }
    public function listAllTrips()
    {
        return $this->tripRepository->listAll();
    }

    public function listForUser(int $userId)
    {
        return $this->tripRepository->listForUser($userId);
    }

    public function createTrip(array $data, int $ownerId): Trip
    {
        $data['owner_id'] = $ownerId;
        $trip =  DB::transaction(fn() => $this->tripRepository->create($data));
        $trip->users()->attach($data['owner_id'], [
            'role' => 'admin',
        ]);
        return $trip;
    }

    public function updateTrip(Trip $trip, array $data, int $userId): bool
    {
        return DB::transaction(fn() => $this->tripRepository->update($trip, $data));
    }

    public function deleteTrip(Trip $trip, int $userId): bool
    {
        return DB::transaction(fn() => $this->tripRepository->delete($trip));
    }

    public function addMember(Trip $trip, int $memberId, string $role)
    {
        DB::transaction(fn() => $trip->users()->syncWithoutDetaching([
            $memberId => ['role' => $role]
        ]));
        return [
            'user_id' => $memberId,
            'role' => $role,
        ];
    }

    public function removeMember(Trip $trip, int $memberId): void
    {
        DB::transaction(fn() => $trip->users()->detach($memberId));
    }
}
