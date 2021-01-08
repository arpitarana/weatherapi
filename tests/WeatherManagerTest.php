<?php
                               
namespace App\Tests;           

use PHPUnit\Framework\TestCase;
use App\Services\WeatherManager;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Psr\Log\LoggerInterface;   

class WeatherManagerTest extends TestCase
{   
    public function testWithValidCity() 
    {
        $logger = $this->createMock(LoggerInterface::class);
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

        $weatherManager = new WeatherManager($weatherApiClient, $logger);
        $data = $weatherManager->getForecastByCities(['Milan']);
        $this->assertNotEmpty($data);   
    }
}