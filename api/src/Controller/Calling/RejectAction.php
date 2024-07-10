<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\WSClient;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class RejectAction extends AbstractController
{
    public function __construct(
        AmoCRM        $amoCRM,
        CallingSender $sender,
        private readonly WSClient $wsClient,
    )
    {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws \Exception
     */
    public function __invoke(Calling $calling, TeamRepository $teams, CallingRepository $callings, Flusher $flusher): JsonResponse
    {

        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }


        $message = 'Информация от бригады' . PHP_EOL;
        $message .= 'Отмена заявки №' . $calling->getNumberCalling() . PHP_EOL;
        $message .= $calling->getRejectedComment() ? 'Причина отмены ' . $calling->getRejectedComment() . PHP_EOL : '';


        $entityId = null;
        $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));
        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $entityId = $lead->getId();
            $lead->setStatusId(143);
            $lead->setName('Неуспех ' . $currentDate->format('d.m.y') . ' ' . $calling->getName());
        }

        $this->client->leads()->update($leads);

        $notesCollection = new NotesCollection();
        $messageNote = new CommonNote();
        $messageNote->setEntityId($entityId)
            ->setText($message)
            ->setCreatedBy(0);

        $notesCollection->add($messageNote);

        try {
            $leadNotesService = $this->client->notes(EntityTypesInterface::LEADS);
            $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
        }

        $calling->setReject(new DateTimeImmutable(), $calling->getRejectedComment());

        $flusher->flush();

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' отменен',
            'Спасибо за информацию!'
        );

        $this->wsClient->sendUpdateOffer($calling->getId());

        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
