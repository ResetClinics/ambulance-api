<?php

namespace App\Services\Call\CrmCallFetcher;

interface CrmLeadFetcherInterface
{
    public function fetch(string $externalId): ?Lead;

}