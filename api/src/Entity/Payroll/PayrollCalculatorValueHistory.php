<?php

declare(strict_types=1);

namespace App\Entity\Payroll;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payroll_calculator_value_history')]
class PayrollCalculatorValueHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'valueHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PayrollCalculator $calculator = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $effectiveFrom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalculator(): ?PayrollCalculator
    {
        return $this->calculator;
    }

    public function setCalculator(PayrollCalculator $calculator): self
    {
        $this->calculator = $calculator;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getEffectiveFrom(): \DateTimeImmutable
    {
        return $this->effectiveFrom;
    }

    public function setEffectiveFrom(\DateTimeImmutable $effectiveFrom): self
    {
        $this->effectiveFrom = $effectiveFrom;

        return $this;
    }
}
