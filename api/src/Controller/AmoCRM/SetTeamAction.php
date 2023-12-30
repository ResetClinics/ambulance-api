<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use App\Dto\Amo\LeadForEmployee;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\AmoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/set-team', name: 'amo-crm_set-team', methods: ["POST"])]
class SetTeamAction extends AbstractController
{
    private AmoCRMApiClient $client;

    private MedTeamRepository $medTeamRepository;

    public function __construct(
        AmoCRM                             $amoCRM,
        MedTeamRepository $medTeamRepository
    )
    {
        $this->client = $amoCRM->getClient();
        $this->medTeamRepository = $medTeamRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(0, true),
            FILE_APPEND);

        $data = $request->request->all();

        $leadId = $data['leads']['status'][0]['id'];

        $filter = new LeadsFilter();
        $filter->setIds([$leadId]);

        $leads = $this->client->leads()->get($filter);

        $leadData = $leads->first();

        $lead = $this->getLeadDto($leadData);


        $medTeam = $this->medTeamRepository->getLastWorkByNumber($lead->team);











        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r($medTeam->getId(), true),
            FILE_APPEND);



        return $this->json(null, Response::HTTP_OK);
    }


    private function getLeadDto($leadData): LeadForEmployee
    {
        $lead = new LeadForEmployee();

        $lead->price = $leadData->getPrice();

        foreach ($leadData->getCustomFieldsValues() as $field) {

            if ($field->getFieldId() === 879807) {
                $lead->numberCalling = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880453) {
                $lead->dateTime = $field->getValues()?->first()->getValue()?->toString();
            }
            if ($field->getFieldId() === 870903) {
                $lead->address = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 875863) {
                $lead->team = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880527) {
                $lead->nosology = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870907) {
                $lead->age = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870945) {
                $lead->description = $field->getValues()?->first()->getValue() ?: '';
            }

            if ($field->getFieldId() === 884333) {
                $lead->hz = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 960101) {
                $lead->leadType = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 896921) {
                $lead->sendPhone = $field->getValues()?->first()->getValue();
            }
        }

        return $lead;
    }
}
