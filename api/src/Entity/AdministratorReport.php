<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\MedTeam\MedTeam;
use App\Repository\AdministratorReportRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdministratorReportRepository::class)]
#[ORM\Table(name: 'administrator_reports')]
#[ApiResource(
    normalizationContext: ['groups' => ['administrator_report:read']],
    denormalizationContext: ['groups' => ['administrator_report:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(SearchFilter::class, properties: ['team.id' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['team.admin.id' => 'exact'])]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'createdAt' => DateFilterInterface::EXCLUDE_NULL,
        'updatedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
class AdministratorReport
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['administrator_report:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Groups(['administrator_report:read', 'administrator_report:write'])]
    #[Assert\NotBlank]
    private ?MedTeam $team = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['administrator_report:read', 'administrator_report:write'])]
    private ?int $mileage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['administrator_report:read', 'administrator_report:write'])]
    private ?int $toolRoad = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['administrator_report:read', 'administrator_report:write'])]
    private ?int $parkingFees = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?MedTeam
    {
        return $this->team;
    }

    public function setTeam(?MedTeam $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getToolRoad(): ?int
    {
        return $this->toolRoad;
    }

    public function setToolRoad(?int $toolRoad): self
    {
        $this->toolRoad = $toolRoad;

        return $this;
    }

    public function getParkingFees(): ?int
    {
        return $this->parkingFees;
    }

    public function setParkingFees(?int $parkingFees): self
    {
        $this->parkingFees = $parkingFees;

        return $this;
    }

    #[Groups(['administrator_report:read'])]
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['administrator_report:read'])]
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
