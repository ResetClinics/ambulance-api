<?php

namespace App\Entity\Calling;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CallingArriveDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Groups(['calling:write'])]
        public readonly ?string $fio = null,
        #[Groups(['calling:write'])]
        public readonly ?string $passport = null,
        #[Assert\NotNull]
        #[Groups(['calling:write'])]
        public readonly ?string $age = null
    )
    {}
}