<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class FinishAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingSender $sender;

    public function __construct(
        AmoCRM        $amoCRM,
        CallingSender $sender
    )
    {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
    }

    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
