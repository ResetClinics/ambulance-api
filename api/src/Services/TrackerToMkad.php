<?php

namespace App\Services;


use App\MkadDistance\Distance;
use Exception;

class TrackerToMkad
{
    public function getDistance(float $lat, float $lon): int
    {
        try {
            $distance = Distance::createMoscowMkadCalculator(
                [$lat, $lon]
            )->calculate();

            return (int)$distance;
        }catch (Exception $e){
            return 0;
        }
    }
}