<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Entity\MedTeam\MedTeam;
use App\Repository\CallingRepository;
use App\Repository\MedTeam\MedTeamRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/shifts', name: 'api_my_shifts', methods: ['GET'])]
class ShiftsAction extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $shifts,
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
        $endOfMonth = new DateTimeImmutable('last day of this month 23:59:59');

        $shifts = $this->shifts->findByPlannedEmployee(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        return $this->json(array_map(function (MedTeam $shift) {
            return [
                'id' => $shift->getId(),
                'startAt' => $shift->getPlannedStartAt()->format('d.m.Y H:i'),
                'finishAt' => $shift->getPlannedFinishAt()->format('d.m.Y H:i'),
                'status' => $shift->getStatus()->value,
            ];
        }, $shifts));
    }
}
