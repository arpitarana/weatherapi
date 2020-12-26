<?php

namespace App\Services;

use PHPUnit\Runner\Exception;
use Symfony\Component\HttpClient\HttpClient;

class APIManager
{
    public function getAPIData($apiKey, $q, $days)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://api.weatherapi.com/v1/forecast.json', [
            'query' => [
                'key' => $apiKey,
                'q' => $q,
                'days' => $days
            ]
        ]);

        if($response->getStatusCode() == 200) {
            $cityForecastData = json_decode($response->getContent(), true);
            return $cityForecastData;
        }
        else {
            return $response->getStatusCode();
        }
    }

    public function getFormattedWeatherCityData($cityForecastData)
    {
        $weatherString = '';
        if(isset($cityForecastData['forecast'])) {
            foreach($cityForecastData['forecast']['forecastday'] as $index => $data) {
                $weatherString .= $data['day']['condition']['text']. ' - ';
            }
            return "Processed city ".$cityForecastData['location']['name'].' | '.rtrim($weatherString, ' - ');
        }

        return false;
    }
}