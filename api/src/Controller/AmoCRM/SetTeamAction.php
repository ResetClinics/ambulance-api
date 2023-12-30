<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseEnumCodeCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Dto\Amo\Employee;
use App\Dto\Amo\Lead;
use App\Dto\Amo\LeadForEmployee;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Partner;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\PartnerRepository;
use App\Repository\UserRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\YaGeolocation\Api;
use Carbon\Carbon;
use DateTimeImmutable;
use DomainException;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

#[Route('/api/amo-crm/set-team', name: 'amo-crm_set-team', methods: ["POST"])]
class SetTeamAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingSender $sender;

    public function __construct(
        private readonly Api $geocodingApi,
        AmoCRM                             $amoCRM,
        private readonly UserRepository    $users,
        private readonly CallingRepository $callings,
        private readonly PartnerRepository $partners,
        private readonly Flusher           $flusher,
        CallingSender                     $sender
    )
    {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
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












        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r($lead, true),
            FILE_APPEND);



        return $this->json(null, Response::HTTP_OK);
    }


}
