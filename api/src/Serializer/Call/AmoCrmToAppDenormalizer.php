<?php

namespace App\Serializer\Call;

use AmoCRM\Exceptions\InvalidArgumentException;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseEnumCodeCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\UseCase\Call\SendFromCrm\Lead;
use Carbon\Carbon;
use DomainException;
use Symfony\Component\HttpFoundation\Response;

class AmoCrmToAppDenormalizer implements CrmToAppDenormalizerInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {

        file_put_contents(
            dirname(__DIR__) . '/../../var/hook-home-call-lead-list-data-' . date("Y-m-d H:i:s") . '.txt',
            print_r($data, true).PHP_EOL,
            FILE_APPEND);


        if (isset($data['leads']['update'][0])) {
            $leadData = $data['leads']['update'][0];
        } elseif (isset($data['leads']['add'][0])) {
            $leadData = $data['leads']['add'][0];
        } else {
            throw new DomainException('Нет данных в теле запроса');
        }

        file_put_contents(
            dirname(__DIR__) . '/../../var/hook-home-call-lead-data-' . date("Y-m-d H:i:s") . '.txt',
            print_r($leadData, true).PHP_EOL,
            FILE_APPEND);


        $lead = LeadModel::fromArray($leadData);

        file_put_contents(
            dirname(__DIR__) . '/../../var/hook-home-call-lead-model-' . date("Y-m-d H:i:s") . '.txt',
            print_r($lead, true).PHP_EOL,
            FILE_APPEND);


        $leadDto = new Lead(
            $lead->getId(),
            $lead->getStatusId(),
            $lead->getPipelineId(),
            $lead->getName()
        );

        $leadDto->mainContactId = $lead->getMainContact()?->getId();

        if (!$lead->getCustomFieldsValues()){
            return $leadDto;
        }

        foreach ($lead->getCustomFieldsValues() as $field) {

            if ($field->getFieldId() === 879807) {
                $leadDto->numberCalling = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880453) {
                /** @var Carbon $dateTime */
                $leadDto->dateTime = $field->getValues()?->first()->getValue()?->toString();
            }
            if ($field->getFieldId() === 870903) {
                $leadDto->address = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 968865) {
                $leadDto->addressInfo = $field->getValues()?->first()->getValue();
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

            if ($field->getFieldId() === 968691) {
                $leadDto->partnerHospitalization = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 968867) {
                $leadDto->noBusinessCards = $field->getValues()?->first()->getValue();
            }
        }

        return $leadDto;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return is_array($data) && is_a($type, Lead::class, true);
    }
}