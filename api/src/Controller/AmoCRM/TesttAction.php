<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Models\LeadModel;
use App\Repository\AmoCrmTokenRepository;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/testt', name: 'amo-crm_testt', methods: ["GET"])]
class TesttAction extends AbstractController
{

    private AmoCRMApiClient $client;

    public function __construct(
        AmoCrmTokenRepository $tokens

    )
    {
        $apiClient = new AmoCRMApiClient(
            'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
            'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
            'https://ambulance.rc-respect.ru/api/amo-crm/auth/callback'
        );

        $token = $tokens->getToken();

        $apiClient->setAccessToken($token)
            ->setAccountBaseDomain('af4040148.amocrm.ru')
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) use ($tokens) {
                    $tokens->update($accessToken, $baseDomain);
                }
            );

        $this->client = $apiClient;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $lead = $this->client->leads()->getOne(20481239, [LeadModel::CONTACTS, LeadModel::CATALOG_ELEMENTS]);

        dump($lead);

        $contact = $this->client->contacts()->getOne(26095592);
        dd($contact);


        return $this->json([], Response::HTTP_OK);
    }
}
