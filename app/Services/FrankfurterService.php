<?php

namespace App\Services;

use Illuminate\Http\Client\Factory;
use RuntimeException;

class FrankfurterService
{
    public function __construct(private readonly Factory $http) {}

    /**
     * @return array{base: string, quote: string, rate: float, date: string, convertedAmount: float}
     */
    public function convert(float $amount, string $sourceCurrency, string $targetCurrency): array
    {
        if ($sourceCurrency === $targetCurrency) {
            return [
                'base' => $sourceCurrency,
                'quote' => $targetCurrency,
                'rate' => 1.0,
                'date' => now()->toDateString(),
                'convertedAmount' => $amount,
            ];
        }

        $baseUrl = rtrim((string) config('services.frankfurter.base_url'), '/');
        $url = $baseUrl.'/rate/'.strtolower($sourceCurrency).'/'.strtolower($targetCurrency);

        $response = $this->http
            ->connectTimeout((int) config('services.frankfurter.connect_timeout', 3))
            ->timeout((int) config('services.frankfurter.timeout', 10))
            ->get($url)
            ->throw();

        $payload = $response->json();
        $rate = data_get($payload, 'rate');
        $date = data_get($payload, 'date');
        $base = data_get($payload, 'base');
        $quote = data_get($payload, 'quote');

        if (! is_numeric($rate) || ! is_string($date) || ! is_string($base) || ! is_string($quote)) {
            throw new RuntimeException('The exchange rate response was not valid.');
        }

        return [
            'base' => strtoupper($base),
            'quote' => strtoupper($quote),
            'rate' => (float) $rate,
            'date' => $date,
            'convertedAmount' => $amount * (float) $rate,
        ];
    }
}
