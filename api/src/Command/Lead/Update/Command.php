<?php

namespace App\Command\Lead\Update;

use App\Dto\Amo\Lead\Lead;

class Command
{
    private Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}