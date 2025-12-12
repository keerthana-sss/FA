<?php

namespace App\Services;

use App\Clients\PlacesSearchClient;

class PlacesSearchService
{
    protected PlacesSearchClient $placesClient;
    public function __construct(PlacesSearchClient $placesClient)
    {
        $this->placesClient = $placesClient;
    }
    public function searchPlaces(array $coordinates, string $category, int $radius = 5000)
    {

        $nearByResult = $this->placesClient->searchNearBy($coordinates, $category, $radius);

        //Clean results
        return collect($nearByResult['features'])->map(function ($item) {
            $p = $item['properties'];

            return [
                'name'      => $p['name'] ?? 'Unknown',
                'formatted' => $p['formatted'],
                'lat'       => $p['lat'],
                'lon'       => $p['lon'],
                'distance'  => $p['distance'] ?? null,
            ];
        })->values();
    }
}
