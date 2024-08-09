<?php

namespace App\Query\Report\Partner\Hospital\CountForTheYear;

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