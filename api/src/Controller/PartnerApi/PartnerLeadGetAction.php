<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Security\PartnerUserIdentity;
use App\Services\OneCAmbulanceApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PartnerLeadGetAction extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly OneCAmbulanceApiClient $oneCAmbulanceApiClient,
    ) {}

    #[Route('/partner/lead/{number}', name: 'partner-api.leads.get', methods: ['GET'])]
    public function lead(string $number): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            return $this->json(['error' => 'Partner not found'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->oneCAmbulanceApiClient->requestAndGetResponse(
                'lead/' . rawurlencode($number),
                ['partner' => $user->getPartnerId()],
            );
        } catch (TransportExceptionInterface) {
            return $this->json(
                ['error' => 'OneC ambulance API request failed'],
                Response::HTTP_BAD_GATEWAY,
            );
        }

        $data = $result['data'];
        if ($this->isListResponse($data)) {
            $lead = $this->findLeadByNumber($data['items'], $number);
            if ($lead === null) {
                return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse($lead, Response::HTTP_OK);
        }

        return new JsonResponse($data, $result['statusCode']);
    }

    private function isListResponse(mixed $data): bool
    {
        return \is_array($data)
            && \array_key_exists('items', $data)
            && \is_array($data['items'])
            && \array_key_exists('pagination', $data);
    }

    /**
     * @param array<int|string, mixed> $items
     *
     * @return array<string, mixed>|null
     */
    private function findLeadByNumber(array $items, string $number): ?array
    {
        foreach ($items as $item) {
            if (!\is_array($item)) {
                continue;
            }

            if ((string) ($item['number'] ?? '') === $number) {
                return $item;
            }
        }

        return null;
    }
}
