<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

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
use function count;

#[Route('/api/amo-crm/web-hook-home-call', name: 'amo_crm.web_hook_home_call', methods: ["POST"])]
class WebHookHomeCallAction extends AbstractController
{
    public function __construct(
        private readonly CrmToAppDenormalizerInterface       $leadDenormalizer,
        private readonly CrmContactFetcherInterface          $contactFetcher,
        private readonly PartnerRepository                   $partners,
        private readonly ValidatorInterface $validator,
        private readonly Handler                             $handler,
        private readonly \App\UseCase\Partner\Create\Handler $partnerHandler,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();

        file_put_contents(
            dirname(__DIR__) . '/../../var/hook-home-call-' . date("Y-m-d H:i:s") . '.txt',
            print_r($data, true).PHP_EOL,
            FILE_APPEND);


        try {
            $lead = $this->leadDenormalizer->denormalize($data, Lead::class);
            file_put_contents(
                dirname(__DIR__) . '/../../var/hook-home-call-lead.txt',
                print_r($lead, true).PHP_EOL,
                FILE_APPEND);
            $violations = $this->validator->validate($lead);
            if (count($violations)) {
                file_put_contents(
                    dirname(__DIR__) . '/../../var/hook-home-call-validate-lead.txt',
                    print_r($violations, true).PHP_EOL,
                    FILE_APPEND);
                return $this->json(null, Response::HTTP_OK);
            }

            if (!$lead->isPipelineHouseCall() || !$lead->isSuitableStatus()) {
                return $this->json(null, Response::HTTP_OK);
            }

            $contact = $this->contactFetcher->fetch($lead->mainContactId);

            $violations = $this->validator->validate($contact);
            if (count($violations)) {
                file_put_contents(
                    dirname(__DIR__) . '/../../var/hook-home-call-validate-contact.txt',
                    print_r($violations, true).PHP_EOL,
                    FILE_APPEND);
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

            $command = new Command($lead, $contact);
            $this->handler->handle($command);

        } catch (Exception $e) {
            file_put_contents(
                dirname(__DIR__) . '/../../var/hook-home-call-exception-' . date("Y-m-d H:i:s") . '.txt',
                print_r($e->getMessage(), true).PHP_EOL,
                FILE_APPEND);

            return $this->json(null, Response::HTTP_OK);
        }

        return $this->json(null, Response::HTTP_OK);
    }
}
