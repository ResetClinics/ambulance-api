<?php

namespace App\UseCase\Call\SetResponsiblePerson;

class Command
{
    public function __construct(
        public readonly string $externalId,
    ) {
    }
}