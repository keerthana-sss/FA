<?php

namespace App\Repositories;

use App\Models\TripFile;
use App\Enums\TripFileType;
use App\Contracts\TripFileRepositoryInterface;

class TripFileRepository implements TripFileRepositoryInterface
{
    public function createFile($trip, $request, $path): TripFile
    {
        return TripFile::create([
            'trip_id'     => $trip->id,
            'uploaded_by' => $request->user()->id,
            'path'        => $path,
            'type'        => $request->type ?? TripFileType::Other,
        ]);
    }

    public function delete(TripFile $file): bool
    {
        return $file->delete();
    }

    public function getFilesByTrip(int $tripId)
    {
        return TripFile::forTrip($tripId)->get();
    }
}
