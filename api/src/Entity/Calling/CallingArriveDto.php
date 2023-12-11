<?php

namespace App\Entity\Calling;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CallingArriveDto
{
    #[Assert\NotNull]
    #[Groups(['calling:read', 'calling:write'])]
    public ?string $fio = null;
    #[Groups(['calling:read', 'calling:write'])]
    public ?string $passport = null;
}