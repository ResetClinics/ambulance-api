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
    private ?string $status;

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
    #[Groups(['hospital:read', 'hospital:write'])]
    private ?int $amount = null;

    #[ORM\ManyToOne]
    private ?Calling $owner = null;

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
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
}
