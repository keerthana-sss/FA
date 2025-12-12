<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripFile;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\TripFileService;
use App\Http\Requests\TripFileUploadRequest;

class TripFileController extends Controller
{
    protected TripFileService $service;

    public function __construct(TripFileService $service)
    {
        $this->service = $service;
    }

    public function upload(TripFileUploadRequest $request, Trip $trip)
    {
        $file = $this->service->upload(
            $trip,
            $request
        );

        return ApiResponse::setMessage('File uploaded successfully')
            ->setData($file)
            ->response();
    }

    public function delete(Trip $trip, TripFile $file)
    {
        $this->service->delete($trip, auth()->user(), $file);

        return ApiResponse::setMessage('File deleted successfully')->response();
    }

    public function listAllFiles(Trip $trip)
    {
        $files = $this->service->listTripFiles($trip);
        if ($files->isEmpty()) {
            return ApiResponse::setMessage('No files for this trip')
                ->setData([])
                ->response();
        }

        return ApiResponse::setData($files)->response();
    }

    public function listUserFiles(Trip $trip)
    {
        $files = $this->service->listUserFiles($trip, auth()->user());
        if ($files->isEmpty()) {
            return ApiResponse::setMessage('No files for this User')
                ->setData([])
                ->response();
        }

        return ApiResponse::setData($files)->response();
    }
}
