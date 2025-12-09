<?php

namespace App\Contracts;

use App\Models\TripFile;

interface TripFileRepositoryInterface
{
    public function store(array $data): TripFile;
    public function delete(TripFile $file): bool;
    public function getByTrip(int $tripId);
}
