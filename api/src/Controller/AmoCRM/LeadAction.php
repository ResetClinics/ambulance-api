<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\LeadModel;
use App\Dto\Amo\Employee;
use App\Dto\Amo\Lead;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\UserRepository;
use App\Services\AmoCRM;
use Carbon\Carbon;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/lead', name: 'amo-crm_lead', methods: ["POST"])]
class LeadAction extends AbstractController
{
    private AmoCRMApiClient $client;
    public function __construct(
        AmoCRM         $amoCRM,
        private readonly UserRepository $users,
        private readonly   CallingRepository $callings,
        private readonly Flusher $flusher
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();
        //$test = '{"leads":{"update":[{"id":"20481239","name":"\u0422\u0435\u0441\u0442 \u0414\u043c\u0438\u0442\u0440\u0438\u0439","status_id":"38874646","old_status_id":"38307946","price":"0","responsible_user_id":"6784588","last_modified":"1679393193","modified_user_id":"6784588","created_user_id":"6784588","date_create":"1679388280","pipeline_id":"4018768","account_id":"29317822","custom_fields":[{"id":"960101","name":"\u0422\u0438\u043f \u0437\u0430\u044f\u0432\u043a\u0438","values":[{"value":"\u041d\u0430\u0448\u0430","enum":"650653"}]},{"id":"875587","name":"\u041f\u0435\u0440\u0432\u0438\u0447\u043d\u044b\u0439 \u0437\u0430\u043f\u0440\u043e\u0441","values":[{"value":"\u0412\u044b\u0432\u043e\u0434 \u0438\u0437 \u0437\u0430\u043f\u043e\u044f","enum":"529899"}]},{"id":"879807","name":"\u2116 \u0441\u0434\u0435\u043b\u043a\u0438","values":[{"value":"03.21-20481239"}]},{"id":"870901","name":"\u041a\u043e\u043c\u0443","values":[{"value":"\u0421\u0430\u043c\u043e\u043e\u0431\u0440\u0430\u0449\u0435\u043d\u0438\u0435","enum":"508745"}]},{"id":"870903","name":"\u0410\u0434\u0440\u0435\u0441","values":[{"value":"\u041b\u0430\u0432\u0440\u0443\u0448\u0438\u043d\u0441\u043a\u0438\u0439 \u043f\u0435\u0440\u0435\u0443\u043b\u043e\u043a, 10\u04414"}]},{"id":"870907","name":"\u0412\u043e\u0437\u0440\u0430\u0441\u0442","values":[{"value":"30"}]},{"id":"870909","name":"\u041f\u043e\u043b","values":[{"value":"\u041c","enum":"508749"}]},{"id":"870945","name":"\u041f\u0440\u0438\u043c\u0435\u0447\u0430\u043d\u0438\u0435","values":[{"value":"\u0422\u0443\u0442 \u043a\u0430\u043a\u043e\u0435 \u0442\u043e \u043e\u0433\u0440\u043e\u043c\u043d\u043e\u0435 \u043f\u0440\u0438\u043c\u0435\u0447\u0430\u043d\u0438\u0435...."}]},{"id":"875863","name":"\u0411\u0440\u0438\u0433\u0430\u0434\u0430","values":[{"value":"6","enum":"530185"}]},{"id":"896921","name":"\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c \u0442\u0435\u043b\u0435\u0444\u043e\u043d","values":[{"value":"1"}]},{"id":"873879","name":"\u0410\u0434\u043c\u0438\u043d","values":[{"value":"\u0414\u0430\u0440\u044c\u044f \u0434\u0435\u0436\u0443\u0440\u043d\u044b\u0439 \u0430\u0434\u043c\u0438\u043d\u0438\u0441\u0442\u0440\u0430\u0442\u043e\u0440"}]},{"id":"873881","name":"\u0412\u0440\u0430\u0447","values":[{"value":"\u0422\u043a\u0430\u0447\u0451\u0432 \u0418\u0433\u043e\u0440\u044c"}]},{"id":"880453","name":"\u0414\u0430\u0442\u0430 \u0432\u0440\u0435\u043c\u044f \u043f\u0440\u0438\u0435\u0437\u0434\u0430","values":["1678355580"]},{"id":"882361","name":"\u041f\u0430\u0440\u0442\u043d\u0435\u0440","values":[{"value":"\u0421\u0430\u0439\u0442 \u041a\u043e\u0440\u0434\u0438\u044f","enum":"600689"}]}],"created_at":"1679388280","updated_at":"1679393193"}]},"account":{"subdomain":"af4040148","id":"29317822","_links":{"self":"https:\/\/af4040148.amocrm.ru"}}}';
        //$data = json_decode($test, true);

        $leadData = [];
        if (isset($data['leads']['update'][0])){
            $leadData = $data['leads']['update'][0];
        }
        if (isset($data['leads']['add'][0])){
            $leadData = $data['leads']['update'][0];
        }
        if (!$leadData){
            return $this->json(null, Response::HTTP_OK);
        }

        if ((int)$leadData['pipeline_id'] !== 4018768){
            return $this->json(null, Response::HTTP_OK);
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/ids.txt',
            print_r($leadData['id']. PHP_EOL, true),
            FILE_APPEND)
        ;

        $leadDto = $this->getLeadInfo((int) $leadData['id']);

        $this->onSetTeam($leadDto);

        return $this->json(null, Response::HTTP_OK);
    }

    private function getLeadInfo(int $leadId): Lead
    {
        $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS, LeadModel::CATALOG_ELEMENTS]);
        if (!$lead){
            throw new \DomainException('Не найден лид');
        }

        if (!$lead->getCustomFieldsValues()){
            throw new \DomainException('Не заполнены поля');
        }


        if (!$lead->getMainContact()){
            throw new \DomainException('Не указан контакт');
        }

        $contact = $this->client->contacts()->getOne($lead->getMainContact()->getId());

        if (!$contact){
            throw new \DomainException('Не найден контакт');
        }

        $name = $contact->getName();

        $phone = null;

        /** @var MultitextCustomFieldValuesModel $field */
        foreach ($contact->getCustomFieldsValues() as $field){
            if ($field->getFieldId() === 604157){
                $phone = $field->getValues()?->first()->getValue();
            }
        }

        if (!$phone){
            throw new \DomainException('Не найден телефон');
        }

        $leadDto = new Lead($leadId, $name, $phone);

        $leadDto->name = $lead->getName();
        $leadDto->statusId = $lead->getStatusId();

        foreach ($lead->getCustomFieldsValues() as $field){

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
                $leadDto->description = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 884333) {
                $leadDto->hz = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 960101) {
                $leadDto->leadType = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 882361) {
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

        if (!$leadDto->doctor || !$leadDto->admin){
            throw new \DomainException('Не установлен персонал');
        }

        return  $leadDto;
    }

    /**
     * @throws \Exception
     */
    private function onSetTeam(Lead $lead): void
    {
        $calling = $this->callings->findOneByNumber($lead->numberCalling);

        $admin = $this->users->getByExternalId($lead->admin->getId());
        $doctor = $this->users->getByExternalId($lead->doctor->getId());


        if ($calling){

            $calling->setNosology($lead->nosology);
            $calling->setAge($lead->age);
            $calling->setChronicDiseases($lead->hz);
            $calling->setLeadType($lead->leadType);
            $calling->setPartnerName($lead->partnerName);
            $calling->setSendPhone($lead->sendPhone);

        }else{
            $calling = new Calling(
                $lead->numberCalling,
                $lead->name,
                $lead->clientName,
                $lead->clientPhone,
                $lead->address,
                $lead->description,
                $admin,
                $doctor
            );

            $calling->setNosology($lead->nosology);
            $calling->setAge($lead->age);
            $calling->setChronicDiseases($lead->hz);
            $calling->setLeadType($lead->leadType);
            $calling->setPartnerName($lead->partnerName);
            $calling->setSendPhone($lead->sendPhone);

            $this->callings->save($calling, false);
        }

        if ($lead->statusId === 38307946 || $lead->statusId === 38874646){
            $calling->setStatus(Status::assigned());
        }elseif ($lead->statusId === 38187418){
            $calling->setStatus(Status::accepted());
        }else{
            $calling->setStatus(Status::completed());
        }

        if ($lead->dateTime) {
            $calling->setDateTime(new DateTimeImmutable($lead->dateTime));
        }

        $this->flusher->flush();
    }
}
