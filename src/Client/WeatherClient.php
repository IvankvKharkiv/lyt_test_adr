<?php

namespace App\Client;

use App\Action\GetWeatherInfo\WeatherResponseException;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherClient
{
    private HttpClientInterface $client;

    public function __construct(
        string $apiEndPoint,
        private string $apiKey,
        HttpClientInterface $httpClient,
    ) {
        $this->client = $httpClient->withOptions(
            (new HttpOptions())
                ->setBaseUri($apiEndPoint)
                ->toArray()
        );
    }

    public function getWeather(string $city): array
    {
        $response = $this->client->request('GET', "/v1/current.json?key={$this->apiKey}&q={$city}");

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new WeatherResponseException(sprintf('Weather endpoint wrong status code: %s', $response->getStatusCode(false)));
        }

        $weather = json_decode($response->getContent(), true);

        if (isset($weather['error'])) {
            throw new WeatherResponseException($weather['error']['message']);
        }

        return $weather;
    }
}
