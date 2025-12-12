<?php

namespace App\Clients;

use App\Response\ApiResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;


class GeoLocationClient
{
    public function fetchCityData(string $city): array
    {
        $response = Http::withoutVerifying()->get(config('services.apiUrls.geocoding_url'), [
            'name' => $city,
            'count' => 1,
        ]);

        if ($response->failed()) {
            throw new HttpResponseException(
                ApiResponse::setMessage('Failed to fetch location data')
                    ->response(500)
            );
        }
        $results = $response->json('results', []);

        if (empty($results)) {
            throw new HttpResponseException(
                ApiResponse::setMessage('No coordinates found for the specified city')
                    ->response(404)
            );
        }

        return $results[0];
    }
}
