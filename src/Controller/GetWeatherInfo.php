<?php

namespace App\Controller;

use App\Action\GetWeatherInfo\WeatherInfo;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/get-weather', name: 'app_get_weather_action', methods: ['GET'])]
class GetWeatherInfo extends AbstractController
{
    public function __construct(
        private readonly WeatherInfo  $weatherService,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(#[MapQueryParameter] string $city = ''): Response
    {
        if (empty($city)) {
            return $this->render('weather.html.twig');
        }
        try {
            $weather = $this->weatherService->getWeather($city);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e, 'city' => $city]);

            return $this->render('weather.html.twig', ['error' => 'Something went wrong please contact the administrator.']);
        }

        return $this->render('weather.html.twig', ['weather' => $weather]);
    }
}
