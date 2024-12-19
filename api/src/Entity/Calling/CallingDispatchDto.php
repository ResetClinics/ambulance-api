<?php

namespace App\Entity\Calling;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CallingDispatchDto
{
    #[Groups(['calling:write'])]
    public ?string $arrivalDateTime = null;
}