<?php

namespace App\Services;

interface WeatherInterface
{
	public function getForecastByCities(array $cities): ?array;
}