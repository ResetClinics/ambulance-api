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
class RejectAction extends AbstractController
{
    public function __invoke(Calling $calling,TeamRepository $teams, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        $calling->setReject(new DateTimeImmutable(), $calling->getRejectedComment());

        $flusher->flush();
        return $this->json(null, Response::HTTP_ACCEPTED);
    }
}
