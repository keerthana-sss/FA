<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\User;
use App\Models\TripFile;
use App\Enums\TripFileType;
use Illuminate\Support\Facades\Storage;
use App\Contracts\TripFileRepositoryInterface;
use Illuminate\Validation\ValidationException;

class TripFileService
{
    protected TripFileRepositoryInterface $repository;

    public function __construct(TripFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function upload(Trip $trip, $actor, $file, $type): TripFile
    {
        if (!$trip->users->contains($actor->id)) {
            throw ValidationException::withMessages([
                'file' => ['You are not a member of this trip.']
            ]);
        }

        $path = $file->store("trip_files/{$trip->id}", 'public');

        return TripFile::create([
            'trip_id'     => $trip->id,
            'uploaded_by' => $actor->id,
            'path'        => $path,
            'type'        => $type,
        ]);
    }

    public function delete(Trip $trip, User $user, $file): bool
    {
        if ($file->trip_id !== $trip->id) {
            abort(403, "File does not belong to this trip.");
        }

        if (!$trip->users()->where('user_id', $user->id)->exists()) {
            abort(403, "Not allowed.");
        }

        Storage::disk('public')->delete($file->path);

        return $this->repository->delete($file);
    }

    public function listTripFiles(Trip $trip)
    {
        // if (!$trip->members()->where('user_id', $user->id)->exists()) {
        //     abort(403, "Unauthorized.");
        // }

        return $this->repository->getByTrip($trip->id);
    }
}
