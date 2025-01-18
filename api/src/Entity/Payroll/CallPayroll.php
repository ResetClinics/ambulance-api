<?php

namespace App\Entity\Payroll;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\User\User;
use App\Repository\Payroll\CallPayrollRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CallPayrollRepository::class)]
#[ORM\Table(name: 'payroll_employee_calls')]
class CallPayroll
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded]
    #[Assert\NotNull]
    private ?Money $accrued = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $accruedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calling $call = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PayrollStrategy $strategy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccrued(): ?Money
    {
        return $this->accrued;
    }

    public function setAccrued(Money $value): void
    {
        $this->accrued = $value;
    }

    public function getAccruedAt(): ?\DateTimeImmutable
    {
        return $this->accruedAt;
    }

    public function setAccruedAt(\DateTimeImmutable $accruedAt): static
    {
        $this->accruedAt = $accruedAt;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getCall(): ?Calling
    {
        return $this->call;
    }

    public function setCall(?Calling $call): static
    {
        $this->call = $call;

        return $this;
    }

    public function getStrategy(): ?PayrollStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(?PayrollStrategy $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }
}
