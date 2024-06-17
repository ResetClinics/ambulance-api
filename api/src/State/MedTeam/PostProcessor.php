<?php

declare(strict_types=1);

namespace App\State\MedTeam;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\AdministratorReport;
use App\Entity\MediaObject;
use App\Repository\AdministratorReportRepository;
use App\Services\File\UploadedBase64File;
use Symfony\Component\HttpFoundation\RequestStack;

class PostProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly RequestStack       $requestStack,
        private readonly AdministratorReportRepository  $reports
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if ($data->getStatus() !== 'completed' && $data->getCompletedAt() !== null) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $request = $this->requestStack->getCurrentRequest();
        $requestData = $request->toArray();

        $mileageReceipts = $requestData['mileageReceipts'] ?? null;
        $parkingFeesReceipts = $requestData['parkingFeesReceipts'] ?? null;
        $tollRoadReceipts = $requestData['tollRoadReceipts'] ?? null;


        $mileage = $requestData['mileage'] ?? null;
        $parkingFees = $requestData['parkingFees'] ?? null;
        $tollRoad = $requestData['tollRoad'] ?? null;


        if (!$mileageReceipts && !$parkingFeesReceipts && !$tollRoadReceipts && !$mileage && !$parkingFees && !$tollRoad) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $report = new AdministratorReport();

        $report->setTeam($data);

        if ($mileageReceipts && is_array($mileageReceipts)){
            foreach ($mileageReceipts as $key => $mileageReceipt) {
                $image = new MediaObject();
                $image->base64content = $mileageReceipt['base64content'] ?? null;
                if ($image->base64content) {
                    $imageFile = new UploadedBase64File($image->base64content, "mileage_receipt-".$key.".png");
                    $image->file = $imageFile;
                    $report->addMileageReceipt($image);
                }
            }
        }


        if ($parkingFeesReceipts && is_array($parkingFeesReceipts)){
            foreach ($parkingFeesReceipts as $key => $parkingFeesReceipt) {
                $image = new MediaObject();
                $image->base64content = $parkingFeesReceipt['base64content'] ?? null;
                if ($image->base64content) {
                    $imageFile = new UploadedBase64File($image->base64content, "parking_fees_receipt-".$key.".png");
                    $image->file = $imageFile;
                    $report->addParkingFeesReceipt($image);
                }
            }
        }

        if ($tollRoadReceipts && is_array($tollRoadReceipts)){
            foreach ($tollRoadReceipts as $key => $tollRoadReceipt) {
                $image = new MediaObject();
                $image->base64content = $tollRoadReceipt['base64content'] ?? null;
                if ($image->base64content) {
                    $imageFile = new UploadedBase64File($image->base64content, "total_road_receipt-".$key.".png");
                    $image->file = $imageFile;
                    $report->addTollRoadReceipt($image);
                }
            }
        }

        $report->setMileage($mileage);
        $report->setParkingFees($parkingFees);
        $report->setToolRoad($tollRoad);


        $this->reports->save($report, true);

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
