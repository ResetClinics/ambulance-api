<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BaseRepository::class)]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: ['groups' => ['base:read']],
    denormalizationContext: ['groups' => ['base:write']],
    openapi: false,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
class Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['base:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['base:read', 'base:write'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Groups(['base:read', 'base:write'])]
    #[ORM\Column(length: 16, nullable: true)]
    private ?string $lon = null;

    #[Groups(['base:read', 'base:write'])]
    #[ORM\Column(length: 16, nullable: true)]
    private ?string $lat = null;
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

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): void
    {
        $this->lat = $lat;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(?string $lon): void
    {
        $this->lon = $lon;
    }
}
