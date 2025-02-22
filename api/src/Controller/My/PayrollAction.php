<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\KpiDocument\KpiPayrollRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/payroll', name: 'api_my_payroll', methods: ['GET'])]
class PayrollAction extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository      $shifts,
        private readonly ShiftPayrollRepository $shiftPayrolls,
    )
    {
    }

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new DomainException('User not found');
        }

        $callsPayroll = 0;
        $payrollPayroll = 0;
        $kpiPayroll = 0;
        $totalPayroll = 0;

        $startOfMonth = new DateTimeImmutable('first day of this month 00:00:00');
        $endOfMonth = new DateTimeImmutable('first day of next month 00:00:00');

        [
            $hoursPayroll,
            $fuelPayroll,
            $parkingPayroll,
            $rentCarPayroll
        ] = $this->calcShifts($startOfMonth, $endOfMonth, $userId);


        return $this->json([
            'hoursPayroll' => $hoursPayroll,
            'fuelPayroll' => $fuelPayroll,
            'parkingPayroll' => $parkingPayroll,
            'rentCarPayroll' => $rentCarPayroll,
            'callsPayroll' => $callsPayroll,
            'payrollPayroll' => $payrollPayroll,
            'kpiPayroll' => $kpiPayroll,
            'totalPayroll' => $totalPayroll,
        ]);
    }

    private function calcShifts(DateTimeImmutable $startOfMonth, DateTimeImmutable $endOfMonth, int $userId): array
    {
        $hoursPayroll = 0;
        $fuelPayroll = 0;
        $parkingPayroll = 0;
        $rentCarPayroll = 0;

        $shifts = $this->shifts->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $shiftIds = [];

        /** @var MedTeam $shift */
        foreach ($shifts as $shift) {
            $shiftIds[] = $shift->getId();
        }


        $shiftPayrolls = $this->shiftPayrolls->findByShiftIds(
            $shiftIds,
            $userId
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            if ($shiftPayroll->getAccruedAt() < $startOfMonth || $shiftPayroll->getAccruedAt() > $endOfMonth) {
                continue;
            }
            if ($shiftPayroll->getAmount() == 0) {
                continue;
            }
            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);

            if ($shiftPayroll->getCalculator()->getProcessor() === 'shift_fuel') {
                $fuelPayroll += $reward;
            } elseif ($shiftPayroll->getCalculator()->getProcessor() === 'shift_parking') {
                $parkingPayroll += $reward;
            } elseif ($shiftPayroll->getCalculator()->getProcessor() === 'shift_rent_car') {
                $rentCarPayroll += $reward;
            } else {
                $hoursPayroll += $reward;
            }
        }

        return [
            $hoursPayroll,
            $fuelPayroll,
            $parkingPayroll,
            $rentCarPayroll
        ];
    }
}
