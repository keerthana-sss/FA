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
            $request->user(),
            $request->file('file'),
            $request->type
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

    public function list(Trip $trip)
    {
        $files = $this->service->listTripFiles($trip);

        return ApiResponse::setData($files)->response();
    }
}
