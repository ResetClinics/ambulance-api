<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/test', name: 'amo-crm_test', methods: ["GET"])]
class TestAction extends AbstractController
{

    public function __invoke(Request $request)
    {
      // $apiClient = new \AmoCRM\Client\AmoCRMApiClient(
      //     'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
      //     'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
      //     'https://ambulance.rc-respect.ru/api/amo-crm/auth/callback'
      // );
      // $fff = $apiClient->getOAuthClient()->getOAuthButton(
      //     [
      //         'title' => 'Установить интеграцию',
      //         'compact' => true,
      //         'class_name' => 'className',
      //         'color' => 'default',
      //         'error_callback' => 'handleOauthError',
      //     ]
      // );

      // echo $fff;



      // dd($fff);

        $filename = '1.png';
        $imageFile = '/app/uploads/' . $filename;

        return $this->file($imageFile, $filename, ResponseHeaderBag::DISPOSITION_INLINE);

        //$token = new \League\OAuth2\Client\Token\AccessToken();

        //return $this->json([], Response::HTTP_OK);
    }
}
