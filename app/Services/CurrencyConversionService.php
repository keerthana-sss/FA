<?php

namespace App\Services;

use App\Clients\ExchangeRateClient;

class CurrencyConversionService
{
    protected ExchangeRateClient $client;

    public function __construct(ExchangeRateClient $client)
    {
        $this->client = $client;
    }

    /**
     * Convert amount from one currency to another.
     */
    public function convert(float $amount, string $from, string $to)
    {
        // If both currencies same, no need to hit API
        if (strtoupper($from) === strtoupper($to)) {
            return round($amount, 2);
        }

        $response = $this->client->convert($from, $to, $amount);

        if (!$response || empty($response['success'])) {
            return null; // ❗ fail clearly
        }

        return round($response['result'], 2);
    }
}
