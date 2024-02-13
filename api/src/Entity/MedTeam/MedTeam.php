<?php

namespace App\Entity\MedTeam;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\MedTeam\SendSms;
use App\Entity\Base;
use App\Entity\Car;
use App\Entity\User\User;
use App\Repository\MedTeam\MedTeamRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedTeamRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Delete(),
        new Patch(),
        new Post(
            uriTemplate: 'med_teams/{id}/send-sms',
            controller: SendSms::class,
            name: 'med_teams-send_sms'
        )
    ],
    normalizationContext: ['groups' => ['med-team:read', 'user:item:read', 'phone:read', 'car:read', 'base:read']],
    denormalizationContext: ['groups' => ['med-team:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'plannedStartAt' => DateFilterInterface::EXCLUDE_NULL,
        'plannedFinishAt' => DateFilterInterface::EXCLUDE_NULL,
        'startedAt' => DateFilterInterface::EXCLUDE_NULL,
        'completedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['plannedStartAt'], arguments: ['orderParameterName' => 'order'])]
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
    private ?DateTimeImmutable $plannedStartAt = null;

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
    private string $status = 'scheduled';

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

    #[ORM\Column(nullable: true)]
    #[Groups(['med-team:read', 'med-team:write'])]
    private ?DateTimeImmutable $plannedFinishAt = null;

    #[ORM\ManyToOne]
    #[Groups(['med-team:read', 'med-team:write'])]
    private ?Base $base = null;

    #[ORM\ManyToOne]
    #[Groups(['med-team:read', 'med-team:write'])]
    private ?Car $car = null;

    #[ORM\OneToMany(mappedBy: 'medTeam', targetEntity: Location::class)]
    #[Groups(['med-team:read'])]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlannedStartAt(): ?DateTimeImmutable
    {
        return $this->plannedStartAt;
    }

    public function setPlannedStartAt(DateTimeImmutable $plannedStartAt): self
    {
        $this->plannedStartAt = $plannedStartAt;

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

    public function getPlannedFinishAt(): ?DateTimeImmutable
    {
        return $this->plannedFinishAt;
    }

    public function setPlannedFinishAt(?DateTimeImmutable $plannedFinishAt): self
    {
        $this->plannedFinishAt = $plannedFinishAt;

        return $this;
    }

    public function getBase(): ?Base
    {
        return $this->base;
    }

    public function setBase(?Base $base): self
    {
        $this->base = $base;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setMedTeam($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getMedTeam() === $this) {
                $location->setMedTeam(null);
            }
        }

        return $this;
    }

}
