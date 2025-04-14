<?php

namespace App\Action\GetWeatherInfo;

use App\Client\WeatherClient;
use Psr\Log\LoggerInterface;

class WeatherInfo
{
    public function __construct(
        private WeatherClient $weatherClient,
        private LoggerInterface $logger,
    ) {}

    public function getWeather(string $cityName): WeatherResultDto
    {
        $weatherResultDto = $this->toDto($this->weatherClient->getWeather($cityName));

        $this->logger->emergency("Weather result for {$cityName}.", ['weatherData' => $weatherResultDto]);

        return $weatherResultDto;
    }

    private function toDto(array $weatherData)
    {
        if (!$this->weatherDataIsValid($weatherData)) {
            throw new WeatherArrayDataException('Invalid weather data. Cannot create DTO');
        }

        return new WeatherResultDto(
            $weatherData['location']['name'],
            $weatherData['location']['country'],
            $weatherData['current']['temp_c'],
            $weatherData['current']['condition']['text'],
            $weatherData['current']['humidity'],
            $weatherData['current']['wind_kph'],
            new \DateTime($weatherData['current']['last_updated']),
        );
    }

    private function weatherDataIsValid(array $weatherData): bool
    {
        if (
            2 !== count(array_intersect(['location', 'current'], array_keys($weatherData)))
            || 2 !== count(array_intersect(['name', 'country'], array_keys($weatherData['location'])))
            || 5 !== count(array_intersect(['temp_c', 'condition', 'humidity', 'wind_kph', 'last_updated'], array_keys($weatherData['current'])))
        ) {
            return false;
        }

        return true;
    }
}
