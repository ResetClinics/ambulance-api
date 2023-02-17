<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(
            normalizationContext: ['groups' => ['team:read', 'team:item:get']]
        ),
        new Put(),
    ],
    normalizationContext: ['groups' => ['team:read']],
    denormalizationContext: ['groups' => ['team:write']]
)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['team:read', 'team:write'])]
    private User $administrator;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[Groups(['team:read', 'team:write'])]
    private Collection $doctors;

    public function __construct(User $administrator)
    {
        $this->doctors = new ArrayCollection();
        $this->administrator = $administrator;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdministrator(): User
    {
        return $this->administrator;
    }

    public function setAdministrator(User $administrator): self
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function getDoctors(): array
    {
        return $this->doctors->toArray();
    }

    public function addDoctor(User $doctor): self
    {
        $this->doctors->add($doctor);

        return $this;
    }

    public function removeDoctor(User $doctor): self
    {
        $this->doctors->removeElement($doctor);

        return $this;
    }
}
