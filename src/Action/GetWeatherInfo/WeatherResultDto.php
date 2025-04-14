<?php

namespace App\Action\GetWeatherInfo;

use Symfony\Component\DependencyInjection\Attribute\When;

#[When(false)]
class WeatherResultDto
{
    public function __construct(
        public readonly string $city,
        public readonly string $country,
        public readonly int $temperature,
        public readonly string $condition,
        public readonly string $humidity,
        public readonly int $wind_speed,
        public readonly \DateTimeInterface $last_updated,
    ) {}
}
