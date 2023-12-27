<?php

namespace App\Query\PartnerReward;

class Query
{
    public function __construct(
        public readonly int $partnerId,
        public readonly  int $serviceId,
        public readonly int $repeat,
        public readonly int $distance
    )
    {
    }
}