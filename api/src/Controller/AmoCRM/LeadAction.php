<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/lead', name: 'amo-crm_lead', methods: ["POST"])]
class LeadAction extends AbstractController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();
        file_put_contents(dirname(__DIR__) .'/../../var/test.txt', print_r($data, true), FILE_APPEND);
        return $this->json($data, Response::HTTP_OK);
    }
}
