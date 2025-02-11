<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\KpiDocument\KpiPayrollRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserPayrollReport extends AbstractController
{
    public function __construct(
        private readonly CallPayrollRepository $callPayrolls,
        private readonly ServicePayrollRepository $servicePayrolls,
        private readonly ShiftPayrollRepository $shiftPayrolls,
        private readonly KpiPayrollRepository $kpiPayrolls,
    ) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/payroll-report', name: 'api_users_payroll_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $startDate = $request->query->get('startDate', '2024-12-01T00:00:00.000Z');
        $endDate = $request->query->get('endDate', '2025-01-01T00:00:00.000Z');

        $servicePayrolls = $this->callPayrolls->findByAccruedAt(
            new DateTimeImmutable($startDate),
            new DateTimeImmutable($endDate),
        );

        $items = [];
        $total = 0;
        $callsTotal = 0;
        $shiftsTotal = 0;
        $kpisTotal = 0;

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            if (!isset($items[$servicePayroll->getEmployee()->getId()])) {
                $items[$servicePayroll->getEmployee()->getId()] = [
                    'employee' => [
                        'id' => $servicePayroll->getEmployee()->getId(),
                        'name' => $servicePayroll->getEmployee()->getName(),
                    ],
                    'calls' => 0,
                    'shifts' => 0,
                    'kpis' => 0,
                    'total' => 0,
                ];
            }

            $reward = (float)($servicePayroll->getAccrued()->amount / 100);
            $items[$servicePayroll->getEmployee()->getId()]['calls'] += $reward;
            $items[$servicePayroll->getEmployee()->getId()]['total'] += $reward;
            $callsTotal += $reward;
            $total += $reward;
        }

        $servicePayrolls = $this->servicePayrolls->findByAccruedAt(
            new DateTimeImmutable('2024-12-01T00:00:00.000Z'),
            new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
        );

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            if (!isset($items[$servicePayroll->getEmployee()->getId()])) {
                $items[$servicePayroll->getEmployee()->getId()] = [
                    'employee' => [
                        'id' => $servicePayroll->getEmployee()->getId(),
                        'name' => $servicePayroll->getEmployee()->getName(),
                    ],
                    'calls' => 0,
                    'shifts' => 0,
                    'kpis' => 0,
                    'total' => 0,
                ];
            }

            $reward = (float)($servicePayroll->getAccrued()->amount / 100);
            $items[$servicePayroll->getEmployee()->getId()]['calls'] += $reward;
            $items[$servicePayroll->getEmployee()->getId()]['total'] += $reward;
            $callsTotal += $reward;
            $total += $reward;
        }

        $shiftPayrolls = $this->shiftPayrolls->findByAccruedAt(
            new DateTimeImmutable('2024-12-01T00:00:00.000Z'),
            new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            if (!isset($items[$shiftPayroll->getEmployee()->getId()])) {
                $items[$shiftPayroll->getEmployee()->getId()] = [
                    'employee' => [
                        'id' => $shiftPayroll->getEmployee()->getId(),
                        'name' => $shiftPayroll->getEmployee()->getName(),
                    ],
                    'calls' => 0,
                    'shifts' => 0,
                    'kpis' => 0,
                    'total' => 0,
                ];
            }

            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);
            $items[$shiftPayroll->getEmployee()->getId()]['shifts'] += $reward;
            $items[$shiftPayroll->getEmployee()->getId()]['total'] += $reward;
            $shiftsTotal += $reward;
            $total += $reward;
        }

        $kpiPayrolls = $this->kpiPayrolls->findByAccruedAt(
            new DateTimeImmutable('2024-12-01T00:00:00.000Z'),
            new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
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

        $items = array_values($items);

        usort($items, static fn ($a, $b) => strcmp($a['employee']['name'], $b['employee']['name']));

        return $this->json([
            'items' => $items,
            'total' => $total,
            'shifts' => $shiftsTotal,
            'calls' => $callsTotal,
            'kpis' => $kpisTotal,
        ]);
    }
}
