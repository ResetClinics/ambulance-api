<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Entity\Partner\PartnerUser;
use App\Services\AmbulanceApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PartnerCallsAction extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly AmbulanceApiClient $ambulanceApiClient,
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/partner/calls', name: 'partner-api.calls.index', methods: ['GET'])]
    public function calls(Request $request): JsonResponse
    {
        /** @var PartnerUser $user */
        $user = $this->security->getUser();
        $partner = $user->getPartner();

        if (!$partner) {
            return $this->json(['error' => 'Partner not found'], 400);
        }

        // Собираем параметры запроса
        $queryParams = $request->query->all();
        
        // Добавляем обязательный параметр partner
        $queryParams['partner'] = $partner->getId();
        
        // Устанавливаем обязательные параметры с значениями по умолчанию
        $queryParams['page'] = $request->query->getInt('page', 1);
        
        // Переименовываем per_page в perPage для внешнего API
        if (isset($queryParams['per_page'])) {
            $queryParams['perPage'] = (int) $queryParams['per_page'];
            unset($queryParams['per_page']);
        } else {
            $queryParams['perPage'] = 20;
        }

        // Выполняем запрос к внешнему API через сервис
        $result = $this->ambulanceApiClient->requestAndGetResponse('calls', $queryParams);

        return new JsonResponse($result['data'], $result['statusCode']);
    }
}
