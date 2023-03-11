<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Calling\AcceptAction;
use App\Controller\Calling\ArriveAction;
use App\Controller\Calling\CompleteAction;
use App\Controller\Calling\CurrentAction;
use App\Controller\Calling\RejectAction;
use App\Entity\Team\Team;
use App\Repository\CallingRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: CallingRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(
            normalizationContext: ['groups' => ['calling:read', 'calling:item:get']]
        ),
        new Put(),
    ],
    normalizationContext: ['groups' => ['calling:read']],
    denormalizationContext: ['groups' => ['calling:write']]
)]
#[Post(uriTemplate: '/callings/current', controller: CurrentAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/accept', controller: AcceptAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/reject', controller: RejectAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/arrive', controller: ArriveAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/complete', controller: CompleteAction::class, input: CallingDto::class, read: false)]
class Calling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $name;

    #[ORM\Column(length: 16)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $phone;


    #[ORM\Column(length: 255)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $address;

    #[ORM\ManyToOne(inversedBy: 'callings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calling:read', 'calling:write'])]
    private Team $team;

    #[ORM\Column(type: 'calling_status', length: 16, nullable: false)]
    #[Groups(['calling:read'])]
    private Status $status;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $description;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $rejectedComment = null;

    #[ORM\Column]
    #[Groups(['calling:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y HH:mm'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $arrivedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $completedAt = null;

    /**
     * @param string $name
     * @param string $phone
     * @param string $address
     * @param Team $team
     * @param string $description
     */
    public function __construct(string $name, string $phone, string $address, Team $team, string $description)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address;
        $this->team = $team;
        $this->description = $description;
        $this->status = Status::assigned();
        $this->createdAt = new DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function setAccepted(DateTimeImmutable $acceptedAt): void
    {
        if (!$this->status->isAssigned()) {
            throw new DomainException('Вызов имеет статус отличный от назначен');
        }
        $this->status = Status::accepted();
        $this->acceptedAt = $acceptedAt;
    }

    public function setArrived(DateTimeImmutable $arrivedAt): void
    {
        if (!$this->status->isAccepted()) {
            throw new DomainException('Вызов имеет статус отличный от принят');
        }
        $this->status = Status::arrived();
        $this->arrivedAt = $arrivedAt;
    }

    public function setComplete(DateTimeImmutable $completedAt): void
    {
        if (!$this->status->isArrived()) {
            throw new DomainException('Вызов имеет статус отличный от прибыли');
        }
        $this->status = Status::completed();
        $this->completedAt = $completedAt;
    }

    public function setReject(DateTimeImmutable $completedAt, string $comment): void
    {
        if (!$this->status->isAssigned()) {
            throw new DomainException('Вызов имеет статус отличный от назначен');
        }
        $this->status = Status::rejected();
        $this->completedAt = $completedAt;
        $this->rejectedComment = $comment;
    }

    public function getStatus(): string
    {
        return $this->status->getName();
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function getAcceptedAt(): ?DateTimeImmutable
    {
        return $this->acceptedAt;
    }


    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }


    public function getRejectedComment(): ?string
    {
        return $this->rejectedComment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function inProgress(): bool
    {
       return !$this->status->isRejected() && !$this->status->isCompleted();
    }

    public function getArrivedAt(): ?DateTimeImmutable
    {
        return $this->arrivedAt;
    }
}
