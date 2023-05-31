<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\ExtendedServiceMessageNote;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\HospitalizationScheduler;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class HospitalizationAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingSender $sender;
    private HospitalizationScheduler $scheduler;

    public function __construct(
        AmoCRM        $amoCRM,
        CallingSender $sender,
        HospitalizationScheduler        $scheduler,
    )
    {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
        $this->scheduler = $scheduler;
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     */
    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }


        $message = 'Информация от бригады' . PHP_EOL;
        $message .= 'Заявка №' . $calling->getNumberCalling() . PHP_EOL;
        $message .= $calling->getPrice() ? 'Итоговая цена ' . $calling->getPrice() . PHP_EOL : '';
        $message .= $calling->getCoastHospital() ? 'Стоимость госпитализации ' . $calling->getCoastHospital() . PHP_EOL : '';
        $message .= $calling->getName() ? 'ФИО пациента ' . $calling->getName() . PHP_EOL : '';
        $message .= $calling->getAge() ? 'Возраст пациента ' . $calling->getAge() . PHP_EOL : '';
        $message .= $calling->getPassport() ? 'Паспорт ' . $calling->getPassport() . PHP_EOL : '';
        $message .= $calling->getCostDay() ? 'Стоимость в сутки ' . $calling->getCostDay() . PHP_EOL : '';
        $message .= $calling->getNote() ? 'Примечание ' . $calling->getNote() . PHP_EOL : '';

        $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));

        $entityId = null;

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $entityId = $lead->getId();
            $lead->setStatusId(45084664);
            $lead->setName($currentDate->format('d.m.y') . ' ' . $calling->getName());
            $lead->setPrice($calling->getPrice());
        }

        $this->client->leads()->update($leads);

        $notesCollection = new NotesCollection();
        $serviceMessageNote = new ExtendedServiceMessageNote();
        $serviceMessageNote->setEntityId($entityId)
            ->setText($message)
            ->setService('Выездное приложение')
            ->setCreatedBy(0);

        $notesCollection->add($serviceMessageNote);

        try {
            $leadNotesService = $this->client->notes(EntityTypesInterface::LEADS);
            $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
        }



        $calling->setComplete(new DateTimeImmutable());
        $flusher->flush();

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );
        try {

            $this->scheduler->schedule($calling);

            $this->sender->sendToAdmin(
                $calling,
                'Вызов N ' . $calling->getNumberCalling(),
                'Создано назначение на стационар'
            );

        } catch (Exception $exception) {
            throw new DomainException('Ошибка создания госпитализации в AmoCRM: ' . $exception->getMessage());
        }

        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
