<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Entity\Calling\CallingArriveDto;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\WSClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class StartTreatmentAction extends AbstractController
{


    public function __construct(
        private readonly WSClient $wsClient,
    )
    {}

    public function __invoke(
        Calling           $calling,
        CallingArriveDto  $dto,
        TeamRepository    $teams,
        CallingRepository $callings,
        Flusher           $flusher): JsonResponse
    {

        $calling->setStatus(Status::treating());

        $flusher->flush();

        $this->wsClient->sendUpdateOffer($calling->getId());

        return $this->json([
            "id" => $calling->getId(),
            "title" => $calling->getTitle(),
            "name" => $calling->getName(),
            "phone" => '+70000000000',
            "fio" => $calling->getFio(),
            "numberCalling" => $calling->getNumberCalling(),
            "address" => $calling->getAddress(),
            "status" => $calling->getStatus(),
            "description" => $calling->getDescription(),
            "chronicDiseases" => $calling->getChronicDiseases(),
            "nosology" => $calling->getNosology(),
            "age" => $calling->getAge(),
            "leadType" => $calling->getLeadType(),
            "partnerName" => $calling->getPartnerName(),
            "sendPhone" => $calling->isSendPhone(),
            "rejectedComment" => $calling->getRejectedComment(),
            "createdAt" => $calling->getCreatedAt()?->format('d.m.Y H:i'),
            "updatedAt" => $calling->getUpdatedAt()?->format('d.m.Y H:i'),
            "acceptedAt" => $calling->getAcceptedAt()?->format('d.m.Y H:i'),
            "dispatchedAt" => $calling->getDispatchedAt()?->format('d.m.Y H:i'),
            "arrivedAt" => $calling->getArrivedAt()?->format('d.m.Y H:i'),
            "completedAt" => $calling->getCompletedAt()?->format('d.m.Y H:i'),
            "dateTime" => $calling->getDateTime(),
            "admin" => $calling?->getAdmin() ?: [
                'id' => $calling->getAdmin()->getId(),
                'phone' => $calling->getAdmin()->getPhone(),
                'name' => $calling->getAdmin()->getName(),
            ],
            "doctor" => $calling?->getDoctor() ?: [
                'id' => $calling->getDoctor()->getId(),
                'phone' => $calling->getDoctor()->getPhone(),
                'name' => $calling->getDoctor()->getName(),
            ],
            "price" => $calling->getPrice(),
            "estimated" => $calling->GetEstimated(),
            "prepayment" => $calling->getPrepayment(),
            "note" => $calling->getNote(),
            "passport" => $calling->getPassport(),
            "coastHospitalAdmission" => $calling->getCoastHospitalAdmission(),
            "coastHospital" => $calling->getCoastHospital(),
            "costDay" => $calling->getCostDay(),
            "phoneRelatives" => $calling->getPhoneRelatives(),
            "resultDate" => $calling->getResultDate(),
            "resultTime" => $calling->getResultTime(),
            "partner" => $calling->getPartner() ?: [
                "id" => $calling->getPartner()->getId(),
                "name" => $calling->getPartner()->getName(),
                "whatsappGroup" => $calling->getPartner()->getWhatsappGroup(),
            ],
            "lon" => $calling->getLon(),
            "lat" => $calling->getLat(),
            "services" => [],
            "amount" => $calling->getAmount(),
            "paymentNextOrder" => $calling->getPaymentNextOrder(),
            "paymentHospitalization" => $calling->getPaymentHospitalization(),
            "totalAmount" => $calling->getTotalAmount(),
            "mkadDistance" => $calling->getMkadDistance(),
            "ownerExternalId" => $calling->getOwnerExternalId(),
            "operator" => null,
            "client" => $calling->getClient() ?: [
                'id' => $calling->getClient()->getId(),
                'phone' => $calling->getClient()->getPhone(),
                'name' => $calling->getClient()->getName(),
            ],
            "noBusinessCards" => $calling->isCurrentNoBusinessCards(),
            "partnerHospitalization" => $calling->isCurrentPartnerHospitalization(),
            "images" => [],
            "addressInfo" => $calling->getAddressInfo(),
            "team" => $calling->getTeam() ?: [
                'id' => $calling->getTeam()->getId(),
                "status" => $calling->getTeam()->getStatus(),
                "phone" => $calling->getTeam()->getPhone() ?: [
                    'id' => $calling->getTeam()->getPhone()->getId(),
                    "externalId" => $calling->getTeam()->getPhone()->getExternalId(),
                ],
            ],
            "finishedAt" => $calling->getFinishedAt()?->format('d.m.Y H:i'),
            "repeat" => $calling->getCountRepeat(),
            'statusLabel' => $calling->getStatusLabel(),
        ], Response::HTTP_ACCEPTED);
    }
}
