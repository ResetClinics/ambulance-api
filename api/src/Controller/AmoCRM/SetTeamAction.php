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

        $leadId = $_POST['leads']['status'][0]['id'];


        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r($leadId, true),
            FILE_APPEND);

        $leadData = [];
        if (isset($data['leads']['update'][0])) {
            $leadData = $data['leads']['update'][0];
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(1, true),
            FILE_APPEND);

        if (isset($data['leads']['add'][0])) {
            $leadData = $data['leads']['update'][0];
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(2, true),
            FILE_APPEND);

        if (!$leadData) {
            return $this->json(null, Response::HTTP_OK);
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(3, true),
            FILE_APPEND);

        if ((int)$leadData['pipeline_id'] !== 4018768) {
            return $this->json(null, Response::HTTP_OK);
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(4, true),
            FILE_APPEND);

        $leadDto = $this->getLeadInfo((int)$leadData['id']);
        if (!$leadDto) {
            return $this->json(null, Response::HTTP_OK);
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(5, true),
            FILE_APPEND);

        try {

            //тут мы должны сработать с лидом
            $this->onSetTeam($leadDto);
        } catch (\Exception $e) {
            throw new DomainException($e->getMessage());
        }


        return $this->json(null, Response::HTTP_OK);
    }

    private function getLeadInfo(int $leadId): ?Lead
    {
        $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS, LeadModel::CATALOG_ELEMENTS]);
        if (!$lead) {
            throw new DomainException('Не найден лид');
        }

        if (!$lead->getCustomFieldsValues()) {
            throw new DomainException('Не заполнены поля');
        }


        if (!$lead->getMainContact()) {
            throw new DomainException('Не указан контакт');
        }

        $contact = $this->client->contacts()->getOne($lead->getMainContact()->getId());

        if (!$contact) {
            throw new DomainException('Не найден контакт');
        }

        $name = $contact->getName();

        if (!$name) {
            return null;
        }

        $phone = null;

        /** @var MultitextCustomFieldValuesModel $field */
        foreach ($contact->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 604157) {
                $phone = $field->getValues()?->first()->getValue();
            }
        }

        if (!$phone) {
            throw new DomainException('Не найден телефон');
        }

        $leadDto = new Lead($leadId, $name, $phone);

        $leadDto->name = $lead->getName();
        $leadDto->statusId = $lead->getStatusId();

        foreach ($lead->getCustomFieldsValues() as $field) {

            if ($field->getFieldId() === 879807) {
                $leadDto->numberCalling = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880453) {
                /** @var Carbon $dateTime */
                $leadDto->dateTime = $field->getValues()?->first()->getValue()?->toString();
                //$date_time = date("d.m.Y H:i:s", $one_field['values'][0]['value']);
            }
            if ($field->getFieldId() === 870903) {
                $leadDto->address = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 875863) {
                $leadDto->team = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880527) {
                $leadDto->nosology = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870907) {
                $leadDto->age = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870945) {
                $leadDto->description = $field->getValues()?->first()->getValue() ?: '';
            }

            if ($field->getFieldId() === 884333) {
                $leadDto->hz = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 960101) {
                $leadDto->leadType = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 882361) {
                $first = $field->getValues()?->first();

                if ($first instanceof BaseEnumCodeCustomFieldValueModel) {
                    $leadDto->partnerExternalId = $first->getEnumId() ? (string)$first->getEnumId() : null;
                }
                $leadDto->partnerName = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 896921) {
                $leadDto->sendPhone = $field->getValues()?->first()->getValue();
            }


            if ($field->getFieldId() === 873879) {
                $userName = $field->getValues()?->first()->getValue();
                $filter = new LeadsFilter();

                $filter->setNames($userName);

                $filter->setStatuses([[
                    'pipeline_id' => 4105087,
                ]]);

                $leadsEmployee = $this->client->leads()->get($filter);
                $leadEmployee = $leadsEmployee->first();
                $leadDto->admin = new Employee($leadEmployee->getId(), $leadEmployee->getName(), 'ROLE_ADMIN');
            }
            if ($field->getFieldId() === 873881) {
                $userName = $field->getValues()?->first()->getValue();
                $filter = new LeadsFilter();

                $filter->setNames($userName);

                $filter->setStatuses([[
                    'pipeline_id' => 4105087,
                ]]);

                $leadsEmployee = $this->client->leads()->get($filter);
                $leadEmployee = $leadsEmployee->first();
                $leadDto->doctor = new Employee($leadEmployee->getId(), $leadEmployee->getName(), 'ROLE_DOCTOR');
            }
        }

        return $leadDto;
    }

    private function onSetTeam(Lead $leadDto)
    {
        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r(1, true),
            FILE_APPEND)
        ;
        file_put_contents(
            dirname(__DIR__) . '/../../var/set_team.txt',
            print_r($leadDto, true),
            FILE_APPEND)
        ;
    }

}
