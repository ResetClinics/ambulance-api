<?php

namespace App\Services\Call\CrmContactFetcher;

use App\UseCase\Call\SendFromCrm\Contact;

interface CrmContactFetcherInterface
{
    public function fetch(int $leadId): Contact;
}