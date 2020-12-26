<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTest extends KernelTestCase {
    public function testInValidAPIKey() {
        $APIManager = new \App\Services\APIManager();
        $cityForecastData = $APIManager->getAPIData('12', 'cities', 2);
        $this->assertEquals(401, $cityForecastData);
    }

    public function testValidAPIKey() {
        $APIManager = new \App\Services\APIManager();
        $cityForecastData = $APIManager->getAPIData('361ae81290834e5c84362746200612', 'cities', 2);
        $this->assertArrayHasKey('forecast', $cityForecastData);
    }

    public function testInValidForecast() {
        $APIManager = new \App\Services\APIManager();
        $cityForecastData = $APIManager->getAPIData('12', 'cities', 2);
        $formattedString = $APIManager->getFormattedWeatherCityData($cityForecastData);
        $this->assertFalse($formattedString);
    }

    public function testValidForecast() {
        $APIManager = new \App\Services\APIManager();
        $cityForecastData = $APIManager->getAPIData('361ae81290834e5c84362746200612', 'cities', 2);
        $formattedString = $APIManager->getFormattedWeatherCityData($cityForecastData);
        $this->assertStringContainsString('Processed', $formattedString);
    }
}