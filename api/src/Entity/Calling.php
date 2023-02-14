<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CallingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CallingRepository::class)]
#[ApiResource]
class Calling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
