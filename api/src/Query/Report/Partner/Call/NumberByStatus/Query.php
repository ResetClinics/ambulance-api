<?php

namespace App\Query\Report\Partner\Call\NumberByStatus;

class Query
{
    public function __construct(
        public readonly int $partnerId,
    )
    {
    }
}