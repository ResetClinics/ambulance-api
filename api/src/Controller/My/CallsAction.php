<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\CallPayroll;
use App\Entity\Payroll\ServicePayroll;
use App\Repository\CallingRepository;
use App\Repository\Payroll\CallPayrollRepository;
use App\Repository\Payroll\ServicePayrollRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/calls', name: 'api_my_calls', methods: ['GET'])]
class CallsAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly CallPayrollRepository $callPayrolls,
        private readonly ServicePayrollRepository $servicePayrolls
    )
    {
    }

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $userId = $this->getUser()?->getId();
        if (!$userId) {
            throw new \DomainException('User not found');
        }

        $startOfMonth = new DateTimeImmutable('first day of this month 00:00:00');
        $endOfMonth = new DateTimeImmutable('first day of next month 00:00:00');

        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        $items = [];
        $total = 0;

        $callsItems = [];

        /** @var Calling $call */
        foreach ($calls as $call) {
            $callsItems[$call->getId()] = [
                'id' => $call->getId(),
                'completedAt' => $call->getCompletedAt()->format('d.m.Y H:i'),
                'completedDate' => $call->getCompletedAt()->format('d.m.Y'),
                'admin' => $call->getAdmin() ? [
                    'id' => $call->getAdmin()->getId(),
                    'name' => $call->getAdmin()->getName(),
                ] : null,
                'doctor' => $call->getDoctor() ? [
                    'id' => $call->getDoctor()->getId(),
                    'name' => $call->getDoctor()->getName(),
                ] : null,
                'callId' => $call->getId(),
                'name' => $call->getAddress(),
                'amount' => '',
                'reward' => 0,
                'subRows' => [],
            ];
        }

        $servicePayrolls = $this->servicePayrolls->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        /** @var ServicePayroll $servicePayroll */
        foreach ($servicePayrolls as $servicePayroll) {
            $reward = (float)($servicePayroll->getAccrued()->amount / 100);

            $callsItems[$servicePayroll->getCallService()->getCalling()->getId()]['reward'] += $reward;

            $callsItems[$servicePayroll->getCallService()->getCalling()->getId()]['subRows'][] = [
                'name' => $servicePayroll->getCallService()->getService()->getName(),
                'amount' => $servicePayroll->getCallService()->getPrice(),
                'reward' => $reward,
            ];
        }

        $callPayrolls = $this->callPayrolls->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        /** @var CallPayroll $callPayroll */
        foreach ($callPayrolls as $callPayroll) {
            if (!isset($callsItems[$callPayroll->getCall()->getId()])) {
                $call = $this->calls->getById($callPayroll->getCall()->getId());

                $callsItems[$call->getId()] = [
                    'id' => $call->getId(),
                    'completedAt' => $call->getCompletedAt()->format('d.m.Y H:i'),
                    'completedDate' => $call->getCompletedAt()->format('d.m.Y'),
                    'admin' => $call->getAdmin() ? [
                        'id' => $call->getAdmin()->getId(),
                        'name' => $call->getAdmin()->getName(),
                    ] : null,
                    'doctor' => $call->getDoctor() ? [
                        'id' => $call->getDoctor()->getId(),
                        'name' => $call->getDoctor()->getName(),
                    ] : null,
                    'callId' => $call->getId(),
                    'name' => $call->getAddress(),
                    'amount' => '',
                    'reward' => 0,
                    'subRows' => [],
                ];
            }
            $reward = (float)($callPayroll->getAccrued()->amount / 100);

            $callsItems[$callPayroll->getCall()->getId()]['reward'] += $reward;

            $callsItems[$callPayroll->getCall()->getId()]['subRows'][] = [
                'name' => $callPayroll->getCalculator()->getName(),
                'amount' => '',
                'reward' => $reward,
            ];
        }

        foreach ($callsItems as $callItem) {
            if (!isset($items[$callItem['completedDate']])) {
                $items[$callItem['completedDate']] = [
                    'name' => $callItem['completedDate'],
                    'amount' => '',
                    'reward' => 0,
                    'subRows' => [],
                ];
            }

            $items[$callItem['completedDate']]['reward'] += $callItem['reward'];

            $items[$callItem['completedDate']]['subRows'][] = $callItem;
            $total += $callItem['reward'];
        }

        return $this->json([
            'items' => array_values($items),
            'total' => $total,
        ]);
    }
}
