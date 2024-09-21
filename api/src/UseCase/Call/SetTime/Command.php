<?php

namespace App\UseCase\Call\SetTime;

class Command
{
     public function __construct(
         public readonly int $id,
         public ?string $time = null,
     ){}
}