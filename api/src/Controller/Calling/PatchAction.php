<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\DateTimeCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateTimeCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateTimeCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Asterisk\UseCase\Channel as AsteriskChannel;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Row;
use App\Entity\Calling\Status;
use App\Entity\User\User;
use App\EventListener\CallPreDenormalizeListener;
use App\Flusher;
use App\Services\AmoCRM;
use App\Services\ATS\BlacklistService\McnBlacklistService;
use App\Services\Call\OperatorReward;
use App\Services\Call\PartnerReward;
use App\Services\CallingSender;
use App\Services\WSClient;
use App\UseCase\Call\AddOrUpdateRepeat\Command;
use App\UseCase\Call\AddOrUpdateRepeat\Handler;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class PatchAction extends AbstractController
{

    private AmoCRMApiClient $client;

    public function __construct(
        private readonly CallPreDenormalizeListener $callPreDenormalizeListener,
        AmoCRM                    $amoCRM,
        private readonly WSClient $wsClient,
        private readonly AsteriskChannel\AddOrUpdate\Handler  $asteriskAddOrUpdateHandler,
        private readonly AsteriskChannel\DeleteByClientPhone\Handler  $asteriskDeleteHandler,
        private readonly McnBlacklistService  $mcnBlacklistService,
        private readonly CallingSender  $sender,
        private readonly Flusher $flusher,
        private readonly PartnerReward  $partnerReward,
        private readonly OperatorReward $operatorReward,
        private readonly Handler $handler,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Calling $calling): JsonResponse
    {
        $this->handleStatusChange($calling);

        $this->flusher->flush();

        return $this->json(
            $calling,
            200,
            [],
            [
                'groups' => [
                    'v1-call:read',
                    'client:item:read',
                ]
            ]
        );
    }

    public function handleStatusChange(Calling $calling): void
    {
        $newStatus = $calling->getStatus();
        $originalStatus = $this->callPreDenormalizeListener->getStatus();
        if ($newStatus === $originalStatus){
            return;
        }

        if ($newStatus === Status::ACCEPTED) {
            $this->handleAcceptedStatus($calling);
        }

        if ($newStatus === Status::ARRIVED) {
            $this->handleArrivedStatus($calling);
        }

        if ($newStatus === Status::DISPATCHED) {
            $this->handleDispatchedStatus($calling);
        }

        if ($newStatus === Status::TREATING) {
            $this->handleTreatingStatus($calling);
        }

        if ($newStatus === Status::COMPLETED) {
            $this->handleCompletedStatus($calling);
        }
    }

    public function handleAcceptedStatus(Calling $calling): void
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($calling->getAdmin()?->getId() !== $user->getId()) {
            throw new DomainException('Принять вызов может только администратор');
        }

        $this->updateAmoCRMLeads($calling, 62358394);

        $calling->setAccepted(new DateTimeImmutable());

        $this->wsClient->sendUpdateOffer($calling->getId());

        $this->handleAsteriskUpdate($calling);
    }

    private function handleArrivedStatus(Calling $calling)
    {
        $this->updateAmoCRMLeads($calling, 62358398);

        $calling->setArrived(new DateTimeImmutable());

        $this->wsClient->sendUpdateOffer($calling->getId());
    }

    private function handleDispatchedStatus(Calling $calling)
    {
        $this->updateAmoCRMLeads($calling, 38187418);

        $calling->setDispatched(new DateTimeImmutable());

        $this->wsClient->sendUpdateOffer($calling->getId());
    }

    private function handleTreatingStatus(Calling $calling)
    {
        $this->updateAmoCRMLeads($calling, 38187418);

        $this->wsClient->sendUpdateOffer($calling->getId());
    }

    public function updateAmoCRMLeads(Calling $calling, int $leadStatusId): void
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId($leadStatusId);
        }

        $this->client->leads()->update($leads);
    }

    public function handleAsteriskUpdate(Calling $calling): void
    {
        try {
            $this->asteriskAddOrUpdateHandler->handle(
                new AsteriskChannel\AddOrUpdate\Command(
                    $calling->getClient()?->getPhone(),
                    $calling->getAdmin()?->getPhone()
                )
            );

            if ($calling->getClient()?->getPhone()) {
                $this->mcnBlacklistService->addToBlacklist($calling->getClient()->getPhone());
            }

        } catch (Exception) {
        }
    }

    public function handleAsteriskDelete(Calling $calling): void
    {
        try {
            $this->asteriskDeleteHandler->handle(
                new AsteriskChannel\DeleteByClientPhone\Command($calling->getClient()?->getPhone())
            );

            if ($calling->getClient()?->getPhone()) {
                $this->mcnBlacklistService->deleteFromBlacklist($calling->getClient()->getPhone());
            }
        } catch (Exception) {

        }
    }



    private function handleCompletedStatus(Calling $calling)
    {
        $price = 0;
        $paymentNextOrder = 0;
        $paymentHospitalization = 0;

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->getService()->getType() === 'default') {
                $price += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            } elseif ($serviceRow->getService()->getType() === 'hospital') {
                $paymentHospitalization += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            } else {
                $paymentNextOrder += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            }
        }

        $calling->setPrice($price);
        $calling->setPaymentHospitalization($paymentHospitalization);
        $calling->setPaymentNextOrder($paymentNextOrder);

        $replay = '';
        $hospital = '';

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->isStationary()) {
                $hospital .= 'Стационар ' . PHP_EOL;
                $hospital .= $serviceRow->getPlannedPrice() ? 'Ориентировочная цена ' . $serviceRow->getPlannedPrice() . PHP_EOL : '';
                $hospital .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $hospital .= $serviceRow->getPlannedAt() ? 'Дата ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') . PHP_EOL : '';
                $hospital .= $serviceRow->getDescription() ?
                    'Комментарий ' . $serviceRow->getDescription() . PHP_EOL : '';
            }
            if ($serviceRow->getService()->getType() === 'replay') {
                $replay .= 'Повтор ' . PHP_EOL;
                $replay .= $serviceRow->getPlannedPrice() ? 'Ориентировочная цена ' . $serviceRow->getPlannedPrice() . PHP_EOL : '';
                $replay .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $replay .= $serviceRow->getPlannedAt() ? '*ПОВТОР* Дата ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') . PHP_EOL : '';
                $replay .= $serviceRow->getDescription() ?
                    'Комментарий ' . $serviceRow->getDescription() . PHP_EOL : '';
                $this->repeat($calling, $serviceRow);

                $this->sender->sendToAdmin(
                    $calling,
                    'Вызов N ' . $calling->getNumberCalling(),
                    'Оформлен повтор'
                );
            }
        }

        $this->completeCall($calling, $hospital, $replay);

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );

        $this->wsClient->sendUpdateOffer($calling->getId());

        $this->handleAsteriskDelete($calling);
    }

    private function completeCall(Calling $calling, string $hospital, string $replay): void
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }

        $price = 0;
        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->getService()->getType() === 'default' && !$serviceRow->isHospital()) {
                $price += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            }
        }

        $message = 'Информация от бригады:' . PHP_EOL;

        $message .= $calling->getPrice() ? 'Итого: ' . $price . PHP_EOL : '';
        $message .= $calling->getOriginalPhone() ? 'Номер телефона заказчика: ' . $calling->getOriginalPhone() . PHP_EOL : '';
        $message .= $calling->getFio() ? 'ФИО пациента: ' . $calling->getFio() . PHP_EOL : '';
        $message .= $calling->getAge() ? 'Возраст пациента: ' . $calling->getAge() . PHP_EOL : '';
        $message .= $calling->getMkadDistance() ? 'Расстояние до МКАД: ' . $calling->getMkadDistance() . PHP_EOL : '';
        $message .= $calling->getAddress() ? 'Адрес: ' . $calling->getAddress() . PHP_EOL : '';
        $message .= PHP_EOL;

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {

            if ($serviceRow->getService()->getType() === 'replay') {
                $message .= 'Повтор' . ($serviceRow->getPlannedAt() ? ' - ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') : '') . PHP_EOL;
                $message .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            }
        }

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {

            if ($serviceRow->isStationary()) {
                continue;
            } elseif ($serviceRow->isHospital()) {
                $message .= 'Госпитализация' . ($serviceRow->getPrice() ? ' - ' . $serviceRow->getPrice() : '') . PHP_EOL;
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            } elseif ($serviceRow->getService()->getType() === 'replay') {
                continue;
            } else {
                $message .= $serviceRow->getService()->getName() . ($serviceRow->getPrice() ? ' - ' . $serviceRow->getPrice() : '') . PHP_EOL;
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            }
        }

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->isStationary()) {
                $message .= 'Стационар' . ($serviceRow->getPrice() ? ' - ' . $serviceRow->getPrice() : '') . PHP_EOL;
                $message .= $serviceRow->getClinic() ? $serviceRow->getClinic()->getName() . PHP_EOL : '';
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            }
        }

        $message .= 'ВСЕГО:' . $calling->getTotalAmount() . PHP_EOL;

        $message .= PHP_EOL;

        $message .= 'Заявка №' . $calling->getNumberCalling() . PHP_EOL;

        $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));

        $entityId = null;

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $entityId = $lead->getId();
            $lead->setStatusId(45084664);
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

        $this->partnerReward->calculate($calling);
        $this->operatorReward->calculate($calling);

        $this->flusher->flush();
    }



    private function repeat(Calling $calling, Row $row): void
    {
        $lead = $this->client->leads()->getOne($calling->getNumberCalling());

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter([$calling->getNumberCalling()]);
        $allLinks = $linksService->get($filter);


        $contactId = null;
        $companyId = null;
        /** @var LinkModel $link */
        foreach ($allLinks as $link) {
            if (
                $link->getMetadata()
                && isset($link->getMetadata()['main_contact'])
                && $link->getMetadata()['main_contact']
            ) {
                $contactId = $link->getToEntityId();
            }

            if ($link->getToEntityType() === 'companies') {
                $companyId = $link->getToEntityId();
            }
        }


        if (!$contactId) {
            throw new NotFoundHttpException('Не найден контакт при создании повтора');
        }

        $name = $row->getPlannedAt()->format('d.m.y ') . ' ПОВТОР в ' . $row->getPlannedAt()->format('H:i ') . ' ' . $calling->getFio();

        $customFieldsValues = new CustomFieldsValuesCollection();
        foreach ($lead->getCustomFieldsValues() as $customFieldsValue) {
            //Время прибытия, бригаду, админа и врача не переносим в повотор
            if (
                $customFieldsValue->getFieldId() === 880453 ||
                $customFieldsValue->getFieldId() === 875863 ||
                $customFieldsValue->getFieldId() === 873879 ||
                $customFieldsValue->getFieldId() === 873881
            ) {
                continue;
            }
            $customFieldsValues->add($customFieldsValue);
        }

        if ($row->getPlannedAt()) {
            $textCustomFieldValueModel = new DateTimeCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(880453);
            $textCustomFieldValueModel->setValues(
                (
                new DateTimeCustomFieldValueCollection())
                    ->add(
                        (new DateTimeCustomFieldValueModel())
                            ->setValue($row->getPlannedAt()->getTimestamp())
                    )
            );
            $customFieldsValues->add($textCustomFieldValueModel);
        }

        $newLead = new LeadModel();
        $newLead->setName($name)
            ->setCreatedBy(0)
            ->setPrice($calling->getEstimated())
            ->setStatusId(38307805)
            ->setPipelineId(4018768)
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


        if ($companyId) {
            $newLead->setCompany(
                (new CompanyModel())
                    ->setId($companyId)
            );
        }

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($newLead);

        $leadModel = $this->client->leads()->addOne($newLead);

        $calling->setOwnerExternalId((string)$leadModel->getId());

        $this->flusher->flush();

        $command = new Command((string)$leadModel->getId());
        $this->handler->handle($command);
    }
}
