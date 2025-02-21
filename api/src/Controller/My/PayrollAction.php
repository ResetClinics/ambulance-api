<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\Payroll\ShiftPayroll;
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
        private readonly CallPayrollRepository $callPayrolls,
        private readonly ServicePayrollRepository $servicePayrolls,
        private readonly ShiftPayrollRepository $shiftPayrolls,
        private readonly KpiPayrollRepository $kpiPayrolls,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new DomainException('User not found');
        }


        $startDate = new DateTimeImmutable();
        $startDate = $startDate->modify('midnight');
        $endDate = new DateTimeImmutable();
        $endDate = $endDate->modify('+1 day midnight');

        $total = 0;
        $callsTotal = 0;
        $shiftsTotal = 0;
        $kpisTotal = 0;


        $servicePayrolls = $this->callPayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $userId
        );

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            $reward = (float)($servicePayroll->getAccrued()->amount / 100);
            $callsTotal += $reward;
            $total += $reward;
        }

        $servicePayrolls = $this->servicePayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $userId
        );

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            $reward = (float)($servicePayroll->getAccrued()->amount / 100);
            $callsTotal += $reward;
            $total += $reward;
        }

        $shiftPayrolls = $this->shiftPayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $userId
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {

            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);
            $shiftsTotal += $reward;
            $total += $reward;
        }

        $kpiPayrolls = $this->kpiPayrolls->findByPlannedEmployee(
            $startDate,
            $endDate,
            $userId
        );

        /** @var KpiPayroll $kpiPayroll */
        foreach ($kpiPayrolls as $kpiPayroll) {
            if (!isset($items[$kpiPayroll->getRecord()->getEmployee()->getId()])) {
                $items[$kpiPayroll->getRecord()->getEmployee()->getId()] = [
                    'employee' => [
                        'id' => $kpiPayroll->getRecord()->getEmployee()->getId(),
                        'name' => $kpiPayroll->getRecord()->getEmployee()->getName(),
                    ],
                    'calls' => 0,
                    'shifts' => 0,
                    'kpis' => 0,
                    'total' => 0,
                ];
            }

            $reward = $kpiPayroll->getAccrued();
            $items[$kpiPayroll->getRecord()->getEmployee()->getId()]['kpis'] += $reward;
            $items[$kpiPayroll->getRecord()->getEmployee()->getId()]['total'] += $reward;
            $kpisTotal += $reward;
            $total += $reward;
        }

        return $this->json([
            'total' => $total,
            'calls' => $callsTotal,
            'shifts' => $shiftsTotal,
            'kpis' => $kpisTotal,
        ]);
    }
}
