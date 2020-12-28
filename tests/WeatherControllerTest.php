<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Controller\WeatherController;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Psr\Log\LoggerInterface;

class WeatherControllerTest extends TestCase
{
    public function testSuccess()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $citiesClient = new MockHttpClient(function ($method, $url, $options) {
            $cities = [
                ['name' => 'Milan']
            ];
            return new MockResponse(json_encode($cities), ['Content-Type' => 'application/json']);
        }, 'https://example.com');
        $weatherApiClient = new MockHttpClient(function ($method, $url, $options) {
            $weatherData = [
                'location' => ['name' => 'Milan'],
                'forecast' => [
                    'forecastday' => [
                        ['day' => ['condition' => ['text' => 'Sunny']]]
                    ]
                ]
            ];
            return new MockResponse(json_encode($weatherData), ['Content-Type' => 'application/json']);
        }, 'https://example.com');

        $controller = new WeatherController($weatherApiClient, $logger);
        $response = $controller->index($citiesClient);
        $this->assertSame('Processed city Milan | Sunny ', $response->getContent());
    }

    public function testExpectInternalServerError()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $citiesClient = new MockHttpClient(function ($method, $url, $options) {
            $cities = [
                ['name' => 'Milan']
            ];
            return new MockResponse(json_encode($cities), ['Content-Type' => 'application/json', 'http_code' => 503]);
        }, 'https://example.com');
        $weatherApiClient = new MockHttpClient(function ($method, $url, $options) {
            $weatherData = [
                'location' => ['name' => 'Milan'],
                'forecast' => [
                    'forecastday' => [
                        ['day' => ['condition' => ['text' => 'Sunny']]]
                    ]
                ]
            ];
            return new MockResponse(json_encode($weatherData), ['Content-Type' => 'application/json']);
        }, 'https://example.com');

        $controller = new WeatherController($weatherApiClient, $logger);
        $response = $controller->index($citiesClient);
        $this->assertSame(503, $response->getStatusCode());
    }

    public function testExpectWeatherInternalServerError()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $citiesClient = new MockHttpClient(function ($method, $url, $options) {
            $cities = [
                ['name' => 'Milan']
            ];
            return new MockResponse(json_encode($cities), ['Content-Type' => 'application/json']);
        }, 'https://example.com');
        $weatherApiClient = new MockHttpClient(function ($method, $url, $options) {
            $weatherData = [];
            return new MockResponse(json_encode($weatherData), ['Content-Type' => 'application/json', 'http_code' => 503]);
        }, 'https://example.com');

        $controller = new WeatherController($weatherApiClient, $logger);
        $response = $controller->index($citiesClient);
        $this->assertSame(404, $response->getStatusCode());
    }
}
