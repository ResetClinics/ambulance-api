<?php

namespace App\Entity\Partner\Agreement;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Service\Service;
use App\Repository\Partner\Agreement\AgreementTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgreementTemplateRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => [
            'agreement:read',
            'agreement:item:read',
            'service:item:read'
        ]],
    denormalizationContext: ['groups' => ['agreement:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
class AgreementTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['agreement:read', 'agreement:write'])]
    private ?int $distance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\NotBlank]
    private ?Service $service = null;

    #[ORM\Column]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\NotBlank]
    private ?float $percent = null;

    #[ORM\Column]
    #[Groups(['agreement:read', 'agreement:write'])]
    #[Assert\NotBlank]
    private ?int $repeatNumber = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getRepeatNumber(): ?int
    {
        return $this->repeatNumber;
    }

    public function setRepeatNumber(int $repeatNumber): self
    {
        $this->repeatNumber = $repeatNumber;

        return $this;
    }
}
