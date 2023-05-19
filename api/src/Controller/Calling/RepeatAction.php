<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\RepeatedCallScheduler;
use DateTimeImmutable;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class RepeatAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingSender $sender;
    private RepeatedCallScheduler $scheduler;

    public function __construct(
        AmoCRM        $amoCRM,
        CallingSender $sender,
        RepeatedCallScheduler $scheduler
    )
    {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
        $this->scheduler = $scheduler;
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     */
    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(45084664);
        }

        $this->client->leads()->update($leads);

        $calling->setComplete(new DateTimeImmutable());
        $flusher->flush();

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );

        $this->scheduler->schedule((int)$calling->getNumberCalling());

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling(),
            'Оформлен повтор'
        );

        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
