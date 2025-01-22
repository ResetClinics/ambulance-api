<?php

declare(strict_types=1);

namespace App\Entity\Payroll\KpiDocument;

use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShiftPayrollRepository::class)]
#[ORM\Table(name: 'payroll_employee_kpis')]
class KpiPayroll
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded]
    #[Assert\NotNull]
    private Money $original;

    #[ORM\Column(nullable: false)]
    private float $kpi;

    #[ORM\Embedded]
    #[Assert\NotNull]
    private Money $accrued;

    #[ORM\Column(nullable: false)]
    private DateTimeImmutable $accruedAt;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private PayrollCalculator $calculator;

    #[ORM\ManyToOne(inversedBy: 'kpiPayrolls')]
    #[ORM\JoinColumn(nullable: false)]
    private KpiRecord $record;

    public function __construct(
        KpiRecord $record,
        PayrollCalculator $calculator,
        DateTimeImmutable $accruedAt,
        Money $original,
        float $kpi,
        Money $accrued,
    ) {
        $this->record = $record;
        $this->calculator = $calculator;
        $this->accruedAt = $accruedAt;
        $this->original = $original;
        $this->kpi = $kpi;
        $this->accrued = $accrued;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccrued(): ?Money
    {
        return $this->accrued;
    }

    public function getAccruedAt(): DateTimeImmutable
    {
        return $this->accruedAt;
    }

    public function getCalculator(): PayrollCalculator
    {
        return $this->calculator;
    }

    public function getKpi(): ?float
    {
        return $this->kpi;
    }

    public function getRecord(): KpiRecord
    {
        return $this->record;
    }

    public function getOriginal(): Money
    {
        return $this->original;
    }
}
