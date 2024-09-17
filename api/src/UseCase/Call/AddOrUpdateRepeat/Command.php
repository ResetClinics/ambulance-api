<?php

namespace App\UseCase\Call\AddOrUpdateRepeat;

class Command
{
    public function __construct(
        public readonly string $externalId,
    ) {
    }
}