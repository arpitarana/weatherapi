<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class WeatherNormalizer implements ContextAwareNormalizerInterface
{
    public function normalize($data, $format = null, $context = [])
    {
        $weatherCitites = [];
        foreach($data as $location) {
            $weatherString = '';
            foreach($location['forecast']['forecastday'] as $index => $data) {
                $weatherString .= $data['day']['condition']['text']. ' - ';
            }
            $weatherCitites[] =  sprintf("Processed city %s | %s ", $location['location']['name'], rtrim($weatherString, ' - '));
        }
 
        return $weatherCitites;
    }

    public function supportsNormalization($data, $format = null, $context = [])
    {
        return $format == 'text';
    }
}
