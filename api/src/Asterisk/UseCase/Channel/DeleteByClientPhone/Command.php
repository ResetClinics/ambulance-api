<?php

namespace App\Asterisk\UseCase\Channel\DeleteByClientPhone;

use Symfony\Component\Validator\Constraints as Assert;
readonly class Command
{
    public function __construct(
        #[Assert\NotNull]
        public ?string $clientPhone = null,
    )
    {}
}