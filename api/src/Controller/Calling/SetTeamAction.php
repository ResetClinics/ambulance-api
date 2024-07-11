<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
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
                $leadCustomFieldsValues = new CustomFieldsValuesCollection();
                $teamSelectCustomValueModel = new SelectCustomFieldValuesModel();
                $teamSelectCustomValueModel->setFieldId(875863);
                $teamSelectCustomValueModel->setValues(
                    (new SelectCustomFieldValueCollection())
                        ->add((new SelectCustomFieldValueModel())->setValue('0')->setEnumId(660461))
                );
                $leadCustomFieldsValues->add($teamSelectCustomValueModel);
                $lead->setCustomFieldsValues($leadCustomFieldsValues);
                $lead->setStatusId(38874646);
            }

            $this->client->leads()->update($leads);
        } catch (Exception $exception) {
            return $this->json(['error' => $exception->getMessage(), $exception], Response::HTTP_ACCEPTED);
        }

        return $this->json([], Response::HTTP_ACCEPTED);
    }
}
