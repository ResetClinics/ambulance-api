<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\MedTeam\MedTeam;
use App\Entity\Payroll\ShiftPayroll;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\Payroll\ShiftPayrollRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserPayrollShiftsReport extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $shifts,
        private readonly ShiftPayrollRepository $shiftPayrolls,
    ) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/{id}/payroll-report/shifts', name: 'api_user_payroll_report_shifts', methods: 'GET', priority: 10)]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $shifts = $this->shifts->findByPlannedEmployee(
            new DateTimeImmutable('2024-11-30T00:00:00.000Z'),
            new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
            $id
        );

        $items = [];
        $total = 0;

        /** @var MedTeam $shift */
        foreach ($shifts as $shift) {
            $items[$shift->getId()] = [
                'id' => $shift->getId(),
                'startedAt' => $shift->getPlannedStartAt()->format('d.m.Y H:i'),
                'finishedAt' => $shift->getPlannedFinishAt()->format('d.m.Y H:i'),
                'car' => $shift->getCar() ? [
                    'id' => $shift->getCar()->getId(),
                    'name' => $shift->getCar()->getName(),
                ] : null,
                'admin' => $shift->getAdmin() ? [
                    'id' => $shift->getAdmin()->getId(),
                    'name' => $shift->getAdmin()->getName(),
                ] : null,
                'doctor' => $shift->getDoctor() ? [
                    'id' => $shift->getDoctor()->getId(),
                    'name' => $shift->getDoctor()->getName(),
                ] : null,
                'name' => $shift->getPlannedStartAt()->format('d.m.Y'),
                'amount' => '',
                'reward' => 0,
                'subRows' => [],
            ];
        }

        $shiftPayrolls = $this->shiftPayrolls->findByPlannedEmployee(
            new DateTimeImmutable('2024-12-01T00:00:00.000Z'),
            new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
            $id
        );

        /** @var ShiftPayroll $shiftPayroll */
        foreach ($shiftPayrolls as $shiftPayroll) {
            $reward = (float)($shiftPayroll->getAccrued()->amount / 100);

            $items[$shiftPayroll->getShift()->getId()]['reward'] += $reward;
            $total += $reward;

            $items[$shiftPayroll->getShift()->getId()]['subRows'][] = [
                'name' => $shiftPayroll->getCalculator()->getName(),
                'amount' => $shiftPayroll->getAmount(),
                'reward' => $reward,
            ];
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
        ]);
    }
}
