<?php

namespace App\Services\Call\CrmContactFetcher;

use App\UseCase\Call\SendFromCrm\Contact;

interface CrmContactFetcherInterface
{
    public function fetch(int $contactId): Contact;
}