<?php

namespace App\Entity\Calling;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Hospital\Clinic;
use App\Entity\Service\Service;
use App\Repository\Calling\RowRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RowRepository::class)]
#[ORM\Table(name: 'calling_rows')]
#[ApiResource(
    openapi: false
)]
class Row
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rows')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?Service $service = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'rows')]
    private ?Calling $calling = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $plannedPrice = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?\DateTimeImmutable $plannedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $partnerReward = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $coastPrice = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $percent = null;

    #[ORM\ManyToOne]
    #[Groups(['calling:read', 'calling:write'])]
    private ?Clinic $clinic = null;


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

    /**
     * Услуга является Стационаром
     */
    public function isStationary(): bool
    {
        return$this->getService()?->getType() === 'hospital';
    }

    /**
     * Услуга является кодированием
     */

    public function isCoding(): bool
    {
        return $this->getService()?->getCategory()?->getId() === 3;
    }

    /**
     * Услуга является терапией
     */

    public function isTherapy(): bool
    {
        return $this->getService()?->getCategory()?->getId() === 1;
    }

    /**
     * Услуга является госпитализацией (доставка в больницу)
     */
    public function isHospital(): bool
    {
        return $this->getService()?->getId() === 12;
    }

    public function getClinic(): ?Clinic
    {
        return $this->clinic;
    }

    public function setClinic(?Clinic $clinic): self
    {
        $this->clinic = $clinic;

        return $this;
    }
}
