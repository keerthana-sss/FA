<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\User;
use App\Models\TripFile;
use App\Enums\TripFileType;
use Illuminate\Support\Facades\Storage;
use App\Contracts\TripFileRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class TripFileService
{
    protected TripFileRepositoryInterface $repository;

    public function __construct(TripFileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function upload(Trip $trip, $request): TripFile
    {
        $path = $request->file('file')->store("trip_files/{$trip->id}", 'public');

        return $this->repository->createFile($trip, $request, $path);
    }

    public function delete(Trip $trip, User $user, $file): bool
    {
        if ($file->trip_id !== $trip->id) {
            throw new AuthorizationException("File does not belong to this trip.");
        }
        if ($file->uploaded_by !== $user->id) {
            abort(403, "You are not allowed to delete this file.");
        }

        if (!Storage::disk('public')->delete($file->path)) {
            throw new \Exception("Failed to delete file from storage.");
        }

        return $this->repository->delete($file);
    }

    public function listTripFiles(Trip $trip)
    {
        return $this->repository->getFilesByTrip($trip->id);
    }

    public function listUserFiles(Trip $trip, $user)
    {
        return $this->repository->getFilesByTrip($trip->id)
            ->where('uploaded_by', $user->id);
    }
}
