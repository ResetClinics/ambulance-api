<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/uploads/{filename}', name: 'amo-crm_test', methods: ["GET"])]
class ImageAction extends AbstractController
{

    public function __invoke(string $filename, Request $request): BinaryFileResponse
    {
        $imageFile = '/app/uploads/' . $filename;

        return $this->file($imageFile, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
