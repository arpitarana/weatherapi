<?php

namespace App\Controller;

use App\Services\APIManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/", name="musement_api_cities", methods={"GET"})
     */
    public function index(APIManager $APIManager): Response
    {
        $apiKey = $this->getParameter('api_key');

        // Note for $cities variable here I have to use musement get cities but those are not working so I took static city array.
        $cities = ['Paris', 'London'];
        $formattedString = '';
        foreach ($cities as $city) {
            $cityForecastData = $APIManager->getAPIData($apiKey, $city, 2);
            $formattedString .= $APIManager->getFormattedWeatherCityData($cityForecastData)."<br>";
        }

        return new Response($formattedString);
    }
}
