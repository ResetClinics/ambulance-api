<?php

namespace App\Entity\Hospital;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Calling\Calling;
use App\Entity\Partner;
use App\Repository\Hospital\HospitalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HospitalRepository::class)]
#[ORM\Table(name: 'hospital_hospitals')]
#[ApiResource(
    normalizationContext: ['groups' => ['hospital:read',  'partner:item:read']],
    denormalizationContext: ['groups' => ['hospital:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
class Hospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?string $external = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?string $fio = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[Assert\Choice(choices: [
        'assigned',
        'inpatient',
        'completed',
        'cancelled'
    ])]
    private ?string $status = 'assigned';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[Assert\Choice(choices: [
        'alcohol',
        'drugs',
        'alcohol-drugs',
        'gambler',
        'psycho'
    ])]
    private ?string $nosology = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[ApiFilter(SearchFilter::class, properties: ['partner.id' => 'exact'])]
    private ?Partner $partner = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read'])]
    private ?int $amount = null;

    #[ORM\ManyToOne]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?Calling $owner = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $prepayment = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?Clinic $clinic = null;

    #[ORM\Column(length: 11, nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    #[Assert\Regex(
        pattern: '/\d{11}/',
        message: 'Номер телефона должен состоять из 11 цифр.'
    )]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $additionalAmount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $mainAmount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternal(): ?string
    {
        return $this->external;
    }

    public function setExternal(?string $external): self
    {
        $this->external = $external;

        return $this;
    }

    public function getFio(): ?string
    {
        return $this->fio;
    }

    public function setFio(?string $fio): self
    {
        $this->fio = $fio;

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

    public function getNosology(): ?string
    {
        return $this->nosology;
    }

    public function setNosology(?string $nosology): self
    {
        $this->nosology = $nosology;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->additionalAmount === null ? 0 : $this->additionalAmount;
    }

    public function getOwner(): ?Calling
    {
        return $this->owner;
    }

    public function setOwner(?Calling $owner): self
    {
        $this->owner = $owner;

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

    public function getClinic(): ?Clinic
    {
        return $this->clinic;
    }

    public function setClinic(?Clinic $clinic): self
    {
        $this->clinic = $clinic;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdditionalAmount(): int
    {
        return $this->additionalAmount === null ? 0 : $this->additionalAmount;
    }

    public function setAdditionalAmount(?int $additionalAmount): self
    {
        $this->additionalAmount = $additionalAmount;

        $this->calcAmount();

        return $this;
    }

    public function getMainAmount(): int
    {
        return $this->additionalAmount === null ? 0 : $this->additionalAmount;
    }

    public function setMainAmount(?int $mainAmount): self
    {
        $this->mainAmount = $mainAmount;

        $this->calcAmount();

        return $this;
    }

    private function calcAmount(): void
    {
        $mainAmount = $this->mainAmount === null ? 0 : $this->mainAmount;
        $additionalAmount = $this->additionalAmount === null ? 0 : $this->additionalAmount;

        $this->amount = $mainAmount + $additionalAmount;
    }
}
