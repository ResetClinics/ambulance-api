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
use App\Entity\Calling\Calling;
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

    public function schedule(Calling $calling): void
    {
        $lead = $this->client->leads()->getOne($calling->getNumberCalling());

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter([$calling->getNumberCalling()]);
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

        $name = $calling->getResultDateFormat() . ' ПОВТОР ' . $calling->getName();

        $newLead = new LeadModel();
        $newLead->setName($name)
            ->setCreatedBy(0)
            ->setPrice($calling->getEstimated())
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