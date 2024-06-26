<?php

namespace App\UseCase\Call\AssignTeamToCall;

class Command
{
    public function __construct(
        public readonly string $externalId,
    ) {
    }
}