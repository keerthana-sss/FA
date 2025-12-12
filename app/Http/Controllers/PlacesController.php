<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\GeoLocationService;
use App\Services\PlacesSearchService;
use App\Http\Requests\PlaceSearchRequest;

class PlacesController extends Controller
{
    protected PlacesSearchService $service;
    protected GeoLocationService $geoLocationService;

    public function __construct(PlacesSearchService $service, GeoLocationService $geoLocationService)
    {
        $this->service = $service;
        $this->geoLocationService = $geoLocationService;
    }

    public function search(PlaceSearchRequest $request)
    {
        //Fetch coordinates from city name
        $coordinates = $this->geoLocationService->getLocationByCity($request->city);

        //Search places near by
        $results = $this->service->searchPlaces(
            $coordinates,
            $request->category,
            $request->radius ?? 5000
        );

        return ApiResponse::setData($results)
            ->setMessage('Places fetched successfully')
            ->response();

    }
}
