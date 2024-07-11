<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Repository\CallingRepository;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\AmoCRM;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/calls/{id}/set-team/{teamId}', name: 'call.set-team', methods: ["POST"])]
class SetTeamAction extends AbstractController
{
    private AmoCRMApiClient $client;

    public function __construct(
        private readonly CallingRepository $calls,
        private readonly MedTeamRepository $teams,
        AmoCRM                             $amoCRM,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke($id, $teamId): JsonResponse
    {
        try {
            $call = $this->calls->getById($id);
            $team = $this->teams->getById($teamId);

            $filter = new LeadsFilter();
            $filter->setIds([$call->getNumberCalling()]);

            $leads = $this->client->leads()->get($filter);

            /** @var LeadModel $lead */
            foreach ($leads as $lead) {

                if (!$lead->getCustomFieldsValues()) {
                    return $this->json(['values' => null], Response::HTTP_ACCEPTED);
                }

                foreach ($lead->getCustomFieldsValues() as $field) {
                    if ($field->getFieldId() === 875863) {
                        return $this->json(['values' => gettype($field)], Response::HTTP_ACCEPTED);
                    }
                }

                return $this->json(['values' => false], Response::HTTP_ACCEPTED);

                //$leadCustomFieldsValues = new CustomFieldsValuesCollection();
//
                //$textCustomFieldValueModel = new TextCustomFieldValuesModel();
                //$textCustomFieldValueModel->setFieldId(875863);
                //$textCustomFieldValueModel->setValues(
                //    (new TextCustomFieldValueCollection())
                //        ->add((new TextCustomFieldValueModel())->setValue($team->getPhone()?->getId()))
                //);
                //$leadCustomFieldsValues->add($textCustomFieldValueModel);
                //$lead->setCustomFieldsValues($leadCustomFieldsValues);
                $lead->setStatusId(38874646);
            }

            $this->client->leads()->update($leads);
        } catch (Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_ACCEPTED);
        }

        return $this->json([], Response::HTTP_ACCEPTED);
    }
}
