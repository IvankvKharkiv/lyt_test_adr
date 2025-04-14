<?php

namespace App\Tests\Application;

use App\Action\GetWeatherInfo\WeatherInfo;
use App\Action\GetWeatherInfo\WeatherResponseException;
use App\Action\GetWeatherInfo\WeatherResultDto;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function PHPUnit\Framework\once;

final class GetWeatherInfoTest extends WebTestCase
{
    #[Test]
    public function itShouldShowWeatherForMadrid(): void
    {
        // Arrange
        $client = static::createClient();
        $weatherServiceMock = $this->createMock(WeatherInfo::class);
        $weatherServiceMock->expects(once())
            ->method('getWeather')
            ->willReturn(new WeatherResultDto(
                'Madrid',
                'Spain',
                12,
                'cond',
                '20%',
                23,
                new \DateTime('2020-01-01T00:00:00'),
            ));
        self::getContainer()->set('App\Action\GetWeatherInfo\WeatherInfo', $weatherServiceMock);

        // Act
        $crawler = $client->request('GET', 'https://nginx:8080/get-weather?city=Madrid');

        // Assert
        self::assertStringContainsString('You selected: Madrid', $crawler->html());
    }

    #[Test]
    public function itShouldShowError(): void
    {
        // Arrange
        $client = static::createClient();

        $weatherServiceMock = $this->createMock(WeatherInfo::class);
        $weatherServiceMock->expects(once())
            ->method('getWeather')
            ->willThrowException(new WeatherResponseException());

        self::getContainer()->set('App\Action\GetWeatherInfo\WeatherInfo', $weatherServiceMock);

        // Act
        $crawler = $client->request('GET', 'https://nginx:8080/get-weather?city=ErrorCity');

        // Assert
        self::assertStringContainsString('Something went wrong please contact the administrator.', $crawler->html());
    }
}
