<?php

declare(strict_types=1);

namespace App\Entity\Payroll;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\Payroll\PayrollCalculatorRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PayrollCalculatorRepository::class)]
#[ORM\Table(name: 'payroll_calculator')]
#[ApiResource(
    shortName: 'Payroll/Calculator',
    operations: [
        new GetCollection(),
        new Get(),
        new Patch(),
    ],
    routePrefix: '/api/v1',
    normalizationContext: ['groups' => ['payroll_calculator:read']],
    denormalizationContext: ['groups' => ['payroll_calculator:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
#[ApiFilter(OrderFilter::class, properties: ['sort'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'type' => 'exact',
        'target' => 'exact',
    ]
)]
class PayrollCalculator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['payroll_calculator:read', 'service:item:read', 'service:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 128, nullable: false)]
    #[Assert\NotNull]
    #[Groups(['payroll_calculator:read', 'service:item:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['payroll_calculator:read', 'payroll_calculator:write'])]
    private ?string $value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['payroll_calculator:read', 'payroll_calculator:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 128, nullable: false)]
    #[Assert\NotNull]
    #[Assert\Choice(choices: [
        'salary',
        'kpi',
        'transport',
    ])]
    #[Groups(['payroll_calculator:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotNull]
    #[Groups(['payroll_calculator:read'])]
    private ?string $processor = null;

    #[ORM\Column(length: 128, nullable: false)]
    #[Assert\NotNull]
    #[Assert\Choice(choices: [
        'service',
        'call',
        'shift',
        'payroll',
    ])]
    #[Groups(['payroll_calculator:read'])]
    private ?string $target = null;

    #[ORM\Column]
    #[Groups(['payroll_calculator:read', 'payroll_calculator:write'])]
    private int $sort = 0;

    #[ORM\Column(options: ['default' => 100])]
    #[Groups(['payroll_calculator:read', 'payroll_calculator:write'])]
    private ?int $weight = null;

    /**
     * @var Collection<int, PayrollCalculatorValueHistory>
     */
    #[ORM\OneToMany(mappedBy: 'calculator', targetEntity: PayrollCalculatorValueHistory::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['effectiveFrom' => 'ASC', 'id' => 'ASC'])]
    private Collection $valueHistories;

    public function __construct()
    {
        $this->valueHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getValueForDate(DateTimeImmutable $date): ?string
    {
        $targetDate = $date->setTime(0, 0);
        $resolvedValue = $this->value;

        foreach ($this->valueHistories as $valueHistory) {
            if ($valueHistory->getEffectiveFrom() <= $targetDate) {
                $resolvedValue = $valueHistory->getValue();
                continue;
            }

            break;
        }

        return $resolvedValue;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getProcessor(): ?string
    {
        return $this->processor;
    }

    public function setProcessor(?string $processor): static
    {
        $this->processor = $processor;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getRates(): array
    {
        $value = json_decode($this->getValue(), true);

        if (!is_array($value)){
            return [];
        }

        return array_map(static function ($item) {
            return [
                'min' => (float)$item['min'],
                'max' => (float)$item['max'],
                'rate' => (float)$item['rate'],
            ];
        }, $value);
    }

    public function getRate(float $value): float
    {
        $rate = array_filter(
            $this->getRates(),
            static fn ($range) => $value >= $range['min'] && $value <= $range['max']
        );

        return (float)(reset($rate)['rate'] ?? 0);
    }

    public function getRatesForDate(DateTimeImmutable $date): array
    {
        $value = json_decode($this->getValueForDate($date), true);

        if (!is_array($value)) {
            return [];
        }

        return array_map(static function ($item) {
            return [
                'min' => (float)$item['min'],
                'max' => (float)$item['max'],
                'rate' => (float)$item['rate'],
            ];
        }, $value);
    }

    /**
     * @return Collection<int, PayrollCalculatorValueHistory>
     */
    public function getValueHistories(): Collection
    {
        return $this->valueHistories;
    }

    public function addValueHistory(PayrollCalculatorValueHistory $valueHistory): self
    {
        if (!$this->valueHistories->contains($valueHistory)) {
            $this->valueHistories->add($valueHistory);
            $valueHistory->setCalculator($this);
        }

        return $this;
    }
}
