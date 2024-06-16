<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
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
use App\Controller\Calling\DispatchAction;
use App\Controller\Calling\FinishAction;
use App\Controller\Calling\HospitalizationAction;
use App\Controller\Calling\HospitalizationWithoutTherapyAction;
use App\Controller\Calling\HospitalizationWithTherapyAction;
use App\Controller\Calling\RecalculateOperatorReward;
use App\Controller\Calling\RejectAction;
use App\Controller\Calling\RepeatAction;
use App\Entity\Client;
use App\Entity\MediaObject;
use App\Entity\Partner;
use App\Entity\User\User;
use App\Filter\Call\EmployeeFilter;
use App\Filter\Call\SearchByFieldsFilter;
use App\Repository\CallingRepository;
use App\State\Call\PostProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Exception;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CallingRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/calls'),
        new Post(
            normalizationContext: [
                'groups' => [
                    'calling:read',
                    'calling:item:read',
                    'calling:detail:read',
                    'partner:item:read',
                    'service:item:read'
                ]
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => [
                    'calling:read',
                    'calling:item:read',
                    'calling:detail:read',
                    'partner:item:read',
                    'service:item:read'
                ]
            ]
        ),
        new Put(
            normalizationContext: [
                'groups' => [
                    'calling:read',
                    'calling:item:read',
                    'calling:detail:read',
                    'partner:item:read',
                    'service:item:read',
                    'media_object:read',
                ]
            ],
            processor: PostProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['calling:read',  'partner:item:read', 'service:item:read', 'user:item:read']],
    denormalizationContext: ['groups' => ['calling:write','media_object:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'team' => 'exact',
    ])]
#[Post(uriTemplate: '/callings/recalculate-operators-rewards', controller: RecalculateOperatorReward::class)]
#[Post(uriTemplate: '/callings/current', controller: CurrentAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/accept', controller: AcceptAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/dispatch', controller: DispatchAction::class, input: CallingDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/arrive', controller: ArriveAction::class, input: CallingArriveDto::class, read: false)]
#[Post(uriTemplate: '/callings/{id}/complete', controller: CompleteAction::class)]
#[Post(uriTemplate: '/callings/{id}/finish', controller: FinishAction::class)]
#[Post(uriTemplate: '/callings/{id}/codding', controller: CoddingAction::class)]
#[Post(uriTemplate: '/callings/{id}/hospitalization', controller: HospitalizationAction::class)]
#[Post(uriTemplate: '/callings/{id}/hospitalization-with-therapy', controller: HospitalizationWithTherapyAction::class)]
#[Post(uriTemplate: '/callings/{id}/hospitalization-without-therapy', controller: HospitalizationWithoutTherapyAction::class)]
#[Post(uriTemplate: '/callings/{id}/repeat', controller: RepeatAction::class)]
#[Post(uriTemplate: '/callings/{id}/reject',controller: RejectAction::class)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'completedAt'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'createdAt' => DateFilterInterface::EXCLUDE_NULL,
        'updatedAt' => DateFilterInterface::EXCLUDE_NULL,
        'completedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
#[ApiFilter(
    EmployeeFilter::class,
    properties: ['employee']
)]
#[ApiFilter(
    SearchByFieldsFilter::class,
    properties: ['search']
)]
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

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $fio = null;

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
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;


    #[ORM\Column]
    #[Groups(['calling:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read'])]
    private ?DateTimeImmutable $dispatchedAt = null;

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
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['calling:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['admin.id' => 'exact'])]
    private ?User $admin;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['calling:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['doctor.id' => 'exact'])]
    private ?User $doctor;

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
    private ?int $coastHospitalAdmission = null;

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
    #[Groups(['calling:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['partner.id' => 'exact'])]
    private ?Partner $partner = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $deleted;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $lon;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $lat;

    #[ORM\OneToMany(mappedBy: 'calling', targetEntity: Row::class, cascade: ['persist'])]
    #[Groups(['calling:read', 'calling:write'])]
    private Collection $services;

    #[ORM\Column(nullable: true)]
    private ?int $amount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $paymentNextOrder = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $paymentHospitalization = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $totalAmount = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $owner = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $partnerReward = null;

    #[ORM\Column(nullable: true, options: ["default" => 0])]
    #[Groups(['calling:read', 'calling:write'])]
    private ?int $mkadDistance = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['calling:read', 'calling:write'])]
    private ?string $ownerExternalId = null;

    #[ORM\ManyToOne]
    #[Groups(['calling:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['operator.id' => 'exact'])]
    private ?User $operator = null;

    #[ORM\Embedded(class: OperatorReward::class)]
    #[Groups(['calling:read'])]
    private OperatorReward $operatorReward;

    #[ORM\ManyToOne(inversedBy: 'callings')]
    #[Groups(['calling:read', 'calling:write'])]
    #[ApiFilter(SearchFilter::class, properties: ['client.id' => 'exact'])]
    private ?Client $client = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write'])]
    private bool $noBusinessCards;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    #[Groups(['calling:read', 'calling:write'])]
    private bool $partnerHospitalization;


    #[ORM\ManyToMany(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'calling_images')]
    #[ORM\JoinColumn(name: 'call_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['calling:read', 'calling:write'])]
    private Collection $images;

    public function __construct(
        string  $numberCalling,
        string  $title,
        string  $name,
        string  $phone,
        ?string $address,
        ?string $description,
        ?User    $admin,
        ?User    $doctor
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
        $this->deleted = false;
        $this->lat = null;
        $this->lon = null;
        $this->services = new ArrayCollection();
        $this->operatorReward = new OperatorReward(0,0,0,0);
        $this->noBusinessCards = false;
        $this->partnerHospitalization = false;

        $this->images = new ArrayCollection();
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
        //if (!$this->status->isAccepted()) {
        //    throw new DomainException('Вызов имеет статус отличный от принят');
        //}
        $this->status = Status::arrived();
        $this->arrivedAt = $arrivedAt;
    }

    public function setDispatched(DateTimeImmutable $dispatchedAt): void
    {
        $this->status = Status::dispatched();
        $this->dispatchedAt = $dispatchedAt;
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
     * @throws Exception
     */
    public function getResultDateFormat(): ?string
    {
        return $this->resultDate ? (new DateTimeImmutable($this->resultDate))->format('d.m.y H:m') : null;
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

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(?string $lon): void
    {
        $this->lon = $lon;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): void
    {
        $this->lat = $lat;
    }

    public function getCoastHospitalAdmission(): ?int
    {
        return $this->coastHospitalAdmission;
    }

    public function setCoastHospitalAdmission(?int $coastHospitalAdmission): void
    {
        $this->coastHospitalAdmission = $coastHospitalAdmission;
    }

    public function getFio(): ?string
    {
        return $this->fio;
    }

    public function setFio(?string $fio): void
    {
        $this->fio = $fio;
    }

    public function getDispatchedAt(): ?DateTimeImmutable
    {
        return $this->dispatchedAt;
    }

    /**
     * @return Collection<int, Row>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Row $row): self
    {
        if (!$this->services->contains($row)) {
            $this->services->add($row);
            $row->setCalling($this);
        }

        $this->price = 0;
        $this->paymentNextOrder = 0;
        $this->totalAmount = 0;

        /** @var Row $serviceRow */
        foreach ($this->services as $serviceRow){
            if ($serviceRow->getService()->getType() === 'default'){
                $this->price  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }else{
                $this->paymentNextOrder  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }
            $this->totalAmount = $this->price + $this->paymentNextOrder;
        }

        return $this;
    }

    public function removeService(Row $row): self
    {
        if ($this->services->removeElement($row)) {
            // set the owning side to null (unless already changed)
            if ($row->getCalling() === $this) {
                $row->setCalling(null);
            }
        }

        $this->price = 0;
        $this->paymentNextOrder = 0;
        $this->totalAmount = 0;

        /** @var Row $serviceRow */
        foreach ($this->services as $serviceRow){
            if ($serviceRow->getService()->getType() === 'default'){
                $this->price  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }else{
                $this->paymentNextOrder  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }
            $this->totalAmount = $this->price + $this->paymentNextOrder;
        }

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentNextOrder(): ?int
    {
        return $this->paymentNextOrder;
    }

    public function setPaymentNextOrder(?int $paymentNextOrder): self
    {
        $this->paymentNextOrder = $paymentNextOrder;

        return $this;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getOwner(): ?self
    {
        return $this->owner;
    }

    public function setOwner(?self $owner): self
    {
        $this->owner = $owner;

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

    public function getMkadDistance(): ?int
    {
        return $this->mkadDistance;
    }

    public function setMkadDistance(?int $mkadDistance): self
    {
        $this->mkadDistance = $mkadDistance;

        return $this;
    }

    public function isComplete(): bool
    {
        return $this->status === Status::completed();
    }


    #[SerializedName('repeat')]
    #[Groups(['calling:read'])]
    public function getCountRepeat(): int
    {
        if ($this->owner){
            return $this->owner->getCountRepeat() + 1;
        }

        return 0;
    }

    public function getPaymentHospitalization(): ?int
    {
        return $this->paymentHospitalization;
    }

    public function setPaymentHospitalization(?int $paymentHospitalization): void
    {
        $this->paymentHospitalization = $paymentHospitalization;
    }

    public function getOwnerExternalId(): ?string
    {
        return $this->ownerExternalId;
    }

    public function setOwnerExternalId(?string $ownerExternalId): void
    {
        $this->ownerExternalId = $ownerExternalId;
    }

    public function getOperator(): ?User
    {
        return $this->operator;
    }

    public function setOperator(?User $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function getOperatorReward(): OperatorReward
    {
        return $this->operatorReward;
    }

    public function setOperatorReward(OperatorReward $operatorReward): void
    {
        $this->operatorReward = $operatorReward;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function isNoBusinessCards(): bool
    {
        return $this->noBusinessCards;
    }

    public function setNoBusinessCards(bool $noBusinessCards): self
    {
        $this->noBusinessCards = $noBusinessCards;

        return $this;
    }


    #[Groups(['calling:read'])]
    public function isCurrentNoBusinessCards(): bool
    {
        return $this->partner?->isNoBusinessCards() ?:$this->noBusinessCards;
    }

    public function isPartnerHospitalization(): bool
    {
        return $this->partnerHospitalization;
    }

    public function setPartnerHospitalization(bool $partnerHospitalization): self
    {
        $this->partnerHospitalization = $partnerHospitalization;

        return $this;
    }

    #[Groups(['calling:read'])]
    public function isCurrentPartnerHospitalization(): bool
    {
        return $this->partner?->isPartnerHospitalization() ?:$this->partnerHospitalization;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MediaObject $mediaObject): self
    {
        if (!$this->images->contains($mediaObject)) {
            $this->images->add($mediaObject);
        }

        return $this;
    }

    public function removeImage(MediaObject $mediaObject): self
    {
        $this->images->removeElement($mediaObject);

        return $this;
    }
}
