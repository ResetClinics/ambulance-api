<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\Payroll\KpiDocument\KpiPayroll;
use App\Repository\Payroll\KpiDocument\KpiPayrollRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserPayrollKPIReport extends AbstractController
{
    public function __construct(
        private readonly KpiPayrollRepository $kpiPayrolls,
    ) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/{id}/payroll-report/kpis', name: 'api_user_payroll_report_kpi', methods: 'GET', priority: 10)]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $shiftPayrolls = $this->kpiPayrolls->findByPlannedEmployee(
            new DateTimeImmutable('2024-12-01T00:00:00.000Z'),
            new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
            $id
        );
        $items = [];
        $total = 0;

        /** @var KpiPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            $items[$shiftPayroll->getId()] = [
                'id' => $shiftPayroll->getId(),
                'name' => $shiftPayroll->getCalculator()->getName(),
                'base' => $shiftPayroll->getOriginal(),
                'kpi' => $shiftPayroll->getKpi(),
                'accrued' => $shiftPayroll->getAccrued(),
            ];
            $total += $shiftPayroll->getAccrued();
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
        ]);
    }
}
