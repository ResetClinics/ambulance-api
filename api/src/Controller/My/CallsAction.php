<?php

declare(strict_types=1);

namespace App\Controller\My;

use App\Entity\Calling\Calling;
use App\Repository\CallingRepository;
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

        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $startOfMonth,
            $endOfMonth,
            $userId
        );

        return $this->json(array_map(function (Calling $call) {
            return [
                'id' => $call->getId(),
                'date' => $call->getCompletedAt()?->format('d.m.Y H:i'),
                'address' => $call->getAddress(),
                'price' => $call->getPrice(),
                'status' => $call->getStatus()->value,
            ];
        }, $calls));
    }
}
