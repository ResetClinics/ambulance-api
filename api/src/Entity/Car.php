<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['car:read']],
    denormalizationContext: ['groups' => ['car:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['car:read', 'car:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['car:read', 'car:write'])]
    private ?bool $isCaddy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isIsCaddy(): ?bool
    {
        return $this->isCaddy;
    }

    public function setIsCaddy(?bool $isCaddy): self
    {
        $this->isCaddy = $isCaddy;

        return $this;
    }
}
