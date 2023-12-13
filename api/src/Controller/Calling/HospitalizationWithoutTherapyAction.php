<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use AmoCRM\Models\NoteType\CommonNote;
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
class HospitalizationWithoutTherapyAction extends AbstractController
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
        $message .= $calling->getPrice() ? 'Стоимость госпитализации ' . $calling->getPrice() . PHP_EOL : '';
        $message .= $calling->getCoastHospital() ? 'Стоимость стационара ' . $calling->getCoastHospital() . PHP_EOL : '';
        $message .= $calling->getFio() ? 'ФИО пациента ' . $calling->getFio() . PHP_EOL : '';
        $message .= $calling->getAge() ? 'Возраст пациента ' . $calling->getAge() . PHP_EOL : '';
        $message .= $calling->getPassport() ? 'Паспорт ' . $calling->getPassport() . PHP_EOL : '';
        $message .= $calling->getCostDay() ? 'Стоимость в сутки ' . $calling->getCostDay() . PHP_EOL : '';
        $message .= $calling->getNote() ? 'Примечание ' . $calling->getNote() . PHP_EOL : '';

        $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));

        $entityId = null;

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $entityId = $lead->getId();
            $lead->setPipelineId(5000668);
            $lead->setStatusId(142);
            $lead->setName($currentDate->format('d.m.y') . ' ' . $calling->getFio());
            $lead->setPrice($calling->getPrice());
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

        $calling->setComplete(new DateTimeImmutable());
        $flusher->flush();

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );
        try {

            $this->addHospital($calling, $message);
            //$this->scheduler->schedule($calling);

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

    private function addHospital(Calling $calling, string $message): void
    {
        $lead = $this->client->leads()->getOne((int)$calling->getNumberCalling());

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter([(int)$calling->getNumberCalling()]);
        $allLinks = $linksService->get($filter);


        $contactId = null;
        /** @var LinkModel $link */
        foreach ($allLinks as $link) {
            if ($link->getMetadata()['main_contact']) {
                $contactId = $link->getToEntityId();
            }
        }

        if (!$contactId) {
            throw new NotFoundHttpException('Не найден контакт при создании стационара');
        }

        $customFieldsValues = new CustomFieldsValuesCollection();
        foreach ($lead->getCustomFieldsValues() as $customFieldsValue){
            //бригаду, админа и врача не переносим в повотор
            if (
                $customFieldsValue->getFieldId() === 875863 ||
                $customFieldsValue->getFieldId() === 873879 ||
                $customFieldsValue->getFieldId() === 873881
            ){
                continue;
            }
            $customFieldsValues->add($customFieldsValue);
        }

        $newLead = new LeadModel();
        $newLead->setName($lead->getName())
            ->setCreatedBy(0)
            ->setPrice($calling->getCoastHospital())
            ->setStatusId(38709310)
            ->setPipelineId(4093174)
            ->setResponsibleUserId($lead->getResponsibleUserId())
            ->setCustomFieldsValues($customFieldsValues)
            ->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setId($contactId)
                            ->setIsMain(true)
                    )
            );

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($newLead);

        $this->client->leads()->add($leadsCollection);
    }

}
