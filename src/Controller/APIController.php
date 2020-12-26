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

        $cities = $APIManager->getCities();
        if($cities) {
            $formattedString = '';
            foreach ($cities as $city) {
                $cityForecastData = $APIManager->getAPIData($apiKey, $city, 2);
                $formattedString .= $APIManager->getFormattedWeatherCityData($cityForecastData) . "<br>";
            }

            return new Response($formattedString);
        }
        else {
            return new Response("No data proper!");
        }
    }
}
