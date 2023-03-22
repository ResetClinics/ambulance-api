<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/auth/callback', name: 'amo-crm_auth-callback', methods: ["GET"])]
class AuthCallbackAction extends AbstractController
{

    public function __invoke(Request $request): JsonResponse
    {
        $apiClient = new AmoCRMApiClient(
            'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
            'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
            'https://ambulance.rc-respect.ru/api/amo-crm/auth/callback'
        );

        $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($request->get('code'));
        dd($accessToken);
        return $this->json([], Response::HTTP_OK);
    }
}
