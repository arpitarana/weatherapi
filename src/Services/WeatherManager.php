<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class WeatherManager implements WeatherInterface
{
	private HttpClientInterface $weatherapiClient;

	private LoggerInterface $logger;

	public function __construct(HttpClientInterface $weatherapiClient, LoggerInterface $logger) {
        $this->weatherapiClient = $weatherapiClient;
        $this->logger = $logger;
    }

	public function getForecastByCities(array $cities): ?array
	{
		foreach ($cities as $city) {
            $responses[] = $this->weatherapiClient->request('GET','/v1/forecast.json', [
                'query' => [
                    'q' => $city,
                    'days' => 2
                ]
            ]);
        }

		return $this->processResponse($responses);
	}

    private function processResponse (array $responses): ?array
    {
        $data = [];
        foreach ($this->weatherapiClient->stream($responses) as $response => $chunk) {
            try {
                if (Response::HTTP_OK !== $response->getStatusCode()) {
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