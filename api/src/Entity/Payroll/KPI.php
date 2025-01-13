<?php

namespace App\Entity\Payroll;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\Payroll\KPIRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: KPIRepository::class)]
#[ORM\Table(name: 'payroll_kpi')]
#[ApiResource(
    shortName: 'Payroll/Kpi',
    operations: [
        new GetCollection(),
        new Get(),
        new Patch(),
    ],
    routePrefix: '/api/v1',
    normalizationContext: ['groups' => ['payroll_kpi:read']],
    denormalizationContext: ['groups' => ['payroll_kpi:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
class KPI
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['payroll_kpi:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(['payroll_kpi:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['payroll_kpi:read', 'payroll_kpi:write'])]
    private ?float $value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['payroll_kpi:read', 'payroll_kpi:write'])]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
