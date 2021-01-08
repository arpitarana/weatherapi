<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class MusementCityManager
{
	private $musementClient;

	private $logger;

	public function __construct(HttpClientInterface $musementClient, LoggerInterface $logger) {
        $this->musementClient = $musementClient;
        $this->logger = $logger;
    }

	public function getAllCities(): ?array
	{
        $cities = [];
		// TODO we can use CachingHttpClient if cities are fixed
        try {
            $response = $this->musementClient->request('GET','/api/v3/cities');
            $cities = array_map(fn($city): string => $city['name'], json_decode($response->getContent(), true));
            return $cities;
        } catch (ServerException | ClientExceptionInterface $e) {
            $this->logger->error('Error while fetching cities', [
                'message' => $e->getMessage()
            ]);
        }

        return $cities;
	}
}