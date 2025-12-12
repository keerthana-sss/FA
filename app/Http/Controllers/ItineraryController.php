<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\ItineraryService;
use App\Http\Requests\Itinerary\CreateItineraryRequest;
use App\Http\Requests\Itinerary\UpdateItineraryRequest;

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
        if ($itineraries->isEmpty()) {
            return ApiResponse::setMessage('No itineraries yet created')
                ->response();
        }
        return ApiResponse::setMessage('Itineraries fetched successfully')
            ->setData($itineraries)
            ->response();
    }

    public function store(CreateItineraryRequest $request, Trip $trip)
    {
        $itinerary = $this->service->createItinerary($request->all(), $trip, $request->user());

        return ApiResponse::setData($itinerary)
            ->setMessage('Itinerary created successfully')
            ->response(201);
    }

    public function update(UpdateItineraryRequest $request, Trip $trip, Itinerary $itinerary)
    {
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
