<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Entity\Calling\CallingArriveDto;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\AmoCRM;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ArriveAction extends AbstractController
{
    private AmoCRMApiClient $client;
    public function __construct(
        AmoCRM                             $amoCRM
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(
        Calling $calling,
        CallingArriveDto $dto,
        TeamRepository $teams,
        CallingRepository $callings,
        Flusher $flusher): JsonResponse
    {

        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(62358398);
        }

        $this->client->leads()->update($leads);


        $calling->setFio($dto->fio);
        $calling->setPassport($dto->passport);
        $calling->setAge($dto->age);

        $calling->setArrived(new DateTimeImmutable());

        $flusher->flush();
        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
