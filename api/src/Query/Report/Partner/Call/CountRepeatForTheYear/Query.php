<?php

namespace App\Query\Report\Partner\Call\CountRepeatForTheYear;

class Query
{
    public function __construct(
        public readonly int $partnerId,
        public readonly string $startDate,
        public readonly string $endDate,
    )
    {
    }
}