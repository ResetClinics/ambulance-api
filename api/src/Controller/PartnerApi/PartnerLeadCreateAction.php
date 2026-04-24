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

class PartnerLeadCreateAction extends AbstractController
{
    private const ALLOWED_TYPES = ['vnd', 'amb', 'stat'];

    public function __construct(
        private readonly Security $security,
        private readonly OneCAmbulanceApiClient $oneCAmbulanceApiClient,
    ) {}

    #[Route('/partner/createlead', name: 'partner-api.leads.create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            return $this->json(['error' => 'Partner not found'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $requestBody = $request->toArray();
        } catch (\JsonException) {
            return $this->json(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $phone = $this->stringValue($requestBody['phone'] ?? null);
        $type = $this->stringValue($requestBody['type'] ?? null);
        $fio = $this->nullableStringValue($requestBody['fio'] ?? '');
        $partnersComment = $this->nullableStringValue($requestBody['partners_comment'] ?? '');

        $errors = [];
        if ($phone === null || trim($phone) === '') {
            $errors['phone'] = 'Phone is required.';
        }

        if ($type === null || !\in_array($type, self::ALLOWED_TYPES, true)) {
            $errors['type'] = 'Type must be one of: vnd, amb, stat.';
        }

        if ($fio === null) {
            $errors['fio'] = 'Fio must be a string or empty value.';
        }

        if ($partnersComment === null) {
            $errors['partners_comment'] = 'Partners comment must be a string or empty value.';
        }

        if ($errors !== []) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payload = [
            'phone' => trim((string) $phone),
            'type' => (string) $type,
            'fio' => $fio ?? '',
            'partners_comment' => $partnersComment ?? '',
            'partners_id' => (string) $user->getPartnerId(),
        ];

        try {
            $result = $this->oneCAmbulanceApiClient->requestAndGetResponse(
                'createlead',
                [],
                'POST',
                $payload,
            );
        } catch (TransportExceptionInterface) {
            return $this->json(
                ['error' => 'OneC ambulance API request failed'],
                Response::HTTP_BAD_GATEWAY,
            );
        }

        return new JsonResponse($this->responseData($result['data'], $result['statusCode']), $result['statusCode']);
    }

    private function stringValue(mixed $value): ?string
    {
        if (\is_string($value) || \is_int($value) || \is_float($value)) {
            return (string) $value;
        }

        return null;
    }

    private function nullableStringValue(mixed $value): ?string
    {
        if ($value === null) {
            return '';
        }

        return \is_string($value) ? $value : null;
    }

    private function responseData(mixed $data, int $statusCode): mixed
    {
        if ($data !== '') {
            return $data;
        }

        if ($statusCode >= Response::HTTP_BAD_REQUEST) {
            return ['error' => 'OneC ambulance API returned an error'];
        }

        return ['success' => true];
    }
}
