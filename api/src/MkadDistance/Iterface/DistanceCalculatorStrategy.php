<?php

namespace App\MkadDistance\Iterface;

use App\MkadDistance\Geometry\DistanceBetweenPoints;

interface DistanceCalculatorStrategy
{
    /**
     * @param $target
     * @param bool $calcByRoutes
     * @return DistanceBetweenPoints
     */
    public function calculate($target, bool $calcByRoutes = true): DistanceBetweenPoints;
}
