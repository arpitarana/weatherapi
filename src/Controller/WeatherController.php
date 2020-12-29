<?php

namespace App\Controller;

use App\Serializer\TextEncoder;
use App\Serializer\WeatherNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpClient\Exception\ServerException;
use Psr\Log\LoggerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

class WeatherController
{
    private HttpClientInterface $weatherapiClient;

    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $weatherapiClient, LoggerInterface $logger) {
        $this->weatherapiClient = $weatherapiClient;
        $this->logger = $logger;
    }

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
    public function index(HttpClientInterface $musementClient): Response
    {
        // TODO we can use CachingHttpClient if cities are fixed
        try {
            $response = $musementClient->request('GET','/api/v3/cities');
            $cities = array_map(fn($city): string => $city['name'], json_decode($response->getContent(), true));
        } catch (ServerException | ClientExceptionInterface $e) {
            $this->logger->error('Error while fetching cities', [
                'message' => $e->getMessage()
            ]);

            return new JsonResponse(['message' => 'Service not available'], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        
        foreach ($cities as $city) {
            $responses[] = $this->weatherapiClient->request('GET','/v1/forecast.json', [
                'query' => [
                    'q' => $city,
                    'days' => 2
                ]
            ]);
        }

        if (!$data = $this->processResponse($responses)) {
            return new JsonResponse(
                [
                    'errorCode' => Response::HTTP_NOT_FOUND,
                    'message' => 'Weather not available'
                ],
            Response::HTTP_NOT_FOUND
            );
        }

        $serializer = new Serializer(
            [new WeatherNormalizer()],
            [new TextEncoder()]
        );

        return new Response(
            $serializer->serialize($data, 'text'),
            200
        );
    }

    /**
     * @param array $responses
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function processResponse (array $responses): ?array
    {
        $data = [];
        foreach ($this->weatherapiClient->stream($responses) as $response => $chunk) {
            try {
                if (200 !== $response->getStatusCode()) {
                    $response->cancel();
                    continue;
                }
                if ($chunk->isLast()) {
                    $data[] = json_decode($response->getContent(), true);
                }
            } catch (\Exception $e) {
                $this->logger->error(sprintf('Error while fetching %s city weather'), [
                    'message' => $e->getMessage()
                ]);
            }
        }

        return $data;
    }
}
