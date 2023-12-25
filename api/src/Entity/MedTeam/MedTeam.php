<?php

namespace App\Entity\MedTeam;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\User\User;
use App\Repository\MedTeam\MedTeamRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedTeamRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['med-team:read', 'user:item:read', 'phone:read']],
    denormalizationContext: ['groups' => ['med-team:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'plannedA' => DateFilterInterface::EXCLUDE_NULL,
        'startedAt' => DateFilterInterface::EXCLUDE_NULL,
        'completedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
class MedTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['med-team:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['med-team:read', 'med-team:write'])]
    #[Assert\NotNull]
    private ?DateTimeImmutable $plannedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['med-team:read', 'med-team:write'])]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['med-team:read', 'med-team:write'])]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(length: 32)]
    #[Groups(['med-team:read', 'med-team:write'])]
    #[Assert\Choice(choices: [
        'draft',
        'scheduled',
        'work',
        'completed',
        'cancelled',
    ])]
    #[ApiFilter(SearchFilter::class, properties: ['status' => 'exact'])]
    private string $status = 'draft';

    #[ORM\ManyToOne]
    #[Groups(['med-team:read', 'med-team:write'])]
    #[ApiFilter(SearchFilter::class, properties: ['admin.id' => 'exact'])]
    private ?User $admin = null;

    #[ORM\ManyToOne]
    #[Groups(['med-team:read', 'med-team:write'])]
    #[ApiFilter(SearchFilter::class, properties: ['admin.id' => 'exact'])]
    private ?User $doctor = null;

    #[ORM\ManyToOne]
    #[Groups(['med-team:read', 'med-team:write'])]
    private ?Phone $phone = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlannedAt(): ?DateTimeImmutable
    {
        return $this->plannedAt;
    }

    public function setPlannedAt(DateTimeImmutable $plannedAt): self
    {
        $this->plannedAt = $plannedAt;

        return $this;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

}
