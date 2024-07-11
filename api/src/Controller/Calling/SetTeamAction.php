<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\AmoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SetTeamAction extends AbstractController
{

    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM                    $amoCRM,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Calling $call, TeamRepository $teams, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        $filter = new LeadsFilter();
        $filter->setIds([$call->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(38874646);
        }

        $this->client->leads()->update($leads);
        return $this->json($call, Response::HTTP_ACCEPTED);
    }
}
