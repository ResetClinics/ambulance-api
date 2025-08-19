<?php

namespace App\Controller\Partner;

use App\UseCase\Partner\Create\Command;
use App\UseCase\Partner\Create\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class CreatePartner extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Handler $handler
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var Command $command */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command::class,
            'json'
        );

        $this->handler->handle($command);

        return $this->json(null, Response::HTTP_CREATED);
    }
}