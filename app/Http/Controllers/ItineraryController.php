<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\ItineraryService;
use App\Http\Requests\CreateItineraryRequest;
use App\Http\Requests\UpdateItineraryRequest;

class ItineraryController extends Controller
{
    protected ItineraryService $service;

    public function __construct(ItineraryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, Trip $trip)
    {
        $itineraries = $this->service->listItineraries($trip, $request->user());
        return ApiResponse::setData($itineraries)
                          ->setMessage('Itineraries fetched successfully')
                          ->response();
    }

    public function store(CreateItineraryRequest $request, Trip $trip)
    {
        // $data = $request->validate([
        //     'day_number'  => 'required|integer|min:1',
        //     'title'       => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'start_time'  => 'nullable|date_format:H:i',
        //     'end_time'    => 'nullable|date_format:H:i',
        // ]);

        $itinerary = $this->service->createItinerary($request->all(), $trip, $request->user());

        return ApiResponse::setData($itinerary)
                          ->setMessage('Itinerary created successfully')
                          ->response(201);
    }

    public function update(UpdateItineraryRequest $request, Trip $trip, Itinerary $itinerary)
    {
        // $data = $request->validate([
        //     'day_number'  => 'sometimes|integer|min:1',
        //     'title'       => 'sometimes|string|max:255',
        //     'description' => 'nullable|string',
        //     'start_time'  => 'nullable|date_format:H:i',
        //     'end_time'    => 'nullable|date_format:H:i',
        //     'location' => 'nullable|string|max:255',
        // ]);

        $itinerary = $this->service->updateItinerary($itinerary, $request->all(), $request->user());

        return ApiResponse::setData($itinerary)
                          ->setMessage('Itinerary updated successfully')
                          ->response();
    }

    public function destroy(Trip $trip, Itinerary $itinerary, Request $request)
    {
        $this->service->deleteItinerary($itinerary, $request->user());

        return ApiResponse::setMessage('Itinerary deleted successfully')->response();
    }
}
