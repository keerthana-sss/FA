<?php

namespace App\Repositories;

use App\Contracts\TripFileRepositoryInterface;
use App\Models\TripFile;

class TripFileRepository implements TripFileRepositoryInterface
{
    public function store(array $data): TripFile
    {
        return TripFile::create($data);
    }

    public function delete(TripFile $file): bool
    {
        return $file->delete();
    }

    public function getByTrip(int $tripId)
    {
        return TripFile::forTrip($tripId)->get();
    }
}
