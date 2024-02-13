<?php

namespace App\Entity\Calling;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Entity\Service\Service;
use App\Repository\Calling\RowRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RowRepository::class)]
#[ORM\Table(name: 'calling_rows')]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['calling-row:read',]]),
        new Put(normalizationContext: ['groups' => ['calling-row:write',]]),
    ],
)]
class Row
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rows')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?Service $service = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read', 'calling-row:write'])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'rows')]
    private ?Calling $calling = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?int $plannedPrice = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?\DateTimeImmutable $plannedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read', 'calling-row:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?int $partnerReward = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?int $coastPrice = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write', 'calling-row:read'])]
    private ?int $percent = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCalling(): ?Calling
    {
        return $this->calling;
    }

    public function setCalling(?Calling $calling): self
    {
        $this->calling = $calling;

        return $this;
    }

    public function getPlannedPrice(): ?int
    {
        return $this->plannedPrice;
    }

    public function setPlannedPrice(?int $plannedPrice): self
    {
        $this->plannedPrice = $plannedPrice;

        return $this;
    }

    public function getPlannedAt(): ?\DateTimeImmutable
    {
        return $this->plannedAt;
    }

    public function setPlannedAt(?\DateTimeImmutable $plannedAt): self
    {
        $this->plannedAt = $plannedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPartnerReward(): ?int
    {
        return $this->partnerReward;
    }

    public function setPartnerReward(?int $partnerReward): self
    {
        $this->partnerReward = $partnerReward;

        return $this;
    }

    public function getCoastPrice(): ?int
    {
        return $this->coastPrice;
    }

    public function setCoastPrice(?int $coastPrice): void
    {
        $this->coastPrice = $coastPrice;
    }

    public function getPercent(): ?int
    {
        return $this->percent;
    }

    public function setPercent(?int $percent): void
    {
        $this->percent = $percent;
    }
}
