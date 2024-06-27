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
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Row;
use App\Entity\Hospital\Hospital;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\Hospital\HospitalRepository;
use App\Services\AmoCRM;
use App\Services\Call\OperatorReward;
use App\Services\Call\PartnerReward;
use App\Services\CallingSender;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class FinishAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingSender $sender;
    private Flusher $flusher;
    private CallingRepository $callings;
    private HospitalRepository $hospitals;
    private PartnerReward $partnerReward;
    private OperatorReward $operatorReward;

    public function __construct(
        AmoCRM        $amoCRM,
        CallingSender $sender,
        CallingRepository $callings,
        HospitalRepository $hospitals,
        PartnerReward $partnerReward,
        OperatorReward $operatorReward,
        Flusher $flusher
    )
    {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
        $this->flusher = $flusher;
        $this->callings = $callings;
        $this->hospitals = $hospitals;
        $this->partnerReward = $partnerReward;
        $this->operatorReward = $operatorReward;
    }

    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {

        $price = 0;
        $paymentNextOrder = 0;
        $paymentHospitalization = 0;

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow){
            if ($serviceRow->getService()->getType() === 'default'){
                $price  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }elseif ($serviceRow->getService()->getType() === 'hospital') {
                $paymentHospitalization  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }else{
                $paymentNextOrder  += $serviceRow->getPrice() !== null ? (int) $serviceRow->getPrice() : 0;
            }
        }

        $calling->setPrice($price);
        $calling->setPaymentHospitalization($paymentHospitalization);
        $calling->setPaymentNextOrder($paymentNextOrder);

        $replay = '';
        $hospital = '';

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow){
            if ($serviceRow->getService()->getType() === 'hospital'){
                $hospital .=  'Госпитализация ' . PHP_EOL;
                $hospital .= $serviceRow->getPlannedPrice() ? 'Ориентировочная цена ' . $serviceRow->getPlannedPrice() . PHP_EOL : '';
                $hospital .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $hospital .= $serviceRow->getPlannedAt() ? '*ПОВТОР* Дата ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') . PHP_EOL : '';
            }
            if ($serviceRow->getService()->getType() === 'replay'){
                $replay .=  'Повтор ' . PHP_EOL;
                $replay .= $serviceRow->getPlannedPrice() ? 'Ориентировочная цена ' . $serviceRow->getPlannedPrice() . PHP_EOL : '';
                $replay .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $replay .= $serviceRow->getPlannedAt() ? '*ПОВТОР* Дата ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') . PHP_EOL : '';
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

        return $this->json($calling, Response::HTTP_ACCEPTED);
    }


    private function completeCall(Calling $calling, string $hospital, string $replay): void
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }

        $message = 'Информация от бригады:' . PHP_EOL;

        $message .= $calling->getPrice() ? 'Итоговая цена: ' . $calling->getPrice() . PHP_EOL : '';
        $message .= $calling->getPhone() ? 'Номер телефона заказчика: ' . $calling->getPhone() . PHP_EOL : '';
        $message .= $calling->getFio() ? 'ФИО пациента: ' . $calling->getFio() . PHP_EOL : '';
        $message .= $calling->getAge() ? 'Возраст пациента: ' . $calling->getAge() . PHP_EOL : '';
        $message .= $calling->getAddress() ? 'Адрес: ' . $calling->getAddress() . PHP_EOL : '';
        $message .= $calling->getMkadDistance() ? 'Расстояние до МКАД: ' . $calling->getMkadDistance() . PHP_EOL : '';

        $message .= $hospital;
        $message .= $replay;

        $message .= $calling->getNote() ? 'Примечание ' . $calling->getNote() . PHP_EOL : '';

        $description = '';

        /** @var Row $row */
        foreach ($calling->getServices()->toArray() as $row){
            $description .= $row->getDescription() ? $row->getDescription() . PHP_EOL : '';
        }

        $message .= $description ? 'Комментарий ' . $description . PHP_EOL : '';

        $message .=  PHP_EOL;

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

            if ($link->getToEntityType() === 'companies'){
                $companyId = $link->getToEntityId();
            }
        }


        if (!$contactId) {
            throw new NotFoundHttpException('Не найден контакт при создании повтора');
        }

        $name = $row->getPlannedAt()->format('d.m.y ') . ' ПОВТОР в ' . $row->getPlannedAt()->format('H:s ') . ' ' . $calling->getFio();

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


        if ($companyId){
            $newLead ->setCompany(
                (new CompanyModel())
                    ->setId($companyId)
            );
        }

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($newLead);

        $leadModel = $this->client->leads()->addOne($newLead);

        $calling->setOwnerExternalId((string)$leadModel->getId());

        $this->flusher->flush();

       /* if (!$this->callings->findOneByNumber((string)$leadModel->getId())){
            $repeat = new Calling(
                (string)$leadModel->getId(),
                $name,
                $calling->getName(),
                $calling->getPhone(),
                $calling->getAddress(),
                null,
                null,
                null
            );

            $repeat->setOwner($calling);

            $price = $row->getPrice() !== null ? (int)$row->getPrice() : null;

            $repeat->setPrepayment($price);

            $this->callings->save($repeat, true);
        }*/
    }
}
