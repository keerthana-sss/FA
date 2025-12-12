<?php

namespace App\Services;

use Exception;
use App\Clients\GeoLocationClient;

class GeoLocationService
{
    protected GeoLocationClient $geoClient;


    public function __construct(GeoLocationClient $geoClient)
    {
        $this->geoClient = $geoClient;
    }


    public function getLocationByCity(string $city)
    {
        $city = strtolower($city);
        $locationData = $this->geoClient->fetchCityData($city);
        if (!isset($locationData['latitude']) || !isset($locationData['longitude'])) {
            throw new Exception("API did not return valid coordinates.");
        }
        return [$locationData["longitude"], $locationData["latitude"]];

    }
}
