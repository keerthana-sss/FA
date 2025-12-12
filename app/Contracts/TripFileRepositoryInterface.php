<?php

namespace App\Contracts;

use App\Models\TripFile;

interface TripFileRepositoryInterface
{
    public function createFile($trip, $request, $path): TripFile;
    public function delete(TripFile $file): bool;
    public function getFilesByTrip(int $tripId);
}
