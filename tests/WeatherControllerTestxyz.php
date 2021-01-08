<?php

namespace App\Tests;           

use App\Services\WeatherManager;
use App\Services\MusementCityManager;
use PHPUnit\Framework\TestCase;
use App\Controller\WeatherController;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Psr\Log\LoggerInterface;   
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
    
class WeatherControllerTest extends KernelTestCase
{ 
    // private $weatherManager;   
    // private $musementCityManager;   
  
    // /**
    //  * {@inheritDoc}           
    //  */                        
    // protected function setUp()
    // {
    //     $kernel = self::bootKernel();   

    //     $container = $kernel->getContainer();
    //     $this->weatherManager = $container->get(WeatherManager::class);
    //     $this->musementCityManager = $container->get(MusementCityManager::class);
    // }

    // public function testCities()    
    // {

    //     $logger = $this->createMock(LoggerInterface::class);
    //     $weatherController = new WeatherController();

    //     $response = $weatherController->index($this->weatherManager, $this->musementCityManager);
    //     $this->assertSame('Processed city Milan | Sunny ', $response->getContent());

    //     $this->assertCount(1, $products);
    // }
}
