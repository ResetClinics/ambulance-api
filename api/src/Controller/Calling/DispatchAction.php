<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Entity\User\User;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\AmoCRM;
use App\Services\WSClient;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DispatchAction extends AbstractController
{

    private AmoCRMApiClient $client;
    public function __construct(
        AmoCRM                             $amoCRM,
        private readonly WSClient $wsClient,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Calling $calling, TeamRepository $teams, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(38187418);
        }

        $this->client->leads()->update($leads);


        $calling->setDispatched(new DateTimeImmutable());

        $flusher->flush();

        $this->wsClient->sendUpdateOffer($calling->getId());

        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
