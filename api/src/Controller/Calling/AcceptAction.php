<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Entity\User\User;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\AmoCRM;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AcceptAction extends AbstractController
{

    private AmoCRMApiClient $client;
    public function __construct(
        AmoCRM                             $amoCRM
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Calling $calling, TeamRepository $teams, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($calling->getAdmin()?->getId() !== $user->getId())
        {
            throw new DomainException('Принять вызов может только администратор');
        }

        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(38187418);
        }


        try {
            $this->client->leads()->update($leads);
        } catch (AmoCRMApiException $e) {
            throw new DomainException(json_encode($this->client->leads()->getLastRequestInfo()));
        }

        $calling->setAccepted(new DateTimeImmutable());

        $flusher->flush();
        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
