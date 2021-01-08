<?php

namespace App\Controller;

use App\Serializer\TextEncoder;
use App\Serializer\WeatherNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use App\Services\WeatherManager;
use App\Services\MusementCityManager;

class WeatherController
{
    /**
     * @OA\Response(
     *     response=201,
     *     description="Returns empty body with 201 status code"
     * )
     *
     * @Security(name="Bearer")
     * @OA\Tag(name="Weather")
     * @Route("/weather", name="weather_index", methods={"GET"})
     */
    public function index(WeatherManager $weatherManager, MusementCityManager $musementCityManager, SerializerInterface $weatherSerializer)
    {
        $cities = $musementCityManager->getAllCities();
        if(!$cities) {
            return new JsonResponse(
                [
                    'errorCode' => Response::HTTP_NOT_FOUND,
                    'message' => 'Cities not available'
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $weatherManager->getForecastByCities($cities);
        if (!$data) {
            return new JsonResponse(
                [
                    'errorCode' => Response::HTTP_NOT_FOUND,
                    'message' => 'Weather not available'
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return new Response(
            $weatherSerializer->serialize($data, 'text'),
            Response::HTTP_OK
        );
    }
}