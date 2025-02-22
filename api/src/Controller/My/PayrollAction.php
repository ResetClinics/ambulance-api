<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\CallPayroll;
use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\CallingRepository;
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
        private readonly CallingRepository        $calls,
        private readonly CallPayrollRepository    $callPayrolls,
        private readonly ServicePayrollRepository $servicePayrolls
    )
    {
    }

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new DomainException('User not found');
        }

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

        $callsPayroll = $this->calcCalls($startOfMonth, $endOfMonth, $userId);

        $payrollPayroll = $hoursPayroll + $callsPayroll;

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

    private function calcCalls(DateTimeImmutable $startOfMonth, DateTimeImmutable $endOfMonth, int $userId): float|int
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $total = 0;
        $callIds = [];
        $rowIds = [];

        /** @var Calling $call */
        foreach ($calls as $call) {
            $callIds[] = $call->getId();
            foreach ($call->getServices() as $row) {
                $rowIds[] = $row->getId();
            }
        }

        $servicePayrolls = $this->servicePayrolls->findByRowIds($rowIds, $userId);

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            $total += (float)($servicePayroll->getAccrued()->amount / 100);
        }

        $callPayrolls = $this->callPayrolls->findByCallIds($callIds, $userId);

        /** @var CallPayroll $callPayroll */
        foreach ($callPayrolls as $callPayroll) {
            $total += (float)($callPayroll->getAccrued()->amount / 100);
        }

        return $total;
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
