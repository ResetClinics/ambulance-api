<?php

declare(strict_types=1);

namespace App\UseCase\Call\AddForPartner;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public int $partnerId;
    #[Assert\NotBlank]
    public ?string $phone = null;
    #[Assert\NotBlank]
    public ?string $description = null;

    public function __construct(int $partnerId)
    {
        $this->partnerId = $partnerId;
    }
}
