<?php

declare(strict_types=1);

namespace App\Entity\Team;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Team\AcceptAction;
use App\Controller\Team\AdministratorAction;
use App\Controller\Team\CompleteAction;
use App\Controller\Team\RejectAction;
use App\Entity\User;
use App\Repository\TeamRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
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
#[Get(uriTemplate: '/teams/my', controller: AdministratorAction::class, read: false)]
#[Post(uriTemplate: '/teams/accept', controller: AcceptAction::class, read: false)]
#[Post(uriTemplate: '/teams/reject', controller: RejectAction::class, read: false)]
#[Post(uriTemplate: '/teams/complete', controller: CompleteAction::class, read: false)]
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

    #[ORM\Column(type: 'team_status', length: 16, nullable: false)]
    #[Groups(['team:read'])]
    private Status $status;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['team:read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['team:read'])]
    private ?DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['team:read'])]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['team:read'])]
    private ?string $rejectedComment = null;

    public function __construct(User $administrator)
    {
        $this->doctors = new ArrayCollection();
        $this->administrator = $administrator;
        $this->status = Status::assigned();
        $this->createdAt = new DateTimeImmutable();
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

    public function getStatus(): string
    {
        return $this->status->getName();
    }

    public function setAccepted(DateTimeImmutable $acceptedAt): void
    {
        if (!$this->status->isAssigned()) {
            throw new DomainException('Бригада имеет статус отличный от назначена');
        }
        $this->status = Status::accepted();
        $this->acceptedAt = $acceptedAt;
    }

    public function setComplete(DateTimeImmutable $completedAt): void
    {
        if (!$this->status->isAccepted()) {
            throw new DomainException('Бригада имеет статус отличный от принята');
        }
        $this->status = Status::completed();
        $this->completedAt = $completedAt;
    }

    public function setReject(DateTimeImmutable $completedAt, string $comment): void
    {
        if (!$this->status->isAssigned()) {
            throw new DomainException('Бригада имеет статус отличный от назначена');
        }
        $this->status = Status::rejected();
        $this->completedAt = $completedAt;
        $this->rejectedComment = $comment;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAcceptedAt(): ?DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getRejectedComment(): ?string
    {
        return $this->rejectedComment;
    }
}
