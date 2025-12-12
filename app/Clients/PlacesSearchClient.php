<?php

namespace App\Clients;

use App\Response\ApiResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlacesSearchClient
{
    public function searchNearBy($coordinates, $category, $radius)
    {
        $apiKey = config('services.apiKeys.geoapify_key');
        $apiUrl = config('services.apiUrls.geoapify_url');
        $lon = $coordinates[0];
        $lat = $coordinates[1];
        $response = Http::withoutVerifying()->get($apiUrl,  [
                'categories' => $category,
                'filter'     => "circle:$lon,$lat,$radius",
                'bias'       => "proximity:$lon,$lat",
                'limit'      => 20,
                'apiKey'     => $apiKey
            ]);

        if ($response->failed()) {
            throw new HttpResponseException(
                ApiResponse::setMessage('Failed to fetch data from geoapify API')
                    ->response(500)
            );
        }

        $results = $response->json();

        return $results;
    }
}