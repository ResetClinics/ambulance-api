<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Security\PartnerUserIdentity;
use App\Services\OneCAmbulanceApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PartnerLeadsAction extends AbstractController
{
    private const DEFAULT_PER_PAGE = 15;

    private const SUPPORTED_QUERY_PARAMS = [
        'status',
        'completedAt_after',
        'completedAt_before',
    ];

    public function __construct(
        private readonly Security $security,
        private readonly OneCAmbulanceApiClient $oneCAmbulanceApiClient,
        private readonly bool $amountsEnabled,
    ) {}

    #[Route('/partner/leads', name: 'partner-api.leads.index', methods: ['GET'])]
    public function leads(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            return $this->json(['error' => 'Partner not found'], Response::HTTP_BAD_REQUEST);
        }

        $queryParams = [
            'partner' => $user->getPartnerId(),
            'page' => $request->query->getInt('page', 1),
            'perPage' => $request->query->getInt('perPage', self::DEFAULT_PER_PAGE),
            'amounts' => $request->query->has('amounts')
                ? $request->query->get('amounts')
                : ($this->amountsEnabled ? 'true' : 'false'),
        ];

        // Only forward the 1C leads contract. Unsupported params such as "search" are ignored.
        foreach (self::SUPPORTED_QUERY_PARAMS as $key) {
            $value = $request->query->get($key);
            if ($value !== null && $value !== '') {
                $queryParams[$key] = $value;
            }
        }

        try {
            $result = $this->oneCAmbulanceApiClient->requestAndGetResponse('leads', $queryParams);
        } catch (TransportExceptionInterface) {
            return $this->json(
                ['error' => 'OneC ambulance API request failed'],
                Response::HTTP_BAD_GATEWAY,
            );
        }

        return new JsonResponse($result['data'], $result['statusCode']);
    }
}
