<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use App\Services\AmoCRM;
use DomainException;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/cel-lead', name: 'amo-crm_cel_lead', methods: ["POST"])]
class CelLeadAction extends AbstractController
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM                                       $amoCRM,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Request $request): JsonResponse
    {
        //todo: получить данные serializer в команду и возможно сразу определять нужное поле без повторного запроса
        $data = $request->request->all();

        try {
            //todo: вынести в handler
            $leadId = $this->getLeadId($data);
            $this->celLeadById($leadId);
        }catch (Exception $e) {
            return $this->json([
                'error' =>  $e->getMessage(),
            ], Response::HTTP_OK);
        }

        return $this->json(null, Response::HTTP_OK);
    }


    private function getLeadId($data)
    {
        if (
            !isset($data['leads']) ||
            !isset($data['leads']['status']) ||
            !isset($data['leads']['status'][0]) ||
            !isset($data['leads']['status'][0]['id'])
        ) {
            throw new InvalidArgumentException('CelLeadAction: Отсутствует необходимое поле для получения ID заявки');
        }

        return $data['leads']['status'][0]['id'];
    }


    private function celLeadById($leadId): void
    {

        if (!$leadId) {
            throw new DomainException('CelLeadAction: Не определен идентификатор заявки');
        }

        $filter = new LeadsFilter();
        $filter->setIds([$leadId]);

        try {
            $leads = $this->client->leads()->get($filter);
        } catch (Exception $e) {
            throw new DomainException('CelLeadAction: Ошибка получения заявки ID: ' . $leadId . ' ' . $e->getMessage());
        }

        $lead = $leads->first();

        $customerRequest = null;
        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 879807) {
                $customerRequest = $field->getValues()?->first()->getValue();
            }
        }

        if (!$customerRequest) {
            throw new DomainException('CelLeadAction: Ошибка определения запроса заявки ID: ' . $leadId);
        }

        $newStatus = null;

        //todo: статусы с константы
        switch ($customerRequest) {
            case 'Вывод из запоя':
                $newStatus = 38307946;
                break;
            case 'Стационар':
                $newStatus = 38709310;
                break;
            case 'Реабилитация':
                $newStatus = 38709331;
                break;
        }

        if (!$newStatus) {
            throw new DomainException(
                'CelLeadAction: Ошибка определения нового статуса заявки ID: ' .
                $leadId . ' запрос;' . $customerRequest
            );
        }

        $lead->setStatusId($newStatus);

        try {
            $this->client->leads()->update($leads);
        } catch (Exception $e) {
            throw new DomainException(
                'Ошибка записи статуса '.$newStatus. ' заявки ID: ' . $leadId . ' ' . $e->getMessage()
            );
        }
    }
}


