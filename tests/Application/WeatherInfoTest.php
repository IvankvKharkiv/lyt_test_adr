<?php

namespace App\Tests\Application;

use App\Action\GetWeatherInfo\WeatherArrayDataException;
use App\Action\GetWeatherInfo\WeatherInfo;
use App\Action\GetWeatherInfo\WeatherResponseException;
use App\Action\GetWeatherInfo\WeatherResultDto;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\TraceableHttpClient;

final class WeatherInfoTest extends KernelTestCase
{
    #[Test]
    public function itShouldReturnWeatherDto(): void
    {
        // Arrange
        $weatherResponse = [
            'location' => ['name' => 'Madrid', 'country' => 'Spain'],
            'current' => [
                'temp_c' => 12,
                'condition' => ['text' => 'condition'],
                'humidity' => '80%',
                'wind_kph' => 25,
                'last_updated' => '2025-04-13 17:16:18',
            ],
        ];

        $expectedWeatherDto = new WeatherResultDto(
            $weatherResponse['location']['name'],
            $weatherResponse['location']['country'],
            $weatherResponse['current']['temp_c'],
            $weatherResponse['current']['condition']['text'],
            $weatherResponse['current']['humidity'],
            $weatherResponse['current']['wind_kph'],
            new \DateTime($weatherResponse['current']['last_updated']),
        );
        $mockResponseJson = json_encode($weatherResponse, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        self::getContainer()->set('Symfony\Contracts\HttpClient\HttpClientInterface', new TraceableHttpClient($httpClient));

        $weatherService = self::getContainer()->get(WeatherInfo::class);

        // Act
        $weatherDto = $weatherService->getWeather('Madrid');

        // Assert
        $this->assertSame('GET', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weather_test_endpoint.com/v1/current.json?key=weather_test_endpoint_key&q=Madrid', $mockResponse->getRequestUrl());
        self::assertTrue($expectedWeatherDto == $weatherDto);
    }

    #[Test]
    public function itShouldThrowWeatherResponseException(): void
    {
        // Arrange
        $weatherResponse = [
            'error' => ['message' => 'Error'],
        ];

        $mockResponseJson = json_encode($weatherResponse, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        self::getContainer()->set('Symfony\Contracts\HttpClient\HttpClientInterface', new TraceableHttpClient($httpClient));

        $weatherService = self::getContainer()->get(WeatherInfo::class);

        // Act
        $this->expectException(WeatherResponseException::class);
        $weatherService->getWeather('Madrid');

        // Assert
        $this->assertSame('GET', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weather_test_endpoint.com/v1/current.json?key=weather_test_endpoint_key&q=Madrid', $mockResponse->getRequestUrl());
    }

    #[Test]
    public function itShouldThrowWeatherResponseExceptionOnHttpCode(): void
    {
        // Arrange
        $weatherResponse = [
        ];

        $mockResponseJson = json_encode($weatherResponse, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 403,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        self::getContainer()->set('Symfony\Contracts\HttpClient\HttpClientInterface', new TraceableHttpClient($httpClient));

        $weatherService = self::getContainer()->get(WeatherInfo::class);

        // Act
        $this->expectException(WeatherResponseException::class);
        $weatherService->getWeather('Madrid');

        // Assert
        $this->assertSame('GET', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weather_test_endpoint.com/v1/current.json?key=weather_test_endpoint_key&q=Madrid', $mockResponse->getRequestUrl());
    }

    #[Test]
    public function itShouldThrowWeatherArrayDataException(): void
    {
        // Arrange
        $weatherResponse = [
        ];

        $mockResponseJson = json_encode($weatherResponse, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        self::getContainer()->set('Symfony\Contracts\HttpClient\HttpClientInterface', new TraceableHttpClient($httpClient));

        $weatherService = self::getContainer()->get(WeatherInfo::class);

        // Act
        $this->expectException(WeatherArrayDataException::class);
        $weatherService->getWeather('Madrid');

        // Assert
        $this->assertSame('GET', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weather_test_endpoint.com/v1/current.json?key=weather_test_endpoint_key&q=Madrid', $mockResponse->getRequestUrl());
    }
}
