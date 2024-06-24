<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use App\Repository\ClientRepository;
use App\Repository\PartnerRepository;
use App\Serializer\Call\CrmToAppDenormalizerInterface;
use App\Services\Call\CrmContactFetcher\CrmContactFetcherInterface;
use App\UseCase\Call\SendFromCrm\Command;
use App\UseCase\Call\SendFromCrm\Handler;
use App\UseCase\Call\SendFromCrm\Lead;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/amo-crm/web-hook-home-call', name: 'amo_crm.web_hook_home_call', methods: ["POST"])]
class WebHookHomeCallAction extends AbstractController
{
    public function __construct(
        private readonly CrmToAppDenormalizerInterface       $leadDenormalizer,
        private readonly CrmContactFetcherInterface          $contactFetcher,
        private readonly PartnerRepository                   $partners,
        private readonly ClientRepository                    $clients,
        private readonly ValidatorInterface $validator,
        private readonly Handler                             $handler,
        private readonly \App\UseCase\Partner\Create\Handler $partnerHandler,
        private readonly \App\UseCase\Client\Create\Handler $clientHandler
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
       $data = $request->request->all();

        try {
            $lead = $this->leadDenormalizer->denormalize($data, Lead::class);

            $violations = $this->validator->validate($lead);
            if (count($violations)) {
                return $this->json(null, Response::HTTP_OK);
            }

            if (!$lead->isPipelineHouseCall() || !$lead->isSuitableStatus()) {
                return $this->json(null, Response::HTTP_OK);
            }

            $partner = $this->partners->findOneByExternalId($lead->partnerExternalId);
            if (!$partner) {
                $partnerCommand = new \App\UseCase\Partner\Create\Command(
                    $lead->partnerName,
                    $lead->partnerExternalId
                );
                $this->partnerHandler->handle($partnerCommand);
            }

            $contact = $this->contactFetcher->fetch($lead->getId());

            $violations = $this->validator->validate($contact);
            if (count($violations)) {
                return $this->json(null, Response::HTTP_OK);
            }

            $client = $this->clients->findByPhone($contact->getPhone());

            if (!$client) {
                $clientCommand = new \App\UseCase\Client\Create\Command(
                    $contact->getName(),
                    $contact->getPhone(),
                );
                $this->clientHandler->handle($clientCommand);
            }

            $command = new Command($lead, $contact);
            $this->handler->handle($command);

        } catch (Exception $e) {
            return $this->json(null, Response::HTTP_OK);
        }

        return $this->json(null, Response::HTTP_OK);
    }
}
