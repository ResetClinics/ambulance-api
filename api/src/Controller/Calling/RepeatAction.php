<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use DateTimeImmutable;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class RepeatAction extends AbstractController
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

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(45084664);
        }

        $this->client->leads()->update($leads);

        $calling->setComplete(new DateTimeImmutable());
        $flusher->flush();

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );
        try {

            $lead = $this->client->leads()->getOne($calling->getNumberCalling());

            file_put_contents(
                dirname(__DIR__) . '/../../var/calling ' . date("Y-m-d H:i:s") . '.txt',
                print_r($calling->getNumberCalling(), true),
                FILE_APPEND);

            if (!$lead) {
                throw new NotFoundHttpException('Не найден лид при создании повтора');
            }

            file_put_contents(
                dirname(__DIR__) . '/../../var/lead ' . date("Y-m-d H:i:s") . '.txt',
                print_r($lead->getId(), true),
                FILE_APPEND);
            $linksService = $this->client->links(EntityTypesInterface::LEADS);

            $filter = new EntitiesLinksFilter([$lead->getId()]);
            $allLinks = $linksService->get($filter);

            $contactId = null;
            /** @var LinkModel $link */
            foreach ($allLinks as $link) {
                if ($link->getMetadata()['main_contact']) {
                    $contactId = $link->getToEntityId();
                }
            }

            file_put_contents(
                dirname(__DIR__) . '/../../var/main-contact ' . date("Y-m-d H:i:s") . '.txt',
                print_r($contactId, true),
                FILE_APPEND);


            if (!$contactId) {
                throw new NotFoundHttpException('Не найден контакт при создании повтора');
            }

            $newLead = new LeadModel();
            $newLead->setName($lead->getName())
                ->setCreatedBy(0)
                ->setStatusId(38307805)
                ->setPipelineId(4018768)
                ->setResponsibleUserId($lead->getResponsibleUserId())
                ->setCustomFieldsValues($lead->getCustomFieldsValues())
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

            $coll = $this->client->leads()->add($leadsCollection);

            file_put_contents(
                dirname(__DIR__) . '/../../var/lead-collection ' . date("Y-m-d H:i:s") . '.txt',
                print_r($coll, true),
                FILE_APPEND);


            $this->sender->sendToAdmin(
                $calling,
                'Вызов N ' . $calling->getNumberCalling(),
                'Оформлен повтор'
            );

        } catch (Exception $exception) {
            throw new DomainException('Ошибка создания повтора в AmoCRM: ' . $exception->getMessage());
        }

        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
