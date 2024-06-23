<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Models\LeadModel;
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
        $data = $request->request->all();

        try {
            //todo: вынести в handler
            $lead = $this->getLead($data);
            $this->celLeadById($lead);
        }catch (Exception $e) {
            return $this->json([
                'error' =>  $e->getMessage(),
            ], Response::HTTP_OK);
        }

        return $this->json(null, Response::HTTP_OK);
    }

    /**
     * @throws \AmoCRM\Exceptions\InvalidArgumentException
     */
    private function getLead($data): LeadModel
    {
        if (
            !isset($data['leads']) ||
            !isset($data['leads']['status']) ||
            !isset($data['leads']['status'][0])
        ) {
            throw new InvalidArgumentException('CelLeadAction: Отсутствуют необходимые данные для создания заявки');
        }

        return LeadModel::fromArray($data['leads']['status'][0]);
    }

    private function celLeadById(LeadModel $lead): void
    {
        $customerRequest = null;
        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 875587) {
                $customerRequest = $field->getValues()?->first()->getValue();
            }
        }

        if (!$customerRequest) {
            return;
        }

        $newStatus = null;

        //todo: статусы с константы
        switch ($customerRequest) {
            case 'Выезд на дом':
            case 'Психиатр на дом':
                $newStatus = 38307946;
                break;
            case 'Стационар НД':
            case 'Стационар Грай':
            case 'Стационар Партнёра':
                $newStatus = 38709310;
                break;
            case 'Реабилитация':
                $newStatus = 38709331;
                break;
        }

        if (!$newStatus) {
            return;
        }

        $lead->setStatusId($newStatus);

        $leads = new LeadsCollection();
        $leads->add($lead);

        try {
            $this->client->leads()->update($leads);
        } catch (Exception $e) {
            throw new DomainException(
                'Ошибка записи статуса '.$newStatus. ' заявки ID: ' . $lead->getId() . ' ' . $e->getMessage()
            );
        }
    }
}


