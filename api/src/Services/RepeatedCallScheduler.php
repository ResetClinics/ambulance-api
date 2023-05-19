<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RepeatedCallScheduler
{
    private AmoCRMApiClient $client;


    public function __construct(
        AmoCRM        $amoCRM,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function schedule(int $id): void
    {
        $lead = $this->client->leads()->getOne($id);

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter([$id]);
        $allLinks = $linksService->get($filter);


        $contactId = null;
        /** @var LinkModel $link */
        foreach ($allLinks as $link) {
            if ($link->getMetadata()['main_contact']) {
                $contactId = $link->getToEntityId();
            }
        }

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

        $this->client->leads()->add($leadsCollection);

    }
}