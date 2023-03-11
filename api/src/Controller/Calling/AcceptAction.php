<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Entity\User\User;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AcceptAction extends AbstractController
{
    public function __invoke(Calling $calling, TeamRepository $teams, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($calling->getTeam()?->getAdministrator()->getId() !== $user->getId())
        {
            throw new DomainException('Принять вызов может только администратор');
        }

        $calling->setAccepted(new DateTimeImmutable());

        $flusher->flush();
        return $this->json(null, Response::HTTP_ACCEPTED);
    }
}
