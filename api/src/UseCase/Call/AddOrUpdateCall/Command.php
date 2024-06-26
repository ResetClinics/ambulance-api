<?php

namespace App\UseCase\Call\AddOrUpdateCall;

class Command
{
    public function __construct(
        public readonly string $externalId,
    ) {
    }
}