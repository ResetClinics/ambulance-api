<?php

declare(strict_types=1);

namespace App\Services\Payroll\ShiftCalculator;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;
use App\Entity\Payroll\ShiftPayroll;
use App\Entity\User\User;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use DomainException;

abstract class AbstractEmployeeCalculator implements ShiftCalculatorInterface
{
    public function __construct(
        private readonly ShiftPayrollRepository $shiftPayrolls,
    ) {}

    final public function calculate(MedTeam $shift, PayrollCalculator $payrollCalculator): void
    {
        if ($shift->getStatus() !== 'completed') {
            return;
        }

        if (!$shift->getPlannedStartAt() || !$shift->getPlannedFinishAt()) {
            return;
        }

        $employee = $this->getEmployee($shift);
        if (!$employee) {
            return;
        }

        $workHours = $this->calculateWorkHours(
            $shift->getPlannedStartAt(),
            $shift->getPlannedFinishAt(),
        );

        foreach ($workHours as $date => $allHours) {
            $accruedAt = new DateTimeImmutable($date);

            $rate = (float)$payrollCalculator->getValue();
            $hours = (int)$allHours[$this->getHoursKey()];

            $accrued = new Money(
                (int)(((float)$hours * $rate) * 100)
            );

            $shiftPayroll = new ShiftPayroll();
            $shiftPayroll
                ->setAccruedAt($accruedAt)
                ->setAccrued($accrued)
                ->setCalculator($payrollCalculator)
                ->setEmployee($employee)
                ->setAmount($hours)
                ->setShift($shift);

            $this->shiftPayrolls->add($shiftPayroll);
        }
    }

    protected function getHoursKey(): string
    {
        throw new DomainException('Not implemented getHoursKey');
    }

    protected function getEmployee(MedTeam $shift): ?User
    {
        throw new DomainException('Not implemented getEmployee');
    }

    private function calculateWorkHours(DateTimeImmutable $plannedStartAt, DateTimeImmutable $plannedFinishAt): array
    {
        $workHours = [];
        $currentDate = $plannedStartAt;
        $finishDate = $plannedFinishAt;

        while ($currentDate <= $finishDate) {
            $nextDay = $currentDate->modify('+1 day');
            $startOfDay = $currentDate->setTime(6, 0);
            $endOfDay = $currentDate->setTime(22, 0);
            $endOfCalendarDay = $nextDay->setTime(0, 0);
            $weFinishOnTheCurrentDay = $currentDate->format('d') === $finishDate->format('d');

            $dayHours = 0;
            $nightHours = 0;

            if ($weFinishOnTheCurrentDay) {
                if ($currentDate <= $startOfDay) {
                    if ($finishDate <= $startOfDay) {
                        $interval = $currentDate->diff($finishDate);
                        $nightHours += $interval->h;
                    } else {
                        $interval = $currentDate->diff($startOfDay);
                        $nightHours += $interval->h;
                        if ($finishDate <= $endOfDay) {
                            $interval = $startOfDay->diff($finishDate);
                            $dayHours += $interval->h;
                        } else {
                            $interval = $startOfDay->diff($endOfDay);
                            $dayHours += $interval->h;
                            $interval = $endOfDay->diff($finishDate);
                            $nightHours += $interval->h;
                        }
                    }
                } else {
                    if ($finishDate <= $endOfDay) {
                        $interval = $currentDate->diff($finishDate);
                        $dayHours += $interval->h;
                    } else {
                        $interval = $currentDate->diff($endOfDay);
                        $dayHours += $interval->h;
                        $interval = $endOfDay->diff($finishDate);
                        $nightHours += $interval->h;
                    }
                }
            } else {
                if ($currentDate <= $startOfDay) {
                    $interval = $currentDate->diff($startOfDay);
                    $nightHours += $interval->h;
                    if ($finishDate <= $endOfDay) {
                        $interval = $startOfDay->diff($finishDate);
                        $dayHours += $interval->h;
                    } else {
                        $interval = $startOfDay->diff($endOfDay);
                        $dayHours += $interval->h;
                        $interval = $endOfDay->diff($finishDate);
                        $nightHours += $interval->h;
                    }
                } else {
                    $interval = $currentDate->diff($endOfDay);
                    $dayHours += $interval->h;
                    $interval = $endOfDay->diff($endOfCalendarDay);
                    $nightHours += $interval->h;
                }
            }

            $workHours[$currentDate->format('Y-m-d')] = [
                'day' => $dayHours,
                'night' => $nightHours,
            ];

            $currentDate = $nextDay->setTime(0, 0);
        }

        return $workHours;
    }
}
