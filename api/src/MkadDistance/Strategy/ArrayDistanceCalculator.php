<?php

namespace App\MkadDistance\Strategy;

use InvalidArgumentException;
use App\MkadDistance\Exception\DistanceRequestException;
use App\MkadDistance\Exception\InnerPolygonException;
use App\MkadDistance\Geometry\DistanceBetweenPoints;
use App\MkadDistance\Geometry\Point;

class ArrayDistanceCalculator extends PointDistanceCalculator
{
    /**
     * @param $target
     * @param bool $calcByRoutes
     * @return DistanceBetweenPoints
     * @throws DistanceRequestException
     * @throws InnerPolygonException
     */
    public function calculate($target, bool $calcByRoutes = true): DistanceBetweenPoints
    {
        if (!is_array($target)) {
            throw new InvalidArgumentException(
                'Target param most be array coordinates [float $lat, float $lon]'
            );
        }
        $target = Point::createFromArray($target);
        return parent::calculate($target, $calcByRoutes);
    }
}
