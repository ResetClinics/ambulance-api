<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/set-team', name: 'amo-crm_set-team', methods: ["POST"])]
class SetTeamAction extends AbstractController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();

        file_put_contents(
            dirname(__DIR__) . '/../../var/team-json.txt',
            print_r(json_encode($data, JSON_THROW_ON_ERROR) . PHP_EOL, true),
            FILE_APPEND
        );
        file_put_contents(
            dirname(__DIR__) . '/../../var/team-array.txt',
            print_r($data, true),
            FILE_APPEND)
        ;
        return $this->json(null, Response::HTTP_OK);
    }
}
