<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Repository\CallingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PartnerCallsAction extends AbstractController
{

    public function __construct(
        private readonly Security $security,
        private readonly CallingRepository $calls
    )
    {
    }

    #[Route('/partner/calls', name: 'partner-api.calls.index', methods: ["GET"])]
    public function version(): JsonResponse
    {
        $user = $this->security->getUser();

        return $this->json([]);
    }
}
