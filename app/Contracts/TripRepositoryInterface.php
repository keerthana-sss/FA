<?php

namespace App\Contracts;

use App\Models\Trip;

interface TripRepositoryInterface
{
    public function listAll();
    public function create(array $data): Trip;

    // public function find(int $id): ?Trip;

    public function update(Trip $trip, array $data): bool;

    public function delete(Trip $trip): bool;

    // public function addUserToTrip(Trip $trip, int $userId, string $role): void;

    // public function updateUserRole(Trip $trip, int $userId, string $role): void;

    // public function removeUserFromTrip(Trip $trip, int $userId): void;

    public function listForUser(int $userId);
}
