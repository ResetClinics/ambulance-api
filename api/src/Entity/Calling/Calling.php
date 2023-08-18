<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Calling\AcceptAction;
use App\Controller\Calling\ArriveAction;
use App\Controller\Calling\CoddingAction;
use App\Controller\Calling\CompleteAction;
use App\Controller\Calling\CurrentAction;
use App\Controller\Calling\HospitalizationAction;
use App\Controller\Calling\RejectAction;
use App\Controller\Calling\RepeatAction;
use App\Entity\Partner;
use App\Entity\User\User;
use App\Repository\CallingRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CallingRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(
            normalizationContext: ['groups' => ['calling:read', 'calling:item:read', 'calling:detail:read']]
        ),
        new Put(),
    ],
    normalizationContext: ['groups' => ['calling:read']],
    denormalizationContext: ['groups' => ['calling:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['team' => 'exact'])]
#[Post(uriTemplate: '/callings/current', controller: CurrentAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/accept', controller: AcceptAction::class, input: CallingDto::class, read: false)]
#[Post(
    uriTemplate: '/callings/{id}/reject',
    controller: RejectAction::class
)]
#[Post(uriTemplate: '/callings/arrive', controller: ArriveAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/complete', controller: CompleteAction::class)]
#[Post(uriTemplate: '/callings/{id}/codding', controller: CoddingAction::class)]
#[Post(uriTemplate: '/callings/{id}/hospitalization', controller: HospitalizationAction::class)]
#[Post(uriTemplate: '/callings/{id}/repeat', controller: RepeatAction::class)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'], arguments: ['orderParameterName' => 'order'])]
class Calling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['calling:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 256)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $title;

    #[ORM\Column(length: 128)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $name;

    #[ORM\Column(length: 16)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $phone;

    #[ORM\Column(length: 32)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $numberCalling;

    #[ORM\Column(length: 255)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $address;

    #[ORM\Column(type: 'calling_status', length: 16, nullable: false)]
    #[Groups(['calling:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private Status $status;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['calling:read', 'calling:write'])]
    private string $description;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $chronicDiseases = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $nosology = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $age = null;
    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $leadType = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $partnerName = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['calling:read', 'calling:write'])]
    #[ApiFilter(BooleanFilter::class)]
    private bool $sendPhone = false;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $rejectedComment = null;

    #[ORM\Column]
    #[Groups(['calling:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y HH:mm'])]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;


    #[ORM\Column]
    #[Groups(['calling:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y HH:mm'])]
    #[Gedmo\Timestampable]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $arrivedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $dateTime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calling:detail:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['admin.id' => 'exact'])]
    private User $admin;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calling:detail:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['doctor.id' => 'exact'])]
    private User $doctor;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $estimated = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $prepayment = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $note = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $passport = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $coastHospital = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $costDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $phoneRelatives = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $resultDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $resultTime = null;

    #[ORM\ManyToOne(inversedBy: 'callings')]
    private ?Partner $partner = null;


    public function __construct(
        string  $numberCalling,
        string  $title,
        string  $name,
        string  $phone,
        ?string $address,
        ?string $description,
        User    $admin,
        User    $doctor
    )
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address ?: '';
        $this->description = $description ?: '';
        $this->status = Status::assigned();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->numberCalling = $numberCalling;
        $this->title = $title;
        $this->admin = $admin;
        $this->doctor = $doctor;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address ?: '';

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
        $this->status = Status::completed();
        $this->completedAt = $completedAt;
    }

    public function setReject(DateTimeImmutable $completedAt, string $comment): void
    {
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNumberCalling(): string
    {
        return $this->numberCalling;
    }

    public function getChronicDiseases(): ?string
    {
        return $this->chronicDiseases;
    }

    public function getLeadType(): ?string
    {
        return $this->leadType;
    }

    public function getPartnerName(): ?string
    {
        return $this->partnerName;
    }

    public function isSendPhone(): bool
    {
        return $this->sendPhone;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getDateTime(): ?DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $numberCalling
     */
    public function setNumberCalling(string $numberCalling): void
    {
        $this->numberCalling = $numberCalling;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string|null $chronicDiseases
     */
    public function setChronicDiseases(?string $chronicDiseases): void
    {
        $this->chronicDiseases = $chronicDiseases;
    }

    /**
     * @param string|null $leadType
     */
    public function setLeadType(?string $leadType): void
    {
        $this->leadType = $leadType;
    }

    /**
     * @param string|null $partnerName
     */
    public function setPartnerName(?string $partnerName): void
    {
        $this->partnerName = $partnerName;
    }

    /**
     * @param bool $sendPhone
     */
    public function setSendPhone(bool $sendPhone): void
    {
        $this->sendPhone = $sendPhone;
    }

    /**
     * @param string|null $rejectedComment
     */
    public function setRejectedComment(?string $rejectedComment): void
    {
        $this->rejectedComment = $rejectedComment;
    }

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeImmutable|null $acceptedAt
     */
    public function setAcceptedAt(?DateTimeImmutable $acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt;
    }

    /**
     * @param DateTimeImmutable|null $arrivedAt
     */
    public function setArrivedAt(?DateTimeImmutable $arrivedAt): void
    {
        $this->arrivedAt = $arrivedAt;
    }

    /**
     * @param DateTimeImmutable|null $completedAt
     */
    public function setCompletedAt(?DateTimeImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    /**
     * @param DateTimeImmutable|null $dateTime
     */
    public function setDateTime(?DateTimeImmutable $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return string|null
     */
    public function getNosology(): ?string
    {
        return $this->nosology;
    }

    /**
     * @return string|null
     */
    public function getAge(): ?string
    {
        return $this->age;
    }

    /**
     * @param string|null $nosology
     */
    public function setNosology(?string $nosology): void
    {
        $this->nosology = $nosology;
    }

    /**
     * @param string|null $age
     */
    public function setAge(?string $age): void
    {
        $this->age = $age;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEstimated(): ?int
    {
        return $this->estimated;
    }

    public function setEstimated(?int $estimated): self
    {
        $this->estimated = $estimated;

        return $this;
    }

    public function getPrepayment(): ?int
    {
        return $this->prepayment;
    }

    public function setPrepayment(?int $prepayment): self
    {
        $this->prepayment = $prepayment;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }

    public function setPassport(?string $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    public function getCoastHospital(): ?int
    {
        return $this->coastHospital;
    }

    public function setCoastHospital(?int $coastHospital): self
    {
        $this->coastHospital = $coastHospital;

        return $this;
    }

    public function getCostDay(): ?int
    {
        return $this->costDay;
    }

    public function setCostDay(?int $costDay): self
    {
        $this->costDay = $costDay;

        return $this;
    }

    public function getPhoneRelatives(): ?string
    {
        return $this->phoneRelatives;
    }

    public function setPhoneRelatives(?string $phoneRelatives): self
    {
        $this->phoneRelatives = $phoneRelatives;

        return $this;
    }

    public function getResultDate(): ?string
    {
        return $this->resultDate;
    }

    /**
     * @throws \Exception
     */
    public function getResultDateFormat(): ?string
    {
        return $this->resultDate ? (new DateTimeImmutable($this->resultDate))->format('d.m.y') : null;
    }

    public function setResultDate(?string $resultDate): self
    {
        $this->resultDate = $resultDate;

        return $this;
    }

    public function getResultTime(): ?string
    {
        return $this->resultTime;
    }

    public function setResultTime(?string $resultTime): self
    {
        $this->resultTime = $resultTime;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }
}
